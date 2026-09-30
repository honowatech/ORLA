<?php

namespace App\Http\Controllers\Multi;

use App\Models\details_zone\Details_zone;
use Illuminate\Http\Request;

class Details_zoneController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $zones = $request->input('zones');
        $id_coursier = $request->input('id_coursier');

        $message = "<b class='text-danger'>Echec de la requete! </b>, Informations éronnés ";
        session()->flash('message', $message);
        $validated = $request->validate([
            'id_coursier' => 'numeric',
            'zones' => 'min:1',
        ]);
        foreach ($zones as $id_zone) {
            $details = new Details_zone;
            $details->id_coursier = $id_coursier;
            $details->id_zone = $id_zone;
            $details->save();
        }
        $message = "Zones attribuées avec <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $id_zone = $request->input('id_zone');
        $id_coursier = $request->input('id_coursier');
        $validated = $request->validate([
            'id_zone' => 'numeric',
        ]);

        $details = Details_zone::where('id_coursier', [$id])->where('id_zone', [$id_zone])->get();
        foreach ($details as $detail) {
            $detail->delete();
        }
        $message = "Zone désattribuée avec <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->back();
    }
}
