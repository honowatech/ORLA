<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePointRelaisTable extends Migration
{
    public function up()
    {
        Schema::create('point_relais', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('libelle', 255);
            $table->integer('id_coursier')->unsigned();
            $table->integer('id_quartier')->unsigned();
            $table->string('quartier', 255)->nullable();
            $table->boolean('statut')->default(1);
        });
    }

    public function down()
    {
        Schema::drop('point_relais');
    }
}
