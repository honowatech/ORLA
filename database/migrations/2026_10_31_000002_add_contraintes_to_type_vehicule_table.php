<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Contraintes d'exploitation par type de véhicule (vitesse, coûts,
 * plage horaire autorisée, capacité) utilisées par l'optimiseur d'itinéraires.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('type_vehicule', function (Blueprint $table) {
            $table->float('vitesse_moyenne')->default(30);      // km/h en ville
            $table->float('cout_km')->default(0);               // coût carburant/usure par km
            $table->float('cout_heure')->default(0);            // coût horaire (livreur + véhicule)
            $table->time('heure_debut_autorisee')->nullable();  // ex: 06:00 (camions)
            $table->time('heure_fin_autorisee')->nullable();    // ex: 22:00
            $table->integer('capacite_colis')->unsigned()->nullable(); // nb max de commandes par tournée
        });
    }

    public function down(): void
    {
        Schema::table('type_vehicule', function (Blueprint $table) {
            $table->dropColumn([
                'vitesse_moyenne', 'cout_km', 'cout_heure',
                'heure_debut_autorisee', 'heure_fin_autorisee', 'capacite_colis',
            ]);
        });
    }
};
