<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agents\Agents;
use App\Models\Clients\Clients;
use App\Models\Coursiers\Coursiers;
use App\Models\TypeUtilisateur\TypeUtilisateur;
use App\Models\Users\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $request = request();
        $users = Users::with(['type_utilisateur'])
            ->where('noms', 'like', '%'.$request->input('recherche').'%')
            ->orWhere('telephone', 'like', '%'.$request->input('recherche').'%')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
        session()->flash('search', $request->input('recherche'));

        return view('admin.pages.utilisateurs.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $typesUser = TypeUtilisateur::get()->reject(function ($typeUser) {
            return $this->estTypeSuperAdmin($typeUser->id) && ! $this->estTypeSuperAdmin(Auth()->user()->id_type_utilisateur);
        });

        return view('admin.pages.utilisateurs.create', compact('typesUser'));
    }

    /**
     * montrer la selection qui va choisir le compte lié au user
     *
     * @return Response
     */
    public function compteLie(Request $request)
    {
        $typeUser = TypeUtilisateur::findOrFail($request->input('type_user'));
        if ($request->input('type_user') == 2) {
            $compteLie = Agents::whereNull('id_utilisateur')->where('statut', [1])->get();
        } elseif ($request->input('type_user') == 3) {
            $compteLie = Coursiers::whereNull('id_utilisateur')->where('statut', [1])->get();
        } elseif ($request->input('type_user') == 4) {
            $compteLie = Clients::whereNull('id_utilisateur')->where('statut', [1])->get();
        } else {
            $compteLie = null;
        }

        return view('admin.pages.utilisateurs.compteLie', compact('typeUser', 'compteLie'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $noms = $request->input('noms');
        $email = $request->input('email');
        $password = $request->input('password');
        $telephone = str_replace(' ', '', $request->input('telephone'));
        $type_user = $request->input('type_user');
        $id_compte_associe = $request->input('id_compte_associe');
        $validated = $request->validate([
            'noms' => 'bail|required|max:255',
            'email' => 'bail|required|unique:users|max:255',
            'password' => 'bail|required|min:8',
            'type_user' => 'bail|required|integer|exists:type_utilisateur,id',
            'telephone' => 'bail|required|unique:users|regex:/^[6,2][0-9]{8}$/',
        ]);
        $this->protegerSuperAdmin($type_user);
        if ($id_compte_associe == null && $type_user != 1) {
            $validated = $request->validate([
                'id_compte_associe' => 'bail|required',
            ]);
        }
        $utilisateur = new Users;
        $utilisateur->noms = $noms;
        $utilisateur->email = $email;
        $utilisateur->password = Hash::make($password);
        $utilisateur->telephone = $telephone;
        $utilisateur->id_type_utilisateur = $type_user;
        if ($type_user == 2) {
            $utilisateur->id_agent = $id_compte_associe;
        } elseif ($type_user == 3) {
            $utilisateur->id_coursier = $id_compte_associe;
        } elseif ($type_user == 4) {
            $utilisateur->id_client = $id_compte_associe;
        }
        $utilisateur->save();
        if ($type_user == 2) {
            $compte = Agents::findOrFail($id_compte_associe);
            $compte->id_utilisateur = $utilisateur->id;
            $compte->save();
        } elseif ($type_user == 3) {
            $compte = Coursiers::findOrFail($id_compte_associe);
            $compte->id_utilisateur = $utilisateur->id;
            $compte->save();
        } elseif ($type_user == 4) {
            $compte = Clients::findOrFail($id_compte_associe);
            $compte->id_utilisateur = $utilisateur->id;
            $compte->save();
        }
        $message = "Utilisateur créé avec <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Request $request, $email)
    {
        $users = Users::with(['type_utilisateur', 'client_utilisateur', 'coursier_utilisateur', 'agent_utilisateur', 'commandes_enregistrees'])->where('email', $email)->get();
        foreach ($users as $user) {
        }

        return view('admin.pages.utilisateurs.info', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($email)
    {
        $users = Users::where('email', $email)->get();
        foreach ($users as $user) {
        }

        return view('admin.pages.utilisateurs.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $noms = $request->input('noms');
        $email = $request->input('email');
        $telephone = str_replace(' ', '', $request->input('telephone'));
        $validated = $request->validate([
            'noms' => 'bail|required|max:255',
            'email' => 'bail|required|max:255',
            'telephone' => 'bail|required|regex:/^[6,2][0-9]{8}$/',
        ]);
        if (Users::where('email', $email)->whereNotIn('id', [$id])->count() > 0) {
            $validated = $request->validate([
                'email' => 'unique:users',
            ]);
        }
        if (Users::where('telephone', $telephone)->whereNotIn('id', [$id])->count() > 0) {
            $validated = $request->validate([
                'telephone' => 'unique:users',
            ]);
        }
        $utilisateur = Users::findOrFail($id);
        $this->protegerSuperAdmin($utilisateur->id_type_utilisateur);
        $utilisateur->noms = $noms;
        $utilisateur->email = $email;
        $utilisateur->telephone = $telephone;
        if ($utilisateur->id_agent != null) {
            $agent = Agents::findOrFail($utilisateur->id_agent);
            $agent->telephone = $telephone;
            $agent->save();
        } elseif ($utilisateur->id_coursier != null) {
            $coursier = Coursiers::findOrFail($utilisateur->id_coursier);
            $coursier->telephone = $telephone;
            $coursier->save();
        } elseif ($utilisateur->id_client != null) {
            $client = Clients::findOrFail($utilisateur->id_client);
            $client->telephone = $telephone;
            $client->save();
        }
        $utilisateur->save();
        $message = "Utilisateur mis à jour avec <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $user = Users::findOrFail($id);
        $this->protegerSuperAdmin($user->id_type_utilisateur);
        if ($user->statut == 0) {
            $user->statut = 1;
            $message = e($user->noms)." Activé(e) avec <b class='text-success'> Succès.</b>";
            if ($user->id_agent != null) {
                $agent = Agents::findOrFail($user->id_agent);
                $agent->statut = 1;
                $agent->save();
            } elseif ($user->id_coursier != null) {
                $coursier = Coursiers::findOrFail($user->id_coursier);
                $coursier->statut = 1;
                $coursier->save();
            } elseif ($user->id_client != null) {
                $client = Clients::findOrFail($user->id_client);
                $client->statut = 1;
                $client->save();
            }
        } else {
            $user->statut = 0;
            $message = e($user->noms)." Desactivé(e) avec <b class='text-success'>Succès.</b>";
            if ($user->id_agent != null) {
                $agent = Agents::findOrFail($user->id_agent);
                $agent->statut = 0;
                $agent->save();
            } elseif ($user->id_coursier != null) {
                $coursier = Coursiers::findOrFail($user->id_coursier);
                $coursier->statut = 0;
                $coursier->save();
            } elseif ($user->id_client != null) {
                $client = Clients::findOrFail($user->id_client);
                $client->statut = 0;
                $client->save();
            }
        }
        $user->save();
        session()->flash('message', $message);

        return redirect()->back();
    }
}
