<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaiementTable extends Migration {

	public function up()
	{
		Schema::create('paiement', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->float('montant');
			$table->enum('mode_paiement', array('mobilemoney', 'orangemoney'));
			$table->integer('id_saver')->unsigned()->nullable()->constrained('users');
			$table->integer('id_client')->constrained('clients');
			$table->datetime('date_paiement');
			$table->date('date_commandes');
		});
	}

	public function down()
	{
		Schema::drop('paiement');
	}
}