<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProduitsTable extends Migration
{
    public function up()
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->increments('id');
            $table->string('noms', 255);
            $table->timestamps();
            $table->string('libelle', 255)->nullable();
            $table->text('description')->nullable();
            $table->boolean('statut')->default(0);
        });
    }

    public function down()
    {
        Schema::drop('produits');
    }
}
