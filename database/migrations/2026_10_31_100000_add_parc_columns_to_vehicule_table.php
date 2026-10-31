<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Enrichit la table vehicule pour la gestion de parc :
     * état opérationnel, kilométrage, carburant, acquisition, identifiants.
     */
    public function up(): void
    {
        Schema::table('vehicule', function (Blueprint $table) {
            $table->string('etat', 30)->default('disponible'); // disponible, en_service, en_maintenance, immobilise, hors_service
            $table->unsignedInteger('kilometrage')->default(0);
            $table->string('numero_chassis', 255)->nullable();
            $table->unsignedSmallInteger('annee')->nullable();
            $table->string('carburant', 30)->nullable(); // essence, diesel, electrique, hybride, gpl
            $table->float('capacite_reservoir')->nullable();
            $table->date('date_acquisition')->nullable();
            $table->float('prix_acquisition')->nullable();
            $table->string('photo', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('vehicule', function (Blueprint $table) {
            $table->dropColumn([
                'etat', 'kilometrage', 'numero_chassis', 'annee', 'carburant',
                'capacite_reservoir', 'date_acquisition', 'prix_acquisition', 'photo',
            ]);
        });
    }
};
