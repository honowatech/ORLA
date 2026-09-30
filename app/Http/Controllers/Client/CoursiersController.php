<?php

namespace App\Http\Controllers\Client;

use App\Models\Coursiers\Coursiers;
use App\Models\Details_zone\Details_zone;
use App\Models\Zone\Zone;

class CoursiersController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        $zones = [];
        $toutes_zones = Zone::where('statut', [1])->get();
        foreach ($toutes_zones as $zone) {
            if (Details_zone::where('id_zone', $zone->id)->where('id_coursier', $id)->count() == 0) {
                array_push($zones, $zone);
            }
        }
        $coursier = Coursiers::findOrFail($id);

        return view('client.pages.coursier.info', compact('coursier', 'zones'));
    }
}
