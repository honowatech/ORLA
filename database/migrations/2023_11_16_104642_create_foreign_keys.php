<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class CreateForeignKeys extends Migration {

	public function up()
	{
		Schema::table('users', function(Blueprint $table) {
			$table->foreign('id_type_utilisateur')->references('id')->on('type_utilisateur')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('users', function(Blueprint $table) {
			$table->foreign('id_agent')->references('id')->on('agents')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('users', function(Blueprint $table) {
			$table->foreign('id_coursier')->references('id')->on('coursiers')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('users', function(Blueprint $table) {
			$table->foreign('id_client')->references('id')->on('clients')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('clients', function(Blueprint $table) {
			$table->foreign('type_client')->references('id')->on('type_client')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('clients', function(Blueprint $table) {
			$table->foreign('id_utilisateur')->references('id')->on('users')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('coursiers', function(Blueprint $table) {
			$table->foreign('id_utilisateur')->references('id')->on('users')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('agents', function(Blueprint $table) {
			$table->foreign('id_utilisateur')->references('id')->on('users')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('zone', function(Blueprint $table) {
			$table->foreign('id_ville')->references('id')->on('ville')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('details_zone', function(Blueprint $table) {
			$table->foreign('id_coursier')->references('id')->on('coursiers')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('details_zone', function(Blueprint $table) {
			$table->foreign('id_zone')->references('id')->on('zone')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('quartier', function(Blueprint $table) {
			$table->foreign('id_zone')->references('id')->on('zone')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('quartier', function(Blueprint $table) {
			$table->foreign('id_ville')->references('id')->on('ville')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('boutiques', function(Blueprint $table) {
			$table->foreign('id_client')->references('id')->on('clients')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('boutiques', function(Blueprint $table) {
			$table->foreign('id_quartier')->references('id')->on('quartier')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('point_relais', function(Blueprint $table) {
			$table->foreign('id_coursier')->references('id')->on('coursiers')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('point_relais', function(Blueprint $table) {
			$table->foreign('id_quartier')->references('id')->on('quartier')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('stock', function(Blueprint $table) {
			$table->foreign('id_produit')->references('id')->on('produits')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('stock', function(Blueprint $table) {
			$table->foreign('id_boutique')->references('id')->on('boutiques')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('stock', function(Blueprint $table) {
			$table->foreign('id_point_relais')->references('id')->on('point_relais')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('commandes', function(Blueprint $table) {
			$table->foreign('id_client')->references('id')->on('clients')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('commandes', function(Blueprint $table) {
			$table->foreign('id_boutique')->references('id')->on('boutiques')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('commandes', function(Blueprint $table) {
			$table->foreign('id_coursier')->references('id')->on('coursiers')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('commandes', function(Blueprint $table) {
			$table->foreign('id_point_relais')->references('id')->on('point_relais')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('commandes', function(Blueprint $table) {
			$table->foreign('id_saver')->references('id')->on('users')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('commandes', function(Blueprint $table) {
			$table->foreign('id_quartier_colis')->references('id')->on('quartier')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('commandes', function(Blueprint $table) {
			$table->foreign('id_quartier_livraison')->references('id')->on('quartier')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('commandes', function(Blueprint $table) {
			$table->foreign('id_montant_livraison')->references('id')->on('montant_livraison')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('montant_livraison', function(Blueprint $table) {
			$table->foreign('id_zone_colis')->references('id')->on('zone')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('montant_livraison', function(Blueprint $table) {
			$table->foreign('id_zone_livraison')->references('id')->on('zone')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('details_commande', function(Blueprint $table) {
			$table->foreign('id_commande')->references('id')->on('commandes')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('details_commande', function(Blueprint $table) {
			$table->foreign('id_produit')->references('id')->on('produits')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('informations_personnels', function(Blueprint $table) {
			$table->foreign('id_agent')->references('id')->on('agents')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('informations_personnels', function(Blueprint $table) {
			$table->foreign('id_client')->references('id')->on('clients')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('informations_personnels', function(Blueprint $table) {
			$table->foreign('id_coursier')->references('id')->on('coursiers')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('informations_personnels', function(Blueprint $table) {
			$table->foreign('id_quartier')->references('id')->on('quartier')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
	}

	public function down()
	{
		Schema::table('users', function(Blueprint $table) {
			$table->dropForeign('users_id_type_utilisateur_foreign');
		});
		Schema::table('users', function(Blueprint $table) {
			$table->dropForeign('users_id_agent_foreign');
		});
		Schema::table('users', function(Blueprint $table) {
			$table->dropForeign('users_id_coursier_foreign');
		});
		Schema::table('users', function(Blueprint $table) {
			$table->dropForeign('users_id_client_foreign');
		});
		Schema::table('clients', function(Blueprint $table) {
			$table->dropForeign('clients_type_client_foreign');
		});
		Schema::table('clients', function(Blueprint $table) {
			$table->dropForeign('clients_id_utilisateur_foreign');
		});
		Schema::table('coursiers', function(Blueprint $table) {
			$table->dropForeign('coursiers_id_utilisateur_foreign');
		});
		Schema::table('agents', function(Blueprint $table) {
			$table->dropForeign('agents_id_utilisateur_foreign');
		});
		Schema::table('zone', function(Blueprint $table) {
			$table->dropForeign('zone_id_ville_foreign');
		});
		Schema::table('details_zone', function(Blueprint $table) {
			$table->dropForeign('details_zone_id_coursier_foreign');
		});
		Schema::table('details_zone', function(Blueprint $table) {
			$table->dropForeign('details_zone_id_zone_foreign');
		});
		Schema::table('quartier', function(Blueprint $table) {
			$table->dropForeign('quartier_id_zone_foreign');
		});
		Schema::table('quartier', function(Blueprint $table) {
			$table->dropForeign('quartier_id_ville_foreign');
		});
		Schema::table('boutiques', function(Blueprint $table) {
			$table->dropForeign('boutiques_id_client_foreign');
		});
		Schema::table('boutiques', function(Blueprint $table) {
			$table->dropForeign('boutiques_id_quartier_foreign');
		});
		Schema::table('point_relais', function(Blueprint $table) {
			$table->dropForeign('point_relais_id_coursier_foreign');
		});
		Schema::table('point_relais', function(Blueprint $table) {
			$table->dropForeign('point_relais_id_quartier_foreign');
		});
		Schema::table('stock', function(Blueprint $table) {
			$table->dropForeign('stock_id_produit_foreign');
		});
		Schema::table('stock', function(Blueprint $table) {
			$table->dropForeign('stock_id_boutique_foreign');
		});
		Schema::table('stock', function(Blueprint $table) {
			$table->dropForeign('stock_id_point_relais_foreign');
		});
		Schema::table('commandes', function(Blueprint $table) {
			$table->dropForeign('commandes_id_client_foreign');
		});
		Schema::table('commandes', function(Blueprint $table) {
			$table->dropForeign('commandes_id_boutique_foreign');
		});
		Schema::table('commandes', function(Blueprint $table) {
			$table->dropForeign('commandes_id_coursier_foreign');
		});
		Schema::table('commandes', function(Blueprint $table) {
			$table->dropForeign('commandes_id_point_relais_foreign');
		});
		Schema::table('commandes', function(Blueprint $table) {
			$table->dropForeign('commandes_id_saver_foreign');
		});
		Schema::table('commandes', function(Blueprint $table) {
			$table->dropForeign('commandes_id_quartier_colis_foreign');
		});
		Schema::table('commandes', function(Blueprint $table) {
			$table->dropForeign('commandes_id_quartier_livraison_foreign');
		});
		Schema::table('commandes', function(Blueprint $table) {
			$table->dropForeign('commandes_id_montant_livraison_foreign');
		});
		Schema::table('montant_livraison', function(Blueprint $table) {
			$table->dropForeign('montant_livraison_id_zone_colis_foreign');
		});
		Schema::table('montant_livraison', function(Blueprint $table) {
			$table->dropForeign('montant_livraison_id_zone_livraison_foreign');
		});
		Schema::table('details_commande', function(Blueprint $table) {
			$table->dropForeign('details_commande_id_commande_foreign');
		});
		Schema::table('details_commande', function(Blueprint $table) {
			$table->dropForeign('details_commande_id_produit_foreign');
		});
		Schema::table('informations_personnels', function(Blueprint $table) {
			$table->dropForeign('informations_personnels_id_agent_foreign');
		});
		Schema::table('informations_personnels', function(Blueprint $table) {
			$table->dropForeign('informations_personnels_id_client_foreign');
		});
		Schema::table('informations_personnels', function(Blueprint $table) {
			$table->dropForeign('informations_personnels_id_coursier_foreign');
		});
		Schema::table('informations_personnels', function(Blueprint $table) {
			$table->dropForeign('informations_personnels_id_quartier_foreign');
		});
	}
}