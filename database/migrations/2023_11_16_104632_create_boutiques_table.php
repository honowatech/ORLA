<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBoutiquesTable extends Migration {

	public function up()
	{
		Schema::create('boutiques', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('libelle', 255);
			$table->integer('id_client')->unsigned();
			$table->integer('id_quartier')->unsigned()->nullable();
			$table->string('quartier', 255)->nullable();
			$table->boolean('statut')->default(1);
		});
	}

	public function down()
	{
		Schema::drop('boutiques');
	}
}