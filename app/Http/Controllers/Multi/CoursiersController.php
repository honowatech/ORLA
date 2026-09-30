<?php

namespace App\Http\Controllers\Multi;

use App\Models\Coursiers\Coursiers;
use App\Models\Informations_personnels\Informations_personnels;
use App\Models\Users\Users;
use App\Models\Vehicule;
use App\Models\Ville\Ville;
use App\Models\Zone\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CoursiersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $statut = [];
        $lower_search = strtolower(trim($request->input('search')));
        if (str_contains('actif', $lower_search)) {
            array_push($statut, 1);
        }
        if (str_contains('désactivé', $lower_search)) {
            array_push($statut, 0);
        }
        $villes = Ville::where('libelle', 'like', '%'.$request->input('search').'%')->get();
        $users = Users::WhereIn('id_ville', $villes->pluck('id'))->whereNotNull('id_coursier')->get();
        $coursiers = Coursiers::where('noms', 'like', '%'.$request->input('search').'%')
            ->orWhere('prenoms', 'like', '%'.$request->input('search').'%')
            ->orWhereIn('id_utilisateur', $users->pluck('id'))
            ->orWhereIn('statut', $statut)
            ->orWhere('telephone', 'like', '%'.$request->input('search').'%')
            ->orWhere('prenoms', 'like', '%'.$request->input('search').'%')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
        foreach ($coursiers as $coursier) {
            $info_perso = Informations_personnels::where('id_coursier', [$coursier->id])->count();
            if ($info_perso == 0) {
                $informations_personnel = new Informations_personnels;
                $informations_personnel->id_coursier = $coursier->id;
                $informations_personnel->save();
            }
        }
        session()->flash('search', $request->input('search'));

        return view('multi.pages.coursiers.index', compact('coursiers'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $villes = Ville::get();

        return view('multi.pages.coursiers.create', compact('villes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $noms = $request->input('noms');
        $prenoms = $request->input('prenoms');
        $add_user_account = $request->input('add_user_account');
        $email = $request->input('email');
        $password = $request->input('password');
        $id_ville = $request->input('id_ville');
        $telephone = str_replace(' ', '', $request->input('telephone'));

        $validated = $request->validate([
            'noms' => 'bail|required|max:255',
            'prenoms' => 'bail|required|max:255',
            'telephone' => 'bail|required|unique:coursiers|unique:users|regex:/^[6,2][0-9]{8}$/',
            'email' => 'bail|required|unique:users|max:255',
            'password' => 'bail|required|min:8',
        ]);

        $utilisateur = new Users;
        $utilisateur->noms = $noms.' '.$prenoms;
        $utilisateur->email = $email;
        $utilisateur->password = Hash::make($password);
        $utilisateur->telephone = $telephone;
        $utilisateur->id_ville = $id_ville;
        $utilisateur->statut = 1;
        $utilisateur->id_type_utilisateur = 3;
        $utilisateur->save();
        $coursier = new Coursiers;
        $coursier->noms = $noms;
        $coursier->prenoms = $prenoms;
        $coursier->telephone = $telephone;
        $coursier->statut = 1;
        $coursier->save();
        $coursier->id_utilisateur = $utilisateur->id;
        $coursier->save();
        $utilisateur->id_coursier = $coursier->id;
        $utilisateur->save();
        $informations_personnel = new Informations_personnels;
        $informations_personnel->id_coursier = $coursier->id;
        $informations_personnel->save();
        $message = "Livreur et utilisateur lié créés avec <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->route('coursiers.show', $coursier->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $coursier = Coursiers::findOrFail($id);
        $zones = Zone::whereIn('statut', [1])->where('id_ville', $coursier->user->id_ville)->get();
        $vehicules = Vehicule::whereIn('statut', [1])->whereNull('id_coursier')->where('id_ville', $coursier->user->id_ville)->get();
        if ((filter(['routeur', 'superviseur_ville'], auth()->user()) == 'true' && $coursier->coursier_utilisateur->id_ville != auth()->user()->id_ville)) {
            return redirect()->route('coursiers.index');
        }

        return view('multi.pages.coursiers.info', compact('coursier', 'zones', 'vehicules'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $coursier = Coursiers::findOrFail($id);
        $villes = Ville::orderBy('updated_at', 'desc')->get();

        return view('multi.pages.coursiers.edit', compact('coursier', 'villes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $vehicules = $request->input('vehicules');
        $attribuate = $request->input('attribuate');
        $noms_vehicules = '';
        if ($attribuate != null) {
            $validated = $request->validate([
                'vehicules' => 'bail|required|array',
            ]);
            foreach ($vehicules as $id_vehicule) {
                $vehicule = Vehicule::findOrFail($id_vehicule);
                $vehicule->id_coursier = $id;
                $vehicule->save();
                $noms_vehicules .= $vehicule->modele.' '.$vehicule->marque.', ';
            }
            $message = 'vehicules '.e($noms_vehicules).' attribués avec Succès.</b>';
            session()->flash('message', $message);

            return redirect()->back();
        }
        $noms = $request->input('noms');
        $telephone = str_replace(' ', '', $request->input('telephone'));
        $prenoms = $request->input('prenoms');
        $validated = $request->validate([
            'noms' => 'bail|required|max:255',
            'prenoms' => 'bail|required|max:255',
            'telephone' => 'bail|required|regex:/^[6,2][0-9]{8}$/',
        ]);
        if (Coursiers::where('telephone', $telephone)->whereNotIn('id', [$id])->count() > 0) {
            $validated = $request->validate([
                'telephone' => 'unique:coursiers',
            ]);
        }
        $coursier = Coursiers::findOrFail($id);
        if ((filter(['routeur', 'superviseur_ville'], auth()->user()) == 'true' && $coursier->coursier_utilisateur->id_ville != auth()->user()->id_ville)) {
            return redirect()->route('coursiers.index');
        }
        $coursier->noms = $noms;
        $coursier->prenoms = $prenoms;
        $coursier->telephone = $telephone;
        $coursier->save();
        if ($coursier->id_utilisateur != null) {
            $utilisateur = Users::findOrFail($coursier->id_utilisateur);
            $utilisateur->noms = $noms.' '.$prenoms;
            $utilisateur->telephone = $telephone;
            $utilisateur->save();
        }
        $message = 'Coursier(e) mis(e) à jour avec Succès.</b>';
        session()->flash('message', $message);

        return redirect()->route('coursiers.show', $coursier->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $coursier = Coursiers::findOrFail($id);
        if ($coursier->statut == 0) {
            $coursier->statut = 1;
            if ($coursier->id_utilisateur != null) {
                $utilisateur = Users::findOrFail($coursier->id_utilisateur);
                $utilisateur->statut = 1;
                $utilisateur->save();
            }
            $message = e($coursier->noms).' '.e($coursier->prenoms)." Activé(e) avec <b class='text-success'> Succès.</b>";
        } else {
            $coursier->statut = 0;
            if ($coursier->id_utilisateur != null) {
                $utilisateur = Users::findOrFail($coursier->id_utilisateur);
                $utilisateur->statut = 0;
                $utilisateur->save();
            }
            $message = e($coursier->noms).' '.e($coursier->prenoms)." Desactivé(e) avec <b class='text-success'> Succès.</b>";
        }
        $coursier->save();
        session()->flash('message', $message);

        return redirect()->back();
    }
}
