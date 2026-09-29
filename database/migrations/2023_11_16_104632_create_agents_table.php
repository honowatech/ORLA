<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAgentsTable extends Migration {

	public function up()
	{
		Schema::create('agents', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('noms', 255);
			$table->string('prenoms', 255);
			$table->integer('id_utilisateur')->unsigned()->nullable();
			$table->boolean('statut')->default(1);
			$table->text('telephone');
		});
	}

	public function down()
	{
		Schema::drop('agents');
	}
}