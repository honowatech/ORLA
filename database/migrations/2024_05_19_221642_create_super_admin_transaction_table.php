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
        Schema::create('super_admin_transaction', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('id_client')->unsigned();
            $table->integer('id_abonnement')->unsigned();
            $table->enum('methode', ['mobile', 'bank', 'application']);
            $table->decimal('montant', 20, 2)->nullable();
            $table->integer('nbre_abonnement')->nullable();
            $table->datetime('date_debut')->nullable();
            $table->datetime('date_fin')->nullable();
            $table->enum('statut', ['waiting', 'success', 'failed', 'cancelled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('super_admin_transaction');
    }
};
