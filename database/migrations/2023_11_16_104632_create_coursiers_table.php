<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCoursiersTable extends Migration {

	public function up()
	{
		Schema::create('coursiers', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('noms', 255);
			$table->string('prenoms', 255);
			$table->integer('id_utilisateur')->unsigned()->nullable();
			$table->boolean('statut')->default(0);
			$table->string('telephone', 255);
		});
	}

	public function down()
	{
		Schema::drop('coursiers');
	}
}