<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTypeClientTable extends Migration {

	public function up()
	{
		Schema::create('type_client', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('libelle', 255);
		});
	}

	public function down()
	{
		Schema::drop('type_client');
	}
}