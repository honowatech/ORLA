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
    \App\Models\ville\Ville::create(array(
                'libelle' => 'Douala',
                'code' => 'Dla',
            ));
    \App\Models\users\Users::create(array(
                'email' => 'test@example.com',
                'password' => Hash::make('11111111'),
                'id_type_utilisateur' => 1,
                'statut' => 1,
                'noms' => 'Admin Speedex'
            ));

	}
}