<?php

namespace App\Http\Controllers\Coursier;

use App\Http\Controllers\Concerns\GereSonProfil;
use App\Models\Activity;
use App\Models\Coursiers\Coursiers;
use App\Models\Users\Users;
use Illuminate\Support\Facades\Auth;

/**
 * Profil du coursier connecté, et disponibilité de ses commandes (destroy).
 */
class UsersController extends Controller
{
    use GereSonProfil;

    protected function espace(): string
    {
        return 'coursier';
    }

    protected function synchroniserFiche(Users $utilisateur, string $noms, string $prenoms, string $telephone): void
    {
        if ($utilisateur->id_coursier != null) {
            $coursier = Coursiers::findOrFail($utilisateur->id_coursier);
            $coursier->noms = $noms;
            $coursier->prenoms = $prenoms;
            $coursier->telephone = $telephone;
            $coursier->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        $commande = $this->commandeDuCoursier($id);

        $activity = new Activity;
        $activity->lien = route('Coursiercommandes.show', $id);
        $activity->texte_lien = 'Voir la commande';
        $activity->jour = now();
        $activity->heure = now();
        $activity->id_user = Auth::user()->id;
        // dd($commande);
        if ($commande->disponibility == 1) {
            $activity->title = 'Commande Indisponible';
            $activity->color = 'danger';
            $activity->message = 'commande rendue Indisponible avec  <b class="text-success">succès</b>';
            $commande->disponibility = 0;
            $message = "Commande rendue Indisponible avec <b class='text-success'> Succès. </b>";
        } else {
            $activity->title = 'Commande Disponible';
            $activity->color = 'success';
            $activity->message = 'commande rendue Disponible avec  <b class="text-success">succès</b>';
            $commande->disponibility = 1;
            $message = "Commande rendue Disponible avec <b class='text-success'> Succès.</b>";
        }
        $activity->save();
        $commande->save();
        session()->flash('message', $message);

        return redirect()->back();
    }
}
