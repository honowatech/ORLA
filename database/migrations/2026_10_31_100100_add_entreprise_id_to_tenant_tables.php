<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Rattache chaque donnée métier à une entreprise.
 *
 * Les données existantes sont rattachées à la première entreprise (créée si
 * besoin) : c'est l'entreprise qui exploitait la plateforme jusqu'ici.
 */
return new class extends Migration
{
    /** Tables propres à chaque entreprise : entreprise_id obligatoire. */
    public const TABLES = [
        'users', 'agents', 'coursiers', 'clients', 'informations_personnels',
        'zone', 'quartier', 'details_zone', 'montant_livraison',
        'vehicule',
        'boutiques', 'point_relais', 'produits', 'stock',
        'commandes', 'details_commande', 'paiement', 'activity',
    ];

    /** Référentiels partagés : entreprise_id vide = commun à toutes. */
    public const TABLES_PARTAGEES = ['ville', 'type_vehicule'];

    public function up(): void
    {
        foreach (array_merge(self::TABLES, self::TABLES_PARTAGEES) as $nom) {
            Schema::table($nom, function (Blueprint $table) {
                $table->unsignedBigInteger('entreprise_id')->nullable()->after('id')->index();
            });
        }

        $id = $this->entrepriseParDefaut();
        if ($id !== null) {
            foreach (self::TABLES as $nom) {
                DB::table($nom)->whereNull('entreprise_id')->update(['entreprise_id' => $id]);
            }
        }

        foreach (self::TABLES as $nom) {
            Schema::table($nom, function (Blueprint $table) {
                $table->unsignedBigInteger('entreprise_id')->nullable(false)->change();
                $table->foreign('entreprise_id')->references('id')->on('entreprises')
                    ->onDelete('restrict')->onUpdate('restrict');
            });
        }

        foreach (self::TABLES_PARTAGEES as $nom) {
            Schema::table($nom, function (Blueprint $table) {
                $table->foreign('entreprise_id')->references('id')->on('entreprises')
                    ->onDelete('restrict')->onUpdate('restrict');
            });
        }
    }

    public function down(): void
    {
        foreach (array_merge(self::TABLES, self::TABLES_PARTAGEES) as $nom) {
            Schema::table($nom, function (Blueprint $table) {
                $table->dropForeign(['entreprise_id']);
                $table->dropIndex(['entreprise_id']);
                $table->dropColumn('entreprise_id');
            });
        }
    }

    protected function entrepriseParDefaut(): ?int
    {
        $existeDesDonnees = collect(self::TABLES)->contains(fn ($nom) => DB::table($nom)->exists());
        if (! $existeDesDonnees) {
            return null;
        }

        $id = DB::table('entreprises')->orderBy('id')->value('id');
        if ($id !== null) {
            return $id;
        }

        return DB::table('entreprises')->insertGetId([
            'name' => config('app.name', 'Speedex'),
            'slug' => 'speedex',
            'statut' => 1,
            'devise' => 'XAF',
            'fuseau_horaire' => 'Africa/Douala',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
