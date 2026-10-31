<?php

namespace App\Http\Controllers\Multi\Concerns;

use App\Models\Vehicule;

/**
 * Contrôle d'accès commun aux écrans de gestion de parc :
 * - admin : accès à tous les véhicules ;
 * - routeur / superviseur de ville : uniquement les véhicules de sa ville.
 */
trait ParcAccess
{
    /** Retourne une redirection si l'utilisateur n'a pas accès au parc, sinon null. */
    protected function accesParc()
    {
        $filter = filter(['admin', 'routeur', 'superviseur_ville'], auth()->user());
        return $filter == 'true' ? null : redirect()->route($filter);
    }

    /** Identifiant de la ville imposée à l'utilisateur, ou null s'il voit tout. */
    protected function villeRestreinte()
    {
        $user = auth()->user();
        return filter(['routeur', 'superviseur_ville'], $user) == 'true' ? $user->id_ville : null;
    }

    /** Requête de base sur les véhicules visibles par l'utilisateur. */
    protected function vehiculesAccessibles()
    {
        $query = Vehicule::query();
        $ville = $this->villeRestreinte();
        if ($ville !== null) {
            $query->where('id_ville', $ville);
        }
        return $query;
    }

    /** Charge un véhicule ou échoue (404) s'il n'est pas visible par l'utilisateur. */
    protected function vehiculeAccessible($id)
    {
        return $this->vehiculesAccessibles()->findOrFail($id);
    }

    /** Identifiants des véhicules visibles (pour filtrer les tables liées). */
    protected function idsVehiculesAccessibles()
    {
        return $this->vehiculesAccessibles()->pluck('id');
    }

    /**
     * Redirige vers l'URL "retour" fournie par le formulaire si elle appartient
     * à l'application, sinon vers l'URL par défaut.
     */
    protected function redirectionRetour($request, $defaut)
    {
        $retour = (string) $request->input('retour');
        if ($retour !== '' && str_starts_with($retour, url('/'))) {
            return redirect($retour);
        }
        return redirect($defaut);
    }
}
