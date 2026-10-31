<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Journal d'audit d'une entreprise : qui a créé, modifié ou supprimé quoi.
 * Alimenté automatiquement par le trait AppartientAEntreprise.
 */
class Journal extends Model
{
    use \App\Models\Concerns\AppartientAEntreprise;

    protected $table = 'journal';

    public const UPDATED_AT = null;

    protected $casts = [
        'changements' => 'array',
        'created_at' => 'datetime',
    ];

    /** Données jamais recopiées dans le journal. */
    public const MASQUES = ['password', 'remember_token', 'jeton_suivi', 'created_at', 'updated_at', 'entreprise_id'];

    /** Tables techniques non journalisées. */
    public const IGNOREES = ['journal', 'activity'];

    /** Libellés lisibles des tables métier. */
    public const LIBELLES = [
        'users' => 'Utilisateur', 'agents' => 'Agent', 'coursiers' => 'Livreur', 'clients' => 'Client',
        'informations_personnels' => 'Informations personnelles', 'zone' => 'Zone', 'quartier' => 'Quartier',
        'details_zone' => 'Affectation de zone', 'montant_livraison' => 'Tarif', 'vehicule' => 'Véhicule',
        'type_vehicule' => 'Type de véhicule', 'ville' => 'Ville', 'boutiques' => 'Boutique',
        'point_relais' => 'Point relais', 'produits' => 'Produit', 'stock' => 'Stock', 'commandes' => 'Commande',
        'details_commande' => 'Détail de commande', 'paiement' => 'Paiement',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public static function enregistrer(Model $sujet, string $action): void
    {
        if (in_array($sujet->getTable(), self::IGNOREES, true) || $sujet->entreprise_id === null) {
            return;
        }

        $changements = match ($action) {
            'creation' => $sujet->getAttributes(),
            'modification' => array_map(
                fn ($colonne) => ['avant' => $sujet->getOriginal($colonne), 'apres' => $sujet->getAttribute($colonne)],
                array_combine(array_keys($sujet->getChanges()), array_keys($sujet->getChanges()))
            ),
            default => null,
        };
        if (is_array($changements)) {
            $changements = array_diff_key($changements, array_flip(self::MASQUES));
            if ($action === 'modification' && $changements === []) {
                return;
            }
        }

        $entree = new static;
        $entree->entreprise_id = $sujet->entreprise_id;
        $entree->id_user = auth()->id();
        $entree->action = $action;
        $entree->sujet_table = $sujet->getTable();
        $entree->sujet_id = $sujet->getKey();
        $entree->changements = $changements;
        $entree->ip = app()->runningInConsole() ? null : request()->ip();
        $entree->save();
    }
}
