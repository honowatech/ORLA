<?php

namespace App\Http\Controllers\Multi;

use App\Models\coursiers\Coursiers;
use App\Models\Type_vehicule;
use App\Models\Vehicule;
use App\Models\ville\Ville;
use Illuminate\Http\Request;

class VehiculeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $user = Auth()->user();
        $statut = [];
        $lower_search = strtolower(trim($request->input('recherche')));
        if (str_contains('actif', $lower_search)) {
            array_push($statut, 1);
        }
        if (str_contains('désactivé', $lower_search)) {
            array_push($statut, 0);
        }
        $types = Type_vehicule::where('libelle', 'like', '%'.$request->input('recherche').'%')
            ->get();
        $villes = Ville::where('libelle', 'like', '%'.$request->input('recherche').'%')
            ->get();
        $coursiers = Coursiers::where('noms', 'like', '%'.$request->input('recherche').'%')
            ->orWhere('prenoms', 'like', '%'.$request->input('recherche').'%')
            ->get();
        $vehicules = Vehicule::where('immatriculation', 'like', '%'.$request->input('recherche').'%')
            ->orWhereIn('id_coursier', $coursiers->pluck('id'))
            ->orWhereIn('id_type', $types->pluck('id'))
            ->orWhereIn('id_ville', $villes->pluck('id'))
            ->orWhereIn('statut', $statut)
            ->orWhere('couleur', 'like', '%'.$request->input('recherche').'%')
            ->orWhere('marque', 'like', '%'.$request->input('recherche').'%')
            ->orWhere('modele', 'like', '%'.$request->input('recherche').'%')
            ->orWhere('description', 'like', '%'.$request->input('recherche').'%')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
        $request = request();
        session()->flash('search', $request->input('recherche'));

        return view('multi.pages.vehicule.index', compact('vehicules'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $user = Auth()->user();
        $villes = Ville::get();
        $types_vehicule = Type_vehicule::where('statut', [1])->get();

        return view('multi.pages.vehicule.create', compact('villes', 'types_vehicule'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $user = Auth()->user();
        $immatriculation = $request->input('immatriculation');
        $couleur = $request->input('couleur');
        $id_type = $request->input('id_type');
        $marque = $request->input('marque');
        $modele = $request->input('modele');
        $description = $request->input('description');
        $id_ville = $request->input('id_ville');
        $request->validate([
            'immatriculation' => 'bail|required|min:4|max:255|unique:vehicule',
            'id_type' => 'bail|required|numeric|min:1',
            'marque' => 'bail|required|min:4|max:255',
            'modele' => 'bail|required|min:2|max:255',
            'id_ville' => 'bail|required|numeric|min:1|',
        ]);
        if ($description != null) {
            $request->validate([
                'description' => 'bail|required|min:2',
            ]);
        }
        if ($couleur != null) {
            $request->validate([
                'couleur' => 'bail|required|min:2|max:255|',
            ]);
        }
        // dd($immatriculation,$couleur,$id_type,$marque,$modele,$description,$id_ville);
        $vehicule = new Vehicule;
        $vehicule->immatriculation = $immatriculation;
        $vehicule->couleur = $couleur;
        $vehicule->id_type = $id_type;
        $vehicule->marque = $marque;
        $vehicule->modele = $modele;
        $vehicule->description = $description;
        $vehicule->id_ville = $id_ville;
        $vehicule->save();
        $message = "Véhicule enregistré avec <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->route('vehicule.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $user = Auth()->user();

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $user = Auth()->user();
        $vehicule = Vehicule::findOrFail($id);
        $villes = Ville::get();
        $types_vehicule = Type_vehicule::where('statut', [1])->get();

        return view('multi.pages.vehicule.edit', compact('vehicule', 'villes', 'types_vehicule'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth()->user();
        $immatriculation = $request->input('immatriculation');
        $couleur = $request->input('couleur');
        $id_type = $request->input('id_type');
        $marque = $request->input('marque');
        $modele = $request->input('modele');
        $description = $request->input('description');
        $id_ville = $request->input('id_ville');
        $request->validate([
            'immatriculation' => 'bail|required|min:4|max:255',
            'id_type' => 'bail|required|numeric|min:1',
            'marque' => 'bail|required|min:4|max:255',
            'modele' => 'bail|required|min:2|max:255',
            'id_ville' => 'bail|required|numeric|min:1|',
        ]);
        if (Vehicule::where('immatriculation', $immatriculation)->whereNotIn('id', [$id])->count() > 0) {
            $validated = $request->validate([
                'immatriculation' => 'unique:vehicule',
            ]);
        }
        if ($description != null) {
            $request->validate([
                'description' => 'bail|required|min:2',
            ]);
        }
        if ($couleur != null) {
            $request->validate([
                'couleur' => 'bail|required|min:2|max:255|',
            ]);
        }
        // dd($immatriculation,$couleur,$id_type,$marque,$modele,$description,$id_ville);
        $vehicule = Vehicule::findOrFail($id);
        if (filter(['routeur', 'superviseur_ville'], $user) == 'true' && $vehicule->id_ville != $user->id_ville) {
            $message = "<b class='text-danger'> Erreur.</b> <br> Ce véhicule n'appartient pas à votre ville";
            session()->flash('message', $message);

            return redirect()->route('vehicule.index');
        }
        $vehicule->immatriculation = $immatriculation;
        $vehicule->couleur = $couleur;
        $vehicule->id_type = $id_type;
        $vehicule->marque = $marque;
        $vehicule->modele = $modele;
        $vehicule->description = $description;
        $vehicule->id_ville = $id_ville;
        $vehicule->save();
        $message = "Véhicule mis à jour avec <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->route('vehicule.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth()->user();
        $vehicule = Vehicule::findOrFail($id);
        $nom_vehicule = $vehicule->modele.' '.$vehicule->marque.', ';
        $attribuate = $request->input('attribuate');
        if ($attribuate != null) {
            $vehicule->id_coursier = null;
            $vehicule->save();
            $message = 'Véhicule  '.e($nom_vehicule)." desattribué  avec <b class='text-success'> Succès.</b>";
            session()->flash('message', $message);

            return redirect()->back();
        }
        $type = $request->input('type');
        if ($type == 'delete') {
            $vehicule->delete();
            $message = 'Véhicule  '.e($nom_vehicule)." supprimée <b class='text-success'> Succès.</b>";
            session()->flash('message', $message);

            return redirect()->back();
        } else {
            if ($vehicule->statut == 0) {
                $vehicule->statut = 1;
                $vehicule->save();
                $message = 'vehicule '.e($nom_vehicule).' Activé avec Succès.</b>';
                session()->flash('message', $message);

                return redirect()->back();
                // code...
            } else {
                $vehicule->statut = 0;
                $vehicule->save();
                $message = 'Véhicule  '.e($nom_vehicule)." desactivé avec <b class='text-success'> Succès.</b>";
                session()->flash('message', $message);

                return redirect()->back();
            }
        }
    }
}
