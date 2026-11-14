<?php

namespace App\Services;

use App\Models\clients\Clients;
use App\Models\commandes\Commandes;
use App\Models\quartier\Quartier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Création des commandes passées depuis l'application mobile.
 *
 * Les adresses sont stockées au même format que le back-office web
 * (champs séparés par SEPARATEUR_ADRESSE) pour rester lisibles dans les deux interfaces.
 */
class CommandeService
{
    public const SEPARATEUR_ADRESSE = '*/*';

    public function __construct(private readonly TarifService $tarifs)
    {
    }

    /**
     * @param  array<string, mixed>  $donnees  données validées (voir StoreCommandeRequest)
     */
    public function creerPourClient(User $auteur, Clients $client, array $donnees): Commandes
    {
        return DB::transaction(function () use ($auteur, $client, $donnees) {
            $quartierColis = $this->resoudreQuartier(
                $donnees['collecte']['id_quartier'] ?? null,
                $donnees['collecte']['quartier'] ?? null,
                $donnees['collecte']['id_ville'] ?? null,
            );
            $quartierLivraison = $this->resoudreQuartier(
                $donnees['livraison']['id_quartier'] ?? null,
                $donnees['livraison']['quartier'] ?? null,
                $donnees['livraison']['id_ville'] ?? null,
            );

            $tarif = $this->tarifs->estimer($quartierColis, $quartierLivraison);

            $commande = new Commandes;
            $commande->id_client = $client->id;
            $commande->nom_client = trim($client->noms.' '.$client->Prenoms);
            $commande->telephone = $client->telephone;
            $commande->id_saver = $auteur->id;
            $commande->date_commande = now();
            $commande->type_commande = 'simple';
            $commande->adresse_colis = implode(self::SEPARATEUR_ADRESSE, [
                $donnees['collecte']['contact'],
                $quartierColis->libelle,
                $donnees['collecte']['description'] ?? '',
            ]);
            $commande->id_quartier_colis = $quartierColis->id;
            $commande->adresse_livraison = implode(self::SEPARATEUR_ADRESSE, [
                $donnees['livraison']['contact'],
                $quartierLivraison->libelle,
                $donnees['livraison']['description'] ?? '',
                $donnees['livraison']['nom'],
            ]);
            $commande->id_quartier_livraison = $quartierLivraison->id;
            $commande->montant_livraison = $tarif['montant'];
            $commande->id_montant_livraison = $tarif['id_montant_livraison'];
            $commande->montant_recuperer = $donnees['montant_a_recuperer'] ?? null;
            $commande->date_livraison = Carbon::createFromFormat(
                'Y-m-d H:i',
                $donnees['date_livraison'].' '.($donnees['heure_livraison'] ?? '00:00'),
            )->startOfMinute();
            $commande->description = $donnees['description'];
            $commande->mode_de_paiement = $donnees['mode_de_paiement'];
            $commande->statut = 'attente';
            $commande->save();

            return $commande;
        });
    }

    /**
     * Retrouve un quartier par son id, ou par son libellé dans une ville
     * (il est créé s'il n'existe pas encore, comme dans le back-office).
     */
    public function resoudreQuartier(?int $id, ?string $libelle, ?int $idVille): Quartier
    {
        if ($id !== null) {
            return Quartier::findOrFail($id);
        }

        $libelle = trim((string) $libelle);

        return Quartier::query()
            ->where('libelle', $libelle)
            ->where('id_ville', $idVille)
            ->first()
            ?? tap(new Quartier, function (Quartier $quartier) use ($libelle, $idVille) {
                $quartier->libelle = $libelle;
                $quartier->id_ville = $idVille;
                $quartier->save();
            });
    }

    /**
     * Décompose une adresse stockée : contact, lieu, description et (pour la
     * livraison) nom du destinataire, séparés par SEPARATEUR_ADRESSE.
     *
     * @return array{contact: ?string, lieu: ?string, description: ?string, nom: ?string}
     */
    public static function decoderAdresse(?string $adresse): array
    {
        $parties = $adresse === null ? [] : explode(self::SEPARATEUR_ADRESSE, $adresse);
        $valeur = fn (int $i) => isset($parties[$i]) && trim($parties[$i]) !== '' ? trim($parties[$i]) : null;

        return [
            'contact' => $valeur(0),
            'lieu' => $valeur(1),
            'description' => $valeur(2),
            'nom' => $valeur(3),
        ];
    }
}
