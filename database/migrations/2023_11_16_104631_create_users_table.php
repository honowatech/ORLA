<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	public function up()
	{
		Schema::create('users', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('noms', 255)->default('user');
			$table->string('email', 255)->unique();
			$table->string('password', 255);
			$table->string('remember_token', 255)->nullable();
			$table->string('telephone', 255)->nullable();
			$table->integer('id_type_utilisateur')->unsigned();
			$table->integer('id_agent')->unsigned()->nullable();
			$table->integer('id_coursier')->unsigned()->nullable();
			$table->integer('id_client')->unsigned()->nullable();
			$table->boolean('statut')->default(1);
		});
	}

	public function down()
	{
		Schema::drop('users');
	}
}