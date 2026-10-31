<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\SuperAdmin\User;
use App\Models\SuperAdmin\Client;
use App\Models\SuperAdmin\Abonnement;
use App\Models\SuperAdmin\Contact;
use App\Tenancy\EntrepriseCourante;

class ErrorController extends Controller
{
    /**
     * Ces pages concernent uniquement l'entreprise de l'utilisateur connecté :
     * si sa situation ne correspond pas à la page demandée, on le renvoie à l'accueil.
     */
    public function empty_abonnement(EntrepriseCourante $courante)
    {
        $entreprise = $courante->entreprise();
        if (!$entreprise->estActive() || $entreprise->accesComplet() || $entreprise->id_abonnement != null) {
            return redirect(Sa_home());
        }
        return $this->page('superadmin.errors.empty_abonnement');
    }

    public function no_abonnement(EntrepriseCourante $courante)
    {
        $entreprise = $courante->entreprise();
        if (!$entreprise->estActive() || $entreprise->accesComplet() || $entreprise->id_abonnement == null) {
            return redirect(Sa_home());
        }
        return $this->page('superadmin.errors.no_abonnement');
    }

    public function full_error(Request $request)
    {
        return redirect(Sa_site_login());
    }

    public function site_inactif(EntrepriseCourante $courante)
    {
        if ($courante->entreprise()->estActive()) {
            return redirect(Sa_home());
        }
        return $this->page('superadmin.errors.client_inactif');
    }

    protected function page($vue)
    {
        $phones = Contact::where('name','phone')->first();
        $email = Contact::where('name','email')->first();
        return view($vue,compact('phones','email'));
    }
}
