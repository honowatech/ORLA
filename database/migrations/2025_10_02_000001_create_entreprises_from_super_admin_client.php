<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * La « licence d'instance » super_admin_client devient la table des entreprises
 * hébergées (tenants). Les colonnes existantes sont conservées ; les nouvelles
 * portent l'identité, les paramètres et l'essai gratuit de chaque entreprise.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('super_admin_client') && ! Schema::hasTable('entreprises')) {
            Schema::rename('super_admin_client', 'entreprises');
        }

        if (! Schema::hasTable('entreprises')) {
            Schema::create('entreprises', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->softDeletes();
                $table->string('name', 255)->nullable();
                $table->string('adresse', 255)->nullable();
                $table->string('telephone', 255)->nullable();
                $table->string('cni', 255)->nullable();
                $table->string('telephone_secondaire', 255)->nullable();
                $table->unsignedBigInteger('id_abonnement')->nullable();
                $table->datetime('date_fin')->nullable();
                $table->datetime('date_dernier_paiement')->nullable();
                $table->boolean('statut')->default(true);
            });
        }

        Schema::table('entreprises', function (Blueprint $table) {
            if (! Schema::hasColumn('entreprises', 'slug')) {
                $table->string('slug', 80)->nullable()->after('name');
            }
            if (! Schema::hasColumn('entreprises', 'email')) {
                $table->string('email')->nullable()->after('slug');
            }
            if (! Schema::hasColumn('entreprises', 'logo_path')) {
                $table->string('logo_path')->nullable();
            }
            if (! Schema::hasColumn('entreprises', 'pays')) {
                $table->string('pays', 2)->default('CM');
            }
            if (! Schema::hasColumn('entreprises', 'devise')) {
                $table->string('devise', 3)->default('XAF');
            }
            if (! Schema::hasColumn('entreprises', 'prefixe_telephone')) {
                $table->string('prefixe_telephone', 6)->default('+237');
            }
            if (! Schema::hasColumn('entreprises', 'essai_fin')) {
                $table->datetime('essai_fin')->nullable();
            }
            if (! Schema::hasColumn('entreprises', 'id_quartier_siege')) {
                // Même type que quartier.id (INT UNSIGNED) pour permettre une clé étrangère.
                $table->unsignedInteger('id_quartier_siege')->nullable();
            }
            if (! Schema::hasColumn('entreprises', 'tarif_defaut')) {
                $table->decimal('tarif_defaut', 12, 2)->default(1000);
            }
            if (! Schema::hasColumn('entreprises', 'parametres')) {
                $table->json('parametres')->nullable();
            }
        });

        // Même type que super_admin_abonnement.id (BIGINT UNSIGNED) pour la future clé étrangère.
        Schema::table('entreprises', function (Blueprint $table) {
            $table->unsignedBigInteger('id_abonnement')->nullable()->change();
        });

        $this->remplirLesSlugs();
    }

    public function down(): void
    {
        Schema::table('entreprises', function (Blueprint $table) {
            $colonnes = array_filter(
                ['slug', 'email', 'logo_path', 'pays', 'devise', 'prefixe_telephone', 'essai_fin', 'id_quartier_siege', 'tarif_defaut', 'parametres'],
                fn (string $colonne) => Schema::hasColumn('entreprises', $colonne)
            );
            if ($colonnes !== []) {
                $table->dropColumn(array_values($colonnes));
            }
        });

        if (Schema::hasTable('entreprises') && ! Schema::hasTable('super_admin_client')) {
            Schema::rename('entreprises', 'super_admin_client');
        }
    }

    private function remplirLesSlugs(): void
    {
        $existants = DB::table('entreprises')->whereNotNull('slug')->pluck('slug')->all();

        DB::table('entreprises')->whereNull('slug')->orderBy('id')->get(['id', 'name'])->each(function ($ligne) use (&$existants) {
            $base = Str::slug((string) $ligne->name) ?: 'entreprise-'.$ligne->id;
            $slug = $base;
            $suffixe = 2;
            while (in_array($slug, $existants, true)) {
                $slug = "{$base}-{$suffixe}";
                $suffixe++;
            }
            $existants[] = $slug;
            DB::table('entreprises')->where('id', $ligne->id)->update(['slug' => $slug]);
        });
    }
};
