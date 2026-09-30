<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDetailsCommandeTable extends Migration
{
    public function up()
    {
        Schema::create('details_commande', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('id_commande')->unsigned();
            $table->integer('id_produit')->unsigned()->nullable();
            $table->string('nom_produit', 255)->nullable();
            $table->float('quantite')->nullable();
            $table->float('prix');
        });
    }

    public function down()
    {
        Schema::drop('details_commande');
    }
}
