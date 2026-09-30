<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Concerns\GereSonProfil;
use App\Models\Clients\Clients;
use App\Models\Users\Users;

/**
 * Profil du client connecté, et disponibilité de ses commandes (destroy).
 */
class UsersController extends Controller
{
    use GereSonProfil;

    protected function espace(): string
    {
        return 'client';
    }

    protected function synchroniserFiche(Users $utilisateur, string $noms, string $prenoms, string $telephone): void
    {
        if ($utilisateur->id_client != null) {
            $client = Clients::findOrFail($utilisateur->id_client);
            $client->noms = $noms;
            $client->Prenoms = $prenoms;
            $client->telephone = $telephone;
            $client->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        $commande = $this->commandeDuClient($id);
        // dd($commande);
        if ($commande->disponibility == 1) {
            $commande->disponibility = 0;
            $message = "Commande rendue Indisponible avec <b class='text-success'> Succès. </b>";
        } else {
            $commande->disponibility = 1;
            $message = "Commande rendue Disponible avec <b class='text-success'> Succès.</b>";
        }
        $commande->save();
        session()->flash('message', $message);

        return redirect()->back();
    }
}
