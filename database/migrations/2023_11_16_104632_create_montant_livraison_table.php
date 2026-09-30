<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMontantLivraisonTable extends Migration
{
    public function up()
    {
        Schema::create('montant_livraison', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('id_zone_colis')->unsigned();
            $table->integer('id_zone_livraison')->unsigned();
            $table->float('montant');
        });
    }

    public function down()
    {
        Schema::drop('montant_livraison');
    }
}
