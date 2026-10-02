<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Rattache toutes les données existantes à une entreprise unique (l'exploitant
 * historique de l'instance), repère la boutique et le quartier « siège » et
 * garantit la présence des types de client.
 *
 * N'utilise que le query builder : aucun scope ni hook Eloquent ne doit intervenir.
 */
return new class extends Migration
{
    private const TABLES = [
        'users', 'agents', 'coursiers', 'clients', 'informations_personnels', 'zone', 'quartier',
        'details_zone', 'montant_livraison', 'type_vehicule', 'vehicule', 'boutiques', 'point_relais',
        'produits', 'stock', 'commandes', 'details_commande', 'paiement', 'activity',
    ];

    public function up(): void
    {
        Schema::table('boutiques', function (Blueprint $table) {
            if (! Schema::hasColumn('boutiques', 'est_siege')) {
                $table->boolean('est_siege')->default(false)->after('statut');
            }
        });

        $idEntreprise = DB::table('entreprises')->orderBy('id')->value('id');

        if ($idEntreprise === null && $this->desDonneesExistent()) {
            $idEntreprise = DB::table('entreprises')->insertGetId([
                'name' => config('saas.entreprise_legacy.nom', 'Speedex'),
                'slug' => config('saas.entreprise_legacy.slug', 'speedex'),
                'statut' => true,
                'pays' => config('saas.pays', 'CM'),
                'devise' => config('saas.devise', 'XAF'),
                'prefixe_telephone' => config('saas.prefixe_telephone', '+237'),
                'tarif_defaut' => config('saas.tarif_defaut', 1000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($idEntreprise === null) {
            $this->garantirLesTypesDeClient();

            return;
        }

        foreach (self::TABLES as $nom) {
            if (Schema::hasTable($nom)) {
                DB::table($nom)->whereNull('entreprise_id')->update(['entreprise_id' => $idEntreprise]);
            }
        }

        DB::table('boutiques')
            ->where('entreprise_id', $idEntreprise)
            ->whereRaw('UPPER(libelle) = ?', ['SPEEDEX'])
            ->update(['est_siege' => true]);

        $this->definirLeQuartierSiege($idEntreprise);
        $this->garantirLesTypesDeClient();
    }

    public function down(): void
    {
        Schema::table('boutiques', function (Blueprint $table) {
            if (Schema::hasColumn('boutiques', 'est_siege')) {
                $table->dropColumn('est_siege');
            }
        });
    }

    private function desDonneesExistent(): bool
    {
        foreach (self::TABLES as $nom) {
            if (Schema::hasTable($nom) && DB::table($nom)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Le quartier « Speedex » créé à la volée par les contrôleurs matérialise le siège
     * de l'entreprise ; il garde son libellé tant que les contrôleurs le recherchent ainsi.
     */
    private function definirLeQuartierSiege(int $idEntreprise): void
    {
        if (DB::table('entreprises')->where('id', $idEntreprise)->whereNotNull('id_quartier_siege')->exists()) {
            return;
        }

        $idQuartier = DB::table('quartier')
            ->where('entreprise_id', $idEntreprise)
            ->whereRaw('UPPER(libelle) = ?', ['SPEEDEX'])
            ->orderBy('id')
            ->value('id');

        if ($idQuartier === null) {
            $idVille = DB::table('ville')->orderBy('id')->value('id');
            if ($idVille === null) {
                return;
            }
            $idQuartier = DB::table('quartier')->insertGetId([
                'entreprise_id' => $idEntreprise,
                'libelle' => 'Speedex',
                'id_ville' => $idVille,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('entreprises')->where('id', $idEntreprise)->update(['id_quartier_siege' => $idQuartier]);
    }

    private function garantirLesTypesDeClient(): void
    {
        foreach ([1 => 'Entreprise', 2 => 'Simple'] as $id => $libelle) {
            if (! DB::table('type_client')->where('id', $id)->exists()) {
                DB::table('type_client')->insert([
                    'id' => $id, 'libelle' => $libelle, 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }
};
