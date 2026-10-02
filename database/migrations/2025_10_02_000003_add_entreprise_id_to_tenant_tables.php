<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Colonne d'appartenance à une entreprise sur chaque table métier.
 * Nullable à ce stade : le remplissage a lieu dans la migration suivante et la
 * contrainte NOT NULL + clé étrangère est posée après audit (phase de durcissement).
 * La table `ville` est volontairement exclue : elle devient un référentiel partagé.
 */
return new class extends Migration
{
    public const TABLES = [
        'users',
        'agents',
        'coursiers',
        'clients',
        'informations_personnels',
        'zone',
        'quartier',
        'details_zone',
        'montant_livraison',
        'type_vehicule',
        'vehicule',
        'boutiques',
        'point_relais',
        'produits',
        'stock',
        'commandes',
        'details_commande',
        'paiement',
        'activity',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $nom) {
            if (! Schema::hasTable($nom) || Schema::hasColumn($nom, 'entreprise_id')) {
                continue;
            }

            Schema::table($nom, function (Blueprint $table) use ($nom) {
                $table->unsignedBigInteger('entreprise_id')->nullable()->after('id');
                $table->index('entreprise_id', "{$nom}_entreprise_id_index");
            });
        }
    }

    public function down(): void
    {
        foreach (array_reverse(self::TABLES) as $nom) {
            if (! Schema::hasTable($nom) || ! Schema::hasColumn($nom, 'entreprise_id')) {
                continue;
            }

            Schema::table($nom, function (Blueprint $table) use ($nom) {
                $table->dropIndex("{$nom}_entreprise_id_index");
                $table->dropColumn('entreprise_id');
            });
        }
    }
};
