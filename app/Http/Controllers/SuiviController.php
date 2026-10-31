<?php

namespace App\Http\Controllers;

use App\Models\commandes\Commandes;
use App\Tenancy\EntrepriseCourante;

/**
 * Suivi public d'une livraison, sans compte, à partir du lien envoyé au
 * destinataire. N'affiche que l'état et le quartier : ni adresse complète,
 * ni téléphone, ni montant.
 */
class SuiviController extends Controller
{
    /** statut => [libellé, couleur, étape (0 = hors parcours normal)] */
    public const ETAPES = [
        'attente' => ['Commande enregistrée', 'secondary', 1],
        'attribue' => ['Livreur attribué', 'info', 2],
        'encours' => ['En cours de livraison', 'warning', 3],
        'livre' => ['Livrée', 'success', 4],
        'annulee' => ['Annulée', 'danger', 0],
        'echoue' => ['Livraison échouée', 'danger', 0],
        'supprime' => ['Annulée', 'danger', 0],
    ];

    public function show(string $jeton, EntrepriseCourante $courante)
    {
        $commande = $courante->sansFiltre(
            fn () => Commandes::with(['entreprise', 'quartier_livraison'])->where('jeton_suivi', $jeton)->first()
        );
        abort_if($commande === null || ! $commande->entreprise?->estActive(), 404);

        return view('suivi.show', [
            'commande' => $commande,
            'entreprise' => $commande->entreprise,
            'etapes' => self::ETAPES,
            'etat' => self::ETAPES[$commande->statut] ?? self::ETAPES['attente'],
        ]);
    }
}
