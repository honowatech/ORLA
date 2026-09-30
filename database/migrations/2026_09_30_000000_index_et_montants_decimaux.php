<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Index sur les colonnes filtrées sans index, et montants en decimal au
 * lieu de float (arrondis binaires sur les sommes d'argent).
 *
 * Pas de nouvelles clés étrangères ici : une base existante peut contenir
 * des lignes orphelines qui feraient échouer la migration.
 */
return new class extends Migration
{
    /** Table => colonnes à indexer. */
    private const INDEX = [
        'commandes' => ['statut', 'date_livraison', 'date_livre', 'id_agent'],
        'vehicule' => ['id_type', 'id_coursier', 'id_ville'],
        'super_admin_client' => ['id_abonnement'],
        'super_admin_transaction' => ['id_client', 'id_abonnement', 'statut'],
        'super_admin_info_transaction' => ['id_transaction'],
        'activity' => ['id_user'],
    ];

    /** Table => colonnes de montant. */
    private const MONTANTS = [
        'commandes' => ['montant_livraison', 'montant_recuperer'],
        'details_commande' => ['prix'],
        'montant_livraison' => ['montant'],
        'paiement' => ['montant'],
    ];

    public function up(): void
    {
        foreach (self::INDEX as $table => $colonnes) {
            Schema::table($table, function (Blueprint $blueprint) use ($colonnes) {
                foreach ($colonnes as $colonne) {
                    $blueprint->index($colonne);
                }
            });
        }

        foreach (self::MONTANTS as $table => $colonnes) {
            Schema::table($table, function (Blueprint $blueprint) use ($table, $colonnes) {
                foreach ($colonnes as $colonne) {
                    $colonneModifiee = $blueprint->decimal($colonne, 12, 2);
                    if ($this->nullable($table, $colonne)) {
                        $colonneModifiee->nullable();
                    }
                    $colonneModifiee->change();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (self::MONTANTS as $table => $colonnes) {
            Schema::table($table, function (Blueprint $blueprint) use ($table, $colonnes) {
                foreach ($colonnes as $colonne) {
                    $colonneModifiee = $blueprint->float($colonne);
                    if ($this->nullable($table, $colonne)) {
                        $colonneModifiee->nullable();
                    }
                    $colonneModifiee->change();
                }
            });
        }

        foreach (self::INDEX as $table => $colonnes) {
            Schema::table($table, function (Blueprint $blueprint) use ($colonnes) {
                foreach ($colonnes as $colonne) {
                    $blueprint->dropIndex([$colonne]);
                }
            });
        }
    }

    /** Conserve le caractère nullable d'origine de la colonne. */
    private function nullable(string $table, string $colonne): bool
    {
        foreach (Schema::getColumns($table) as $info) {
            if ($info['name'] === $colonne) {
                return $info['nullable'];
            }
        }

        return false;
    }
};
