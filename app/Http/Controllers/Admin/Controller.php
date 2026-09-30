<?php

namespace App\Http\Controllers\Admin;

use App\Models\TypeUtilisateur\TypeUtilisateur;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Le type d'utilisateur donné est-il « Super Admin » ?
     */
    protected function estTypeSuperAdmin($id_type_utilisateur)
    {
        $type = TypeUtilisateur::find($id_type_utilisateur);

        return $type !== null && strtoupper($type->libelle) == strtoupper('Super Admin');
    }

    /**
     * Seul un Super Admin peut créer, modifier, désactiver ou réinitialiser
     * un compte Super Admin : un agent ne doit pas pouvoir s'élever.
     */
    protected function protegerSuperAdmin($id_type_utilisateur_cible)
    {
        abort_if(
            $this->estTypeSuperAdmin($id_type_utilisateur_cible)
                && ! $this->estTypeSuperAdmin(Auth::user()->id_type_utilisateur),
            403
        );
    }
}
