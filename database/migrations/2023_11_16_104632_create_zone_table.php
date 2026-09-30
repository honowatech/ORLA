<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateZoneTable extends Migration
{
    public function up()
    {
        Schema::create('zone', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('libelle', 255);
            $table->integer('id_ville')->unsigned();
            $table->boolean('statut')->default(0);
        });
    }

    public function down()
    {
        Schema::drop('zone');
    }
}
