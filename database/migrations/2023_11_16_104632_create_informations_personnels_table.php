<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInformationsPersonnelsTable extends Migration
{
    public function up()
    {
        Schema::create('informations_personnels', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('id_agent')->unsigned()->nullable();
            $table->integer('id_client')->unsigned()->nullable();
            $table->integer('id_coursier')->unsigned()->nullable();
            $table->string('telephone2', 255)->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance', 255)->nullable();
            $table->string('cni', 255)->nullable();
            $table->date('date_delivrance')->nullable();
            $table->string('lieu_delivrance', 255)->nullable();
            $table->date('date_expiration')->nullable();
            $table->string('localisation', 255)->nullable();
            $table->integer('id_quartier')->unsigned()->nullable();
        });
    }

    public function down()
    {
        Schema::drop('informations_personnels');
    }
}
