<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQuartierTable extends Migration {

	public function up()
	{
		Schema::create('quartier', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('id_zone')->unsigned()->nullable();
			$table->string('libelle', 255);
			$table->integer('id_ville')->unsigned();
		});
	}

	public function down()
	{
		Schema::drop('quartier');
	}
}