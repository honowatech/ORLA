<?php

namespace App\Http\Controllers\Multi;

use App\Models\Coursiers\Coursiers;
use App\Models\Informations_personnels\Informations_personnels;
use App\Models\Ville\Ville;
use Illuminate\Http\Request;

class Informations_personnelsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $id = $request->input('id');
        $type = $request->input('type');
        $telephone2 = $request->input('telephone2');
        $date_naissance = $request->input('date_naissance');
        $lieu_naissance = $request->input('lieu_naissance');
        $cni = $request->input('cni');
        $date_delivrance = $request->input('date_delivrance');
        $date_expiration = $request->input('date_expiration');
        $lieu_delivrance = $request->input('lieu_delivrance');
        $quartier = $request->input('id_quartier_associe');
        $localisation = $request->input('localisation');
        session()->flash('infos_perso_error', '1');
        if ($telephone2 != null) {
            $validated = $request->validate([
                'telephone2' => 'bail|numeric|regex:/^[6,2][0-9]{8}$/',
            ]);
            if (Informations_personnels::where('telephone2', $telephone2)->whereNotIn('id_'.$type, [$id])->count() > 0) {
                $validated = $request->validate([
                    'telephone2' => 'bail|numeric|unique:informations_personnels',
                ]);
            }
        }
        $request->validate([
            'id' => 'bail|required',
            'date_naissance' => 'bail|required|date',
            'lieu_naissance' => 'bail|required|min:4',
            'lieu_delivrance' => 'bail|required|min:4',
            'cni' => 'bail|required|min:7|max:25',
            'date_delivrance' => 'bail|required|date|before:date_expiration',
            'date_expiration' => 'bail|required|date|after:date_delivrance',
            'id_quartier_associe' => 'bail|numeric|gt:0',
        ]);
        if (Informations_personnels::where('cni', $cni)->whereNotIn('id_'.$type, [$id])->count() > 0) {
            $validated = $request->validate([
                'cni' => 'unique:informations_personnels',
            ]);
        }
        session()->forget('infos_perso_error', '1');
        $info_perso = Informations_personnels::where('id_'.$type, [$id])
            ->get('id')->value('id');
        $informations_personnel = Informations_personnels::findOrFail($info_perso);
        $informations_personnel->telephone2 = $telephone2;
        $informations_personnel->date_naissance = $date_naissance;
        $informations_personnel->lieu_naissance = $lieu_naissance;
        $informations_personnel->cni = $cni;
        $informations_personnel->date_delivrance = $date_delivrance;
        $informations_personnel->lieu_delivrance = $lieu_delivrance;
        $informations_personnel->date_expiration = $date_expiration;
        $informations_personnel->id_quartier = $quartier;
        $informations_personnel->localisation = $localisation;
        $informations_personnel->save();
        $message = "Informations personnels mis à jour avec <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->route($type.'s.show', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($infos)
    {
        $table_info = explode('-', $infos);
        session()->flash('infos_perso', '1');
        $villes = Ville::orderBy('updated_at', 'desc')->get();
        if ($table_info[1] == 0) {
            session()->flash('message', "L'authentification des agents n'est pas encore disponible.");

            return redirect()->back();
        }
        if ($table_info[1] == 1) {
            session()->flash('message', "L'authentification des clients n'est pas encore disponible.");

            return redirect()->back();
        }
        if ($table_info[1] == 2) {
            $coursier = Coursiers::findOrFail($table_info[0]);

            return view('multi.pages.coursiers.edit', compact('coursier', 'villes'));
        }
    }
}
