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
        Schema::create('super_admin_client', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('name', 255)->nullable();
            $table->string('adresse', 255)->nullable();
            $table->string('telephone', 255)->nullable();
            $table->string('cni', 255)->nullable();
            $table->string('telephone_secondaire', 255)->nullable();
            $table->integer('id_abonnement')->nullable();
            $table->datetime('date_fin')->nullable();
            $table->datetime('date_dernier_paiement')->nullable();
            $table->boolean('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('super_admin_client');
    }
};
