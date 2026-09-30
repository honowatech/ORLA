<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStockTable extends Migration
{
    public function up()
    {
        Schema::create('stock', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('id_produit')->unsigned();
            $table->integer('id_boutique')->unsigned()->nullable();
            $table->integer('id_point_relais')->unsigned()->nullable();
            $table->boolean('statut')->default(1);
            $table->string('libelle', 255);
            $table->float('Quantite_en_stock')->default('0');
            $table->enum('type_gestion', ['ajout', 'retrait']);
            $table->float('qute_changement')->default('0');
        });
    }

    public function down()
    {
        Schema::drop('stock');
    }
}
