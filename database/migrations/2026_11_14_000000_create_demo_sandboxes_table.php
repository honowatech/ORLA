<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registre des bacs démo, dans la base principale.
 *
 * Les données d'un bac vivent dans son propre fichier SQLite ; cette table
 * ne garde que ce qu'il faut pour les attribuer, les expirer et les purger.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demo_sandboxes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->unique();      // nom du fichier, aléatoire
            $table->unsignedInteger('seed');
            $table->date('built_on')->nullable();      // jour du bac (fuseau démo)
            $table->dateTime('built_at')->nullable();  // nul tant qu'il se construit
            $table->dateTime('claimed_at')->nullable();
            $table->string('claim_token', 64)->nullable()->unique();
            $table->dateTime('expires_at')->nullable(); // inactivité
            $table->dateTime('ends_at')->nullable();    // durée maximale
            $table->dateTime('ended_at')->nullable();   // « Terminer »
            $table->timestamps();

            $table->index(['claimed_at', 'built_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_sandboxes');
    }
};
