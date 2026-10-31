<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Coordonnées GPS des quartiers : utilisées par l'optimiseur d'itinéraires
 * pour estimer les distances lorsqu'aucun tronçon de route n'est renseigné.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quartier', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('quartier', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
