<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\SuperAdmin\Client;
use App\Models\SuperAdmin\Contact;
use Illuminate\Http\Request;

class ErrorController extends Controller
{
    public function empty_abonnement(Request $request)
    {
        $clients = Client::get();
        foreach ($clients as $client) {
            if ($client->id_abonnement != null || $clients->count() == 0 || Sa_statut($clients)) {
                return redirect(Sa_site_login());
            }
        }
        $phones = Contact::where('name', 'phone')->first();
        $email = Contact::where('name', 'email')->first();

        return view('superadmin.errors.empty_abonnement', compact('phones', 'email'));
    }

    public function no_abonnement(Request $request)
    {
        $clients = Client::get();
        $check = Sa_check_abonnement($clients);
        if ($check != 'true' || $clients->count() == 0 || Sa_statut($clients)) {
            return redirect(Sa_site_login());
        }
        $phones = Contact::where('name', 'phone')->first();
        $email = Contact::where('name', 'email')->first();

        return view('superadmin.errors.no_abonnement', compact('phones', 'email'));
    }

    public function full_error(Request $request)
    {
        $clients = Client::get();
        if ($clients->count() > 0) {
            return redirect(Sa_site_login());
        }
        $phones = Contact::where('name', 'phone')->first();
        $email = Contact::where('name', 'email')->first();

        return view('superadmin.errors.empty_client', compact('phones', 'email'));
    }

    public function site_inactif(Request $request)
    {
        $clients = Client::get();
        foreach ($clients as $client) {
            if ($client->statut == 1) {
                return redirect(Sa_site_login());
            }
        }
        $phones = Contact::where('name', 'phone')->first();
        $email = Contact::where('name', 'email')->first();

        return view('superadmin.errors.client_inactif', compact('phones','email'));
    }
}
