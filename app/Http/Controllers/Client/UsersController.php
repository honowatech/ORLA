<?php

namespace App\Http\Controllers\Client;

use App\Models\Clients\Clients;
use App\Models\Users\Users;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('errors.404');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('errors.404');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return view('errors.404');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show(Request $request, $id)
    {
        $user = Users::findOrFail(Auth()->user()->id);

        return view('client.pages.user.info', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id)
    {
        $user = Users::findOrFail(Auth()->user()->id);

        return view('client.pages.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(Request $request, $id)
    {
        // Seul le profil du compte connecté peut être modifié.
        $id = Auth()->user()->id;
        $noms = $request->input('noms');
        $prenoms = $request->input('prenoms');
        $email = $request->input('email');
        $telephone = str_replace(' ', '', $request->input('telephone'));
        $validated = $request->validate([
            'noms' => 'bail|required|max:255',
            'prenoms' => 'bail|required|max:255',
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
        $utilisateur->noms = $noms.' '.$prenoms;
        $utilisateur->email = $email;
        $utilisateur->telephone = $telephone;
        // La fiche client liée suit les informations du compte.
        if ($utilisateur->id_client != null) {
            $client = Clients::findOrFail($utilisateur->id_client);
            $client->noms = $noms;
            $client->Prenoms = $prenoms;
            $client->telephone = $telephone;
            $client->save();
        }
        $utilisateur->save();
        $message = "Informations Enregistrées avec <b class='text-success'> Succès. </b>";
        session()->flash('message', $message);

        return redirect()->route('Clientusers.show', $utilisateur->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        $commande = $this->commandeDuClient($id);
        // dd($commande);
        if ($commande->disponibility == 1) {
            $commande->disponibility = 0;
            $message = "Commande rendue Indisponible avec <b class='text-success'> Succès. </b>";
        } else {
            $commande->disponibility = 1;
            $message = "Commande rendue Disponible avec <b class='text-success'> Succès.</b>";
        }
        $commande->save();
        session()->flash('message', $message);

        return redirect()->back();
    }
}
