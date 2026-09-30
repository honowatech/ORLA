<?php

namespace App\Http\Controllers\Coursier;

use App\Models\Activity;
use App\Models\Commandes\Commandes;
use App\Models\Coursiers\Coursiers;
use App\Services\Commandes\ChangerStatutCommande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommandesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        return view('errors.404');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        $commande = Commandes::findOrFail($id);
        if ($commande->id_coursier != Auth::user()->coursier_utilisateur->id) {
            return view('errors.404');
        }
        $contenu = [
            'attente' => 'primary/clock/En attente',
            'attribue' => 'info/user-check/Attribuée',
            'encours' => 'dark/loader/En cours',
            'livre' => 'success/check/Livrée',
            'annulee' => 'danger/alert-triangle/Annulée',
            'echoue' => 'warning/slash/Echouée',
        ];
        $ids = [];
        $statuts = ['attente', 'attribue', 'encours', 'livre', 'annulee', 'echoue'];
        $coursiers = Coursiers::where('statut', [1])->get();

        return view('coursier.pages.commandes.info', compact('commande', 'contenu', 'statuts', 'coursiers'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id)
    {
        return view('errors.404');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(Request $request, $id)
    {
        return view('errors.404');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(Request $request, $id, ChangerStatutCommande $changerStatut)
    {
        // ici nous déclarons les variables communes aux deux formulaires
        $statut = $request->input('statut');
        $id_coursier = $request->input('id_coursier');

        $reponses = [
            'encours' => 'Livraison En cours',
            'livre' => 'Livraison terminée',
            'annulee' => 'Livraison annulée',
            'echoue' => 'Livraison échouée',
        ];
        $reponse = $reponses[$statut] ?? 'Changement de statut';

        // on cherche la commande concernée
        $commande = $this->commandeDuCoursier($id);
        $message = "<b class='text-danger text-center'>Echec ! </br> Ce statut n'est pas correct.</b>";
        // ici on vérifie si le statut existe et est bel et bien une chaine de caractère
        session()->flash('message', $message);
        $validated = $request->validate([
            'statut' => 'bail|required|alpha',
        ]);
        session()->forget('message');
        // ici on vérifie si le coursier existe et est bel et bien un nombre positif supérieur à 0
        if ($id_coursier != null) {
            // on vérifie si la commande qui veut être attribuée est bel et bien en attente. si oui on insère l'id du coursier sinon on retourne un message d'erreur
            if ($commande->statut == 'attente') {
                session()->flash('id_commande', $id);
                $validated = $request->validate([
                    'id_coursier' => 'bail|required|numeric|min:1',
                ]);
                $commande->id_coursier = $id_coursier;
            } else {
                $message = "<b class='text-danger text-center'>Echec ! </br> Mauvaise pratique.</b>";
                session()->flash('message', $message);

                return redirect()->back();
            }
        }
        $activity = new Activity;
        $activity->lien = route('Coursiercommandes.show', $id);
        $activity->texte_lien = 'Voir la commande';
        $activity->jour = now();
        $activity->heure = now();
        $activity->id_user = Auth::user()->id;
        if ($changerStatut($commande, (string) $statut)) {
            if ($statut == 'encours') {
                $activity->color = 'dark';
                $activity->message = 'Livraison mise en cours avec <b class="text-success">succès</b>';
            } elseif ($statut == 'livre') {
                $activity->message = $reponse.' avec <b class="text-success">succès</b>';
                $activity->color = 'success';
            } else {
                $activity->message = $reponse.' avec <b class="text-success">succès</b>';
                $activity->color = 'danger';
            }

            $activity->title = $reponse;
            $message = "<b class='text-success text-center'>Statut de la commande modifié avec succès.</b>";
            session()->flash('message', $message);
            $activity->save();

            return redirect()->back();
        } else {
            $activity->title = "Echec de l'action";
            $activity->color = 'danger';
            $activity->message = 'Le changement de statut a échoué';
            $message = "<b class='text-danger text-center'>Echec ! </br> Cette opération est impossible.</b>";
            session()->flash('message', $message);
            $activity->save();

            return redirect()->back();
        }

    }
}
