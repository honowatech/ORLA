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
        Schema::create('type_vehicule', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('libelle', 255)->nullable();
            $table->text('description')->nullable();
            $table->boolean('statut')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_vehicule');
    }
};