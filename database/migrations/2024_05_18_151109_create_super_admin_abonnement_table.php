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
        Schema::create('super_admin_abonnement', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('titre')->nullable();
            $table->integer('accumulateur')->unsigned()->default('1');
            $table->enum('type_periode', array('heure', 'jour', 'semaine', 'mois', 'annee'));
            $table->decimal('montant', 20, 2)->nullable();
            $table->integer('periode_grace')->nullable();
            $table->boolean('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('super_admin_abonnement');
    }
};
