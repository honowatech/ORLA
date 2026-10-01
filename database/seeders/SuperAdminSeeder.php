<?php

namespace Database\Seeders;

use App\Models\SuperAdmin\Api;
use App\Models\SuperAdmin\Contact;
use App\Models\SuperAdmin\User as SuperAdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Compte opérateur de la plateforme, passerelle de paiement et coordonnées de contact.
 * Idempotent : ne modifie pas un enregistrement déjà présent.
 */
class SuperAdminSeeder extends Seeder
{
    public const EMAIL = 'superadmin@example.com';

    public function run(): void
    {
        SuperAdminUser::firstOrCreate(
            ['email' => self::EMAIL],
            ['name' => 'Honowa Technologies', 'password' => Hash::make('11111111')]
        );

        Api::firstOrCreate(
            ['name' => 'Monetbill'],
            ['key' => null, 'secret' => null, 'user' => null, 'password' => null, 'statut' => 1]
        );

        Contact::firstOrCreate(['name' => 'phone'], ['value' => '674970806/695510761']);
        Contact::firstOrCreate(['name' => 'email'], ['value' => 'contact@honowa.com']);
    }
}
