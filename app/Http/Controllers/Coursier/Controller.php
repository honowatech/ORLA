<?php

namespace App\Http\Controllers\Coursier;

use App\Models\Commandes\Commandes;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Commande attribuée au coursier connecté.
     * Renvoie une 404 si la commande n'existe pas ou appartient à un autre compte.
     */
    protected function commandeDuCoursier($id)
    {
        $id_coursier = Auth::user()->id_coursier;
        abort_if($id_coursier === null, 403);

        return Commandes::where('id_coursier', $id_coursier)->findOrFail($id);
    }
}
