<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Les identifiants de passerelle de paiement sont chiffrés en base (cast
 * `encrypted` sur le modèle Api). Idempotent : une valeur déjà chiffrée est
 * laissée telle quelle. Attention : la rotation d'APP_KEY rendrait ces valeurs illisibles.
 */
return new class extends Migration
{
    private const COLONNES = ['key', 'secret', 'password'];

    public function up(): void
    {
        Schema::table('super_admin_api', function (Blueprint $table) {
            foreach (self::COLONNES as $colonne) {
                $table->text($colonne)->nullable()->change();
            }
        });

        DB::table('super_admin_api')->orderBy('id')->get()->each(function ($ligne) {
            $modifs = [];
            foreach (self::COLONNES as $colonne) {
                $valeur = $ligne->{$colonne};
                if ($valeur === null || $valeur === '') {
                    continue;
                }
                try {
                    Crypt::decryptString($valeur);
                } catch (DecryptException) {
                    $modifs[$colonne] = Crypt::encryptString($valeur);
                }
            }
            if ($modifs !== []) {
                DB::table('super_admin_api')->where('id', $ligne->id)->update($modifs);
            }
        });
    }

    public function down(): void
    {
        DB::table('super_admin_api')->orderBy('id')->get()->each(function ($ligne) {
            $modifs = [];
            foreach (self::COLONNES as $colonne) {
                $valeur = $ligne->{$colonne};
                if ($valeur === null || $valeur === '') {
                    continue;
                }
                try {
                    $modifs[$colonne] = Crypt::decryptString($valeur);
                } catch (DecryptException) {
                    // Déjà en clair.
                }
            }
            if ($modifs !== []) {
                DB::table('super_admin_api')->where('id', $ligne->id)->update($modifs);
            }
        });
    }
};
