<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicule', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('immatriculation', 255)->nullable();
            $table->string('couleur', 255)->nullable();
            $table->integer('id_type')->unsigned();
            $table->integer('id_coursier')->unsigned()->nullable();
            $table->string('marque', 255)->nullable();
            $table->string('modele', 255)->nullable();
            $table->text('description')->nullable();
            $table->integer('id_ville')->unsigned();
            $table->boolean('statut')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicule');
    }
};
