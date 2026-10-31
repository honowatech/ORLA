<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables de gestion de parc automobile :
     * documents réglementaires, entretiens, carburant, incidents,
     * historique d'attribution et relevés de kilométrage.
     */
    public function up(): void
    {
        Schema::create('vehicule_document', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('id_vehicule');
            $table->string('type', 50); // assurance, visite_technique, carte_grise, vignette, taxe, autre
            $table->string('libelle', 255)->nullable();
            $table->string('numero', 255)->nullable();
            $table->string('fournisseur', 255)->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_expiration')->nullable();
            $table->float('montant')->nullable();
            $table->string('fichier', 255)->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('id_saver')->nullable();
            $table->index('id_vehicule');
            $table->index('date_expiration');
        });

        Schema::create('vehicule_maintenance', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('id_vehicule');
            $table->string('type', 50); // vidange, pneus, freins, revision, reparation, carrosserie, controle, autre
            $table->string('libelle', 255);
            $table->text('description')->nullable();
            $table->date('date_prevue')->nullable();
            $table->date('date_realisation')->nullable();
            $table->unsignedInteger('kilometrage')->nullable();
            $table->float('cout')->nullable();
            $table->string('prestataire', 255)->nullable();
            $table->date('prochaine_date')->nullable();
            $table->unsignedInteger('prochain_kilometrage')->nullable();
            $table->string('statut', 20)->default('planifie'); // planifie, en_cours, realise, annule
            $table->unsignedBigInteger('id_saver')->nullable();
            $table->index('id_vehicule');
            $table->index('statut');
        });

        Schema::create('vehicule_carburant', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('id_vehicule');
            $table->unsignedBigInteger('id_coursier')->nullable();
            $table->date('date');
            $table->float('litres');
            $table->float('montant');
            $table->unsignedInteger('kilometrage')->nullable();
            $table->boolean('plein_complet')->default(1);
            $table->string('station', 255)->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('id_saver')->nullable();
            $table->index('id_vehicule');
            $table->index('date');
        });

        Schema::create('vehicule_incident', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('id_vehicule');
            $table->unsignedBigInteger('id_coursier')->nullable();
            $table->string('type', 50); // accident, panne, vol, amende, autre
            $table->dateTime('date');
            $table->string('lieu', 255)->nullable();
            $table->text('description');
            $table->float('cout')->nullable();
            $table->string('statut', 20)->default('ouvert'); // ouvert, en_cours, resolu
            $table->date('date_resolution')->nullable();
            $table->boolean('immobilise')->default(0);
            $table->unsignedBigInteger('id_saver')->nullable();
            $table->index('id_vehicule');
            $table->index('statut');
        });

        Schema::create('vehicule_attribution', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('id_vehicule');
            $table->unsignedBigInteger('id_coursier');
            $table->dateTime('date_debut');
            $table->dateTime('date_fin')->nullable();
            $table->unsignedInteger('kilometrage_debut')->nullable();
            $table->unsignedInteger('kilometrage_fin')->nullable();
            $table->unsignedBigInteger('id_saver')->nullable();
            $table->index('id_vehicule');
            $table->index('id_coursier');
        });

        Schema::create('vehicule_kilometrage', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('id_vehicule');
            $table->date('date');
            $table->unsignedInteger('kilometrage');
            $table->string('source', 30)->default('manuel'); // manuel, carburant, maintenance, attribution, incident
            $table->text('description')->nullable();
            $table->unsignedBigInteger('id_saver')->nullable();
            $table->index('id_vehicule');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicule_kilometrage');
        Schema::dropIfExists('vehicule_attribution');
        Schema::dropIfExists('vehicule_incident');
        Schema::dropIfExists('vehicule_carburant');
        Schema::dropIfExists('vehicule_maintenance');
        Schema::dropIfExists('vehicule_document');
    }
};
