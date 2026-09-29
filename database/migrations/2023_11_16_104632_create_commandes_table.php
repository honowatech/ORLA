<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommandesTable extends Migration {

	public function up()
	{
		Schema::create('commandes', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('id_client')->unsigned()->nullable();
			$table->string('telephone', 255)->nullable();
			$table->string('nom_client',255)->nullable();
			$table->integer('id_boutique')->unsigned()->nullable();
			$table->integer('id_coursier')->unsigned()->nullable();
			$table->integer('id_point_relais')->unsigned()->nullable();
			$table->integer('id_saver')->unsigned();
			$table->datetime('date_commande');
			$table->enum('type_commande', array('simple', 'entreprise'));
			$table->string('adresse_colis', 255)->nullable();
			$table->integer('id_quartier_colis')->unsigned()->nullable();
			$table->string('adresse_livraison', 255)->nullable();
			$table->integer('id_quartier_livraison')->unsigned()->nullable();
			$table->float('montant_livraison')->nullable();
			$table->integer('id_montant_livraison')->unsigned()->nullable();
			$table->float('montant_recuperer')->nullable();
			$table->datetime('date_mise_encours')->nullable();
			$table->text('description')->nullable();
			$table->datetime('date_livre')->nullable();
			$table->datetime('date_livraison');
			$table->enum('mode_de_paiement', array('coursier', 'speedex'));
			$table->enum('statut', array('attente', 'attribue', 'encours', 'livre', 'annulee', 'supprime','echoue'));
		});
	}

	public function down()
	{
		Schema::drop('commandes');
	}
}