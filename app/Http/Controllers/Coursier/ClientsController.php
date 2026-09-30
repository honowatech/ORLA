<?php

namespace App\Http\Controllers\Coursier;

use App\Models\Clients\Clients;
use App\Models\Commandes\Commandes;
use App\Models\Quartier\Quartier;

class ClientsController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        // Un coursier ne voit que les clients de ses propres commandes.
        $id_coursier = Auth()->user()->id_coursier;
        abort_if($id_coursier === null, 403);
        $client = Clients::whereHas('commandes', function ($query) use ($id_coursier) {
            $query->where('id_coursier', $id_coursier);
        })->findOrFail($id);
        $quartiers = Quartier::get();

        return view('coursier.pages.clients.info', compact('client', 'quartiers'));
    }
}
