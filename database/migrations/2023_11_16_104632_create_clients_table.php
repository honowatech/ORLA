<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClientsTable extends Migration {

	public function up()
	{
		Schema::create('clients', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('noms', 255)->nullable();
			$table->string('Prenoms', 255)->nullable();
			$table->integer('type_client')->unsigned();
			$table->integer('id_utilisateur')->unsigned()->nullable();
			$table->boolean('statut')->default(1);
			$table->string('telephone', 255)->nullable();
		});
	}

	public function down()
	{
		Schema::drop('clients');
	}
}