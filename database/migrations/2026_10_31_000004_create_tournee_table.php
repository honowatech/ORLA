<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tournées optimisées : une tournée regroupe un livreur, un véhicule, une
 * date de départ et la séquence ordonnée des étapes (enlèvements / livraisons).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournee', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('id_ville')->unsigned();
            $table->integer('id_coursier')->unsigned();
            $table->integer('id_vehicule')->unsigned()->nullable();
            $table->integer('id_quartier_depart')->unsigned();
            $table->integer('id_saver')->unsigned();
            $table->datetime('date_depart');
            $table->datetime('date_fin_estimee')->nullable();
            $table->float('distance_totale_km')->default(0);
            $table->float('duree_totale_minutes')->default(0);
            $table->float('cout_total')->default(0);
            $table->float('distance_reference_km')->default(0);   // itinéraire non optimisé (ordre de saisie)
            $table->float('duree_reference_minutes')->default(0);
            $table->float('cout_reference')->default(0);
            $table->text('avertissements')->nullable();           // JSON
            $table->enum('statut', ['planifiee', 'encours', 'terminee', 'annulee'])->default('planifiee');
        });

        Schema::create('tournee_etape', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('id_tournee')->unsigned();
            $table->integer('id_commande')->unsigned();
            $table->integer('id_quartier')->unsigned();
            $table->enum('type_etape', ['enlevement', 'livraison']);
            $table->integer('ordre')->unsigned();
            $table->datetime('heure_arrivee_estimee')->nullable();
            $table->float('distance_etape_km')->default(0);
            $table->float('duree_etape_minutes')->default(0);
            $table->float('attente_minutes')->default(0);        // attente ouverture plage horaire
            $table->float('retard_minutes')->default(0);         // dépassement de la date de livraison promise
            $table->text('itineraire')->nullable();              // JSON : suite des quartiers traversés

            $table->index('id_tournee');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournee_etape');
        Schema::dropIfExists('tournee');
    }
};
