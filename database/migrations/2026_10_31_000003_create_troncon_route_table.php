<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tronçons de route entre deux quartiers : distance, durée, état de la
 * chaussée, restrictions horaires et types de véhicules interdits.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('troncon_route', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('id_ville')->unsigned();
            $table->integer('id_quartier_depart')->unsigned();
            $table->integer('id_quartier_arrivee')->unsigned();
            $table->float('distance_km');
            $table->float('duree_minutes')->nullable();          // si null : calculée via la vitesse du véhicule
            $table->enum('etat_route', ['bon', 'moyen', 'degrade', 'impraticable'])->default('bon');
            $table->boolean('bidirectionnel')->default(1);
            $table->time('heure_debut_fermeture')->nullable();    // plage pendant laquelle le tronçon est fermé
            $table->time('heure_fin_fermeture')->nullable();
            $table->text('types_vehicule_interdits')->nullable(); // JSON : liste d'id de type_vehicule
            $table->text('description')->nullable();
            $table->boolean('statut')->default(1);

            $table->index(['id_quartier_depart', 'id_quartier_arrivee']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('troncon_route');
    }
};
