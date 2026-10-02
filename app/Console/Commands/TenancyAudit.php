<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Vérifie la cohérence du cloisonnement par entreprise :
 * lignes orphelines (entreprise_id NULL), références croisées entre entreprises
 * et doublons qui empêcheraient la pose des index uniques composites.
 *
 * Utilise uniquement le query builder (aucun scope Eloquent).
 */
class TenancyAudit extends Command
{
    protected $signature = 'tenancy:audit';

    protected $description = 'Audite le cloisonnement des données par entreprise (lignes orphelines, références croisées, doublons)';

    public const TABLES = [
        'users', 'agents', 'coursiers', 'clients', 'informations_personnels', 'zone', 'quartier',
        'details_zone', 'montant_livraison', 'type_vehicule', 'vehicule', 'boutiques', 'point_relais',
        'produits', 'stock', 'commandes', 'details_commande', 'paiement', 'activity',
    ];

    /** [table enfant, colonne, table parent] */
    public const REFERENCES = [
        ['users', 'id_agent', 'agents'],
        ['users', 'id_coursier', 'coursiers'],
        ['users', 'id_client', 'clients'],
        ['agents', 'id_utilisateur', 'users'],
        ['coursiers', 'id_utilisateur', 'users'],
        ['clients', 'id_utilisateur', 'users'],
        ['informations_personnels', 'id_agent', 'agents'],
        ['informations_personnels', 'id_coursier', 'coursiers'],
        ['informations_personnels', 'id_client', 'clients'],
        ['informations_personnels', 'id_quartier', 'quartier'],
        ['quartier', 'id_zone', 'zone'],
        ['details_zone', 'id_coursier', 'coursiers'],
        ['details_zone', 'id_zone', 'zone'],
        ['montant_livraison', 'id_zone_colis', 'zone'],
        ['montant_livraison', 'id_zone_livraison', 'zone'],
        ['vehicule', 'id_type', 'type_vehicule'],
        ['vehicule', 'id_coursier', 'coursiers'],
        ['boutiques', 'id_client', 'clients'],
        ['boutiques', 'id_quartier', 'quartier'],
        ['point_relais', 'id_coursier', 'coursiers'],
        ['point_relais', 'id_quartier', 'quartier'],
        ['stock', 'id_produit', 'produits'],
        ['stock', 'id_boutique', 'boutiques'],
        ['stock', 'id_point_relais', 'point_relais'],
        ['commandes', 'id_client', 'clients'],
        ['commandes', 'id_boutique', 'boutiques'],
        ['commandes', 'id_coursier', 'coursiers'],
        ['commandes', 'id_agent', 'agents'],
        ['commandes', 'id_point_relais', 'point_relais'],
        ['commandes', 'id_saver', 'users'],
        ['commandes', 'id_quartier_colis', 'quartier'],
        ['commandes', 'id_quartier_livraison', 'quartier'],
        ['commandes', 'id_montant_livraison', 'montant_livraison'],
        ['details_commande', 'id_commande', 'commandes'],
        ['details_commande', 'id_produit', 'produits'],
        ['paiement', 'id_client', 'clients'],
        ['activity', 'id_user', 'users'],
    ];

    /** Futurs index uniques composites (phase de durcissement). */
    public const UNIQUES = [
        'zone' => ['entreprise_id', 'id_ville', 'libelle'],
        'quartier' => ['entreprise_id', 'id_ville', 'libelle'],
        'type_vehicule' => ['entreprise_id', 'libelle'],
        'vehicule' => ['entreprise_id', 'immatriculation'],
        'produits' => ['entreprise_id', 'noms'],
        'boutiques' => ['entreprise_id', 'id_client', 'libelle'],
        'coursiers' => ['entreprise_id', 'telephone'],
        'agents' => ['entreprise_id', 'telephone'],
        'clients' => ['entreprise_id', 'telephone'],
        'users' => ['entreprise_id', 'telephone'],
        'montant_livraison' => ['entreprise_id', 'id_zone_colis', 'id_zone_livraison'],
    ];

    public function handle(): int
    {
        $anomalies = 0;

        $this->components->info('Lignes sans entreprise');
        $orphelines = [];
        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'entreprise_id')) {
                continue;
            }
            $nombre = DB::table($table)->whereNull('entreprise_id')->count();
            if ($nombre > 0) {
                $orphelines[] = [$table, $nombre];
                $anomalies += $nombre;
            }
        }
        $this->afficher(['Table', 'Lignes'], $orphelines, 'Aucune ligne orpheline.');

        $this->components->info('Références vers une autre entreprise');
        $croisees = [];
        foreach (self::REFERENCES as [$enfant, $colonne, $parent]) {
            if (! Schema::hasTable($enfant) || ! Schema::hasTable($parent)) {
                continue;
            }
            $nombre = DB::table("{$enfant} as e")
                ->join("{$parent} as p", 'p.id', '=', "e.{$colonne}")
                ->whereNotNull("e.{$colonne}")
                ->whereColumn('e.entreprise_id', '!=', 'p.entreprise_id')
                ->count();
            if ($nombre > 0) {
                $croisees[] = ["{$enfant}.{$colonne} → {$parent}", $nombre];
                $anomalies += $nombre;
            }
        }
        if (Schema::hasTable('entreprises') && Schema::hasColumn('entreprises', 'id_quartier_siege')) {
            $nombre = DB::table('entreprises as e')
                ->join('quartier as q', 'q.id', '=', 'e.id_quartier_siege')
                ->whereColumn('q.entreprise_id', '!=', 'e.id')
                ->count();
            if ($nombre > 0) {
                $croisees[] = ['entreprises.id_quartier_siege → quartier', $nombre];
                $anomalies += $nombre;
            }
        }
        $this->afficher(['Référence', 'Lignes'], $croisees, 'Aucune référence croisée.');

        $this->components->info('Doublons bloquant les futurs index uniques');
        $doublons = [];
        foreach (self::UNIQUES as $table => $colonnes) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            $nombre = DB::table($table)
                ->select($colonnes)
                ->whereNotNull(end($colonnes))
                ->groupBy($colonnes)
                ->havingRaw('COUNT(*) > 1')
                ->get()
                ->count();
            if ($nombre > 0) {
                $doublons[] = [$table.' ('.implode(', ', $colonnes).')', $nombre];
                $anomalies += $nombre;
            }
        }
        $this->afficher(['Index', 'Groupes en double'], $doublons, 'Aucun doublon.');

        if ($anomalies > 0) {
            $this->components->error("{$anomalies} anomalie(s) détectée(s).");

            return self::FAILURE;
        }

        $this->components->success('Cloisonnement cohérent.');

        return self::SUCCESS;
    }

    private function afficher(array $entetes, array $lignes, string $vide): void
    {
        if ($lignes === []) {
            $this->line("  {$vide}");

            return;
        }

        $this->table($entetes, $lignes);
    }
}
