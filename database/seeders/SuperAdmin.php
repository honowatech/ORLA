<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
class SuperAdmin extends Seeder {

	public function run()
	{
        \App\Models\SuperAdmin\User::create([
            'password' => Hash::make('11111111'),
            'name' => 'Honowa Technologies',
            'email' => 'superadmin@example.com',
        ]);
        \App\Models\SuperAdmin\Api::create([
            'name'=> 'Monetbill',
            'key'=> null,
            'secret'=> null,
            'user'=> null,
            'password'=> null,
            'statut'=> 1,
        ]);
        \App\Models\SuperAdmin\Contact::create([
            'name'=> 'phone',
            'value'=> '674970806/695510761',
        ]);
        \App\Models\SuperAdmin\Contact::create([
            'name'=> 'email',
            'value'=> 'contact@honowa.com',
        ]);
    }
}
