<?php

namespace App\Http\Controllers\Client;

use App\Models\commandes\Commandes;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Commande appartenant au client connecté.
     * Renvoie une 404 si la commande n'existe pas ou appartient à un autre compte.
     */
    protected function commandeDuClient($id)
    {
        $id_client = Auth::user()->id_client;
        abort_if($id_client === null, 403);

        return Commandes::where('id_client', $id_client)->findOrFail($id);
    }
}
