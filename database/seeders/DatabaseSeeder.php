<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Référentiels et compte opérateur dans tous les environnements ;
     * données de démonstration uniquement en local et en test.
     */
    public function run(): void
    {
        $this->call([
            ReferentielSeeder::class,
            SuperAdminSeeder::class,
        ]);

        if (app()->environment('local', 'testing')) {
            $this->call(DemoSeeder::class);
        }
    }
}
