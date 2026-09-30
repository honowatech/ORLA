<?php

namespace App\Http\Controllers\Admin;

use App\Models\boutiques\Boutiques;
use Illuminate\Http\Request;

class BoutiquesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $libelle = $request->input('libelle');
        $id_quartier = $request->input('id_quartier');
        $id_client = $request->input('id_client');
        $localisation = $request->input('localisation');
        session()->flash('erreur', 'erreur');
        $validated = $request->validate([
            'libelle' => 'bail|required|unique:boutiques|min:4|max:25',
            'id_quartier' => 'bail|required|numeric|min:1',
            'id_client' => 'bail|required|numeric|min:1',
            'localisation' => 'bail|required|max:255|min:10',
        ]);
        session()->forget('erreur');
        $boutique = new Boutiques;
        $boutique->libelle = $libelle;
        $boutique->id_quartier = $id_quartier;
        $boutique->id_client = $id_client;
        $boutique->quartier = $localisation;
        $boutique->statut = 1;
        $boutique->save();
        $message = "Boutique crée avec <b class='text-success'>Succès.</b>";
        session()->flash('message', $message);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $boutique = Boutiques::findOrFail($id);
        if ($boutique->statut == 0) {
            $boutique->statut = 1;
            $message = 'Boutique "'.e($boutique->libelle).'" Activée avec <b class="text-success"> Succès.</b>';
        } else {
            $boutique->statut = 0;
            $message = 'Boutique "'.e($boutique->libelle).'" Desactivée avec <b class="text-success"> Succès.</b>';
        }
        $boutique->save();
        session()->flash('message', $message);

        return redirect()->back();
    }
}
