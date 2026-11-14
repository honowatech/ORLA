<?php

namespace App\Actions\Commandes;

use App\Models\commandes\Commandes;
use App\Models\details_commande\Details_commande;
use App\Models\montant_livraison\Montant_livraison;
use App\Models\quartier\Quartier;
use Carbon\Carbon;

/**
 * Enregistre une nouvelle commande « en attente », avec son tarif de zone et,
 * pour une commande entreprise, le détail des produits.
 *
 * Extrait des contrôleurs Admin et Client (méthode store), qui le partagent
 * avec le générateur de la démo. La validation du formulaire et la création
 * du client ou des quartiers à la volée restent dans les contrôleurs.
 */
class EnregistrerCommande
{
    /**
     * @param  array  $data  id_client, telephone, nom_client, id_boutique (entreprise), id_saver,
     *                       type_commande, adresse_colis, id_quartier_colis, adresse_livraison,
     *                       id_quartier_livraison, montant_livraison, montant_recuperer,
     *                       date_livraison (Carbon), description, mode_de_paiement
     * @param  array  $produits  liste de [Produits $produit, quantité] (commande entreprise)
     */
    public function handle(array $data, array $produits = []): Commandes
    {
        $commande = new Commandes;
        $commande->id_client = $data['id_client'];
        $commande->telephone = $data['telephone'];
        $commande->nom_client = $data['nom_client'];
        if ($data['type_commande'] == 'entreprise') {
            $commande->id_boutique = $data['id_boutique'];
        }
        $commande->id_saver = $data['id_saver'];
        $commande->date_commande = now();
        $commande->type_commande = $data['type_commande'];
        $commande->adresse_colis = $data['adresse_colis'];
        $commande->id_quartier_colis = $data['id_quartier_colis'];
        $commande->adresse_livraison = $data['adresse_livraison'];
        $commande->id_quartier_livraison = $data['id_quartier_livraison'];
        $commande->montant_livraison = $data['montant_livraison'];
        $commande->montant_recuperer = $data['montant_recuperer'] ?? null;
        $commande->date_livraison = $data['date_livraison'] instanceof Carbon
            ? $data['date_livraison']
            : Carbon::parse($data['date_livraison']);
        $commande->id_montant_livraison = $this->tarifDeZone($data['id_quartier_colis'], $data['id_quartier_livraison']);
        $commande->description = $data['description'];
        $commande->mode_de_paiement = $data['mode_de_paiement'];
        $commande->statut = 'attente';
        $commande->save();

        if ($data['type_commande'] == 'entreprise') {
            foreach ($produits as [$produit, $quantite]) {
                $detail = new Details_commande;
                $detail->id_commande = $commande->id;
                $detail->id_produit = $produit->id;
                $detail->nom_produit = $produit->noms;
                $detail->quantite = $quantite;
                $detail->prix = $data['montant_livraison'];
                $detail->save();
            }
        }

        return $commande;
    }

    /**
     * Tarif applicable entre deux quartiers de zones différentes, null sinon.
     */
    public function tarifDeZone($idQuartierColis, $idQuartierLivraison): ?int
    {
        $depart = Quartier::findOrFail($idQuartierColis);
        $arrivee = Quartier::findOrFail($idQuartierLivraison);
        if ($depart->zone == $arrivee->zone || $depart->zone == null || $arrivee->zone == null) {
            return null;
        }

        return Montant_livraison::whereIn('id_zone_colis', [$depart->zone->id, $arrivee->zone->id])
            ->whereIn('id_zone_livraison', [$depart->zone->id, $arrivee->zone->id])
            ->limit(1)
            ->get('id')
            ->value('id');
    }
}
