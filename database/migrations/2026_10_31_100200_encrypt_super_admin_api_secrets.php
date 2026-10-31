<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Les identifiants des API de paiement (Monetbil) étaient stockés en clair.
 */
return new class extends Migration
{
    protected const COLONNES = ['key', 'secret', 'password'];

    public function up(): void
    {
        Schema::table('super_admin_api', function (Blueprint $table) {
            foreach (self::COLONNES as $colonne) {
                $table->text($colonne)->nullable()->change();
            }
        });

        foreach (DB::table('super_admin_api')->get() as $api) {
            $valeurs = [];
            foreach (self::COLONNES as $colonne) {
                if ($api->$colonne !== null && ! $this->estChiffre($api->$colonne)) {
                    $valeurs[$colonne] = Crypt::encryptString($api->$colonne);
                }
            }
            if ($valeurs) {
                DB::table('super_admin_api')->where('id', $api->id)->update($valeurs);
            }
        }
    }

    public function down(): void
    {
        foreach (DB::table('super_admin_api')->get() as $api) {
            $valeurs = [];
            foreach (self::COLONNES as $colonne) {
                if ($api->$colonne !== null && $this->estChiffre($api->$colonne)) {
                    $valeurs[$colonne] = Crypt::decryptString($api->$colonne);
                }
            }
            if ($valeurs) {
                DB::table('super_admin_api')->where('id', $api->id)->update($valeurs);
            }
        }
    }

    protected function estChiffre(string $valeur): bool
    {
        try {
            Crypt::decryptString($valeur);

            return true;
        } catch (DecryptException) {
            return false;
        }
    }
};
