<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDetailsZoneTable extends Migration
{
    public function up()
    {
        Schema::create('details_zone', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('id_coursier')->unsigned()->nullable();
            $table->integer('id_zone')->unsigned();
        });
    }

    public function down()
    {
        Schema::drop('details_zone');
    }
}
