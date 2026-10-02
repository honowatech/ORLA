<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Les ENUM qui figent la marque historique (« speedex ») deviennent des chaînes :
 * la valeur reste inchangée ici, elle sera renommée en même temps que le code qui
 * la compare. Le téléphone des agents devient indexable (unique composite à venir).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->string('mode_de_paiement', 20)->change();
        });

        Schema::table('paiement', function (Blueprint $table) {
            $table->string('qui_paie', 20)->change();
        });

        $longueurMax = DB::table('agents')->whereRaw('LENGTH(telephone) > 30')->exists() ? 255 : 30;

        Schema::table('agents', function (Blueprint $table) use ($longueurMax) {
            $table->string('telephone', $longueurMax)->change();
        });
    }

    public function down(): void
    {
        // Les types VARCHAR restent compatibles avec le code existant : pas de retour aux ENUM.
    }
};
