<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {

	public function run()
	{
    
	\App\Models\typeUtilisateur\TypeUtilisateur::create(array(
                'libelle' => 'Super Admin'
            ));

        // add_agent_type
    \App\Models\typeUtilisateur\TypeUtilisateur::create(array(
                'libelle' => 'Agent'
            ));

        // add_coursier_type
    \App\Models\typeUtilisateur\TypeUtilisateur::create(array(
                'libelle' => 'Coursier'
            ));

        // add_client_type
    \App\Models\typeUtilisateur\TypeUtilisateur::create(array(
                'libelle' => 'Client'
            ));
    $courante = app(\App\Tenancy\EntrepriseCourante::class);

        // Ville commune à toutes les entreprises
    $courante->sansFiltre(fn () => \App\Models\ville\Ville::create(array(
                'libelle' => 'Douala',
                'code' => 'Dla',
            )));

        // Entreprise de démonstration et son administrateur
    $entreprise = \App\Models\Entreprise::create(array(
                'name' => 'Speedex',
                'statut' => 1,
                'essai_jusqu_au' => now()->addDays(config('tenancy.essai_jours')),
            ));
    app(\App\Tenancy\InscriptionEntreprise::class)->creerAdministrateur(
                $entreprise, 'Admin Speedex', 'test@example.com', null, '11111111'
            )->markEmailAsVerified();

	}
}