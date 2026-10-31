<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Models\SuperAdmin\User;

class SuperAdminController extends Controller
{
    //ouvrir la page de connexion du Super Admin
    public function login()
    {
        $check = check_superadmin();
        if($check == 'true'){
            return redirect()->route('SuperAdmin.home');
        }
        return view('superadmin.login');
    }
    public function connect(Request $request)
    {
        $check = check_superadmin();
        if($check == 'true'){
            return redirect()->route('SuperAdmin.home');
        }

        $email = $request->input('email'); 
        $password = $request->input('password');
        $user = User::where('email',$email)->first();
        $back = false;
        $check_password = false;
        $dernier_url = session()->has('dernier_url') ? session()->get('dernier_url') : route('SuperAdmin.home');
        if( $user == null ){
            $message = 'Nous ne trouvons pas votre email';
            $back = true;
        }else{
            $check_password = password_verify($password, $user->password);
            if($check_password == false){
                $message = 'Vos informations ne correspondent pas';
                $back = true;
            }
        }
        if($back == true){
            /////// L'utilisateur n'a pas pu se connecter...
            session()->flash('email',[
                                    'value'=>$email,
                                    'error'=>$message,
                                ]);
            return redirect()->back();
        }else{
            session()->put('SuperAdmin_infos',['connect'=>true,'infos'=>$user]);
        }
        //dd($dernier_url,$check_password,$email,$password,$user, $back);
        return redirect($dernier_url);
    }
    //////////////////: Déconnecter le Super Admin
    public function disconnect()
    {
        $check = check_superadmin();
        if($check != 'true'){
            return redirect()->route($check);
        }
        session()->forget('dernier_url');
        session()->forget('SuperAdmin_infos');
        return redirect()->route('SuperAdmin.login');
    }
    public function home()
    {
        $check = check_superadmin();
        if($check != 'true'){
            session()->put('dernier_url',url()->current());
            return redirect()->route($check);
        }
        $today = \Carbon\Carbon::now();
        $debut_mois = $today->copy()->startOfMonth();
        $debut_prec = $today->copy()->subMonth()->startOfMonth();
        $fin_prec = $today->copy()->subMonth()->endOfMonth();
        $clients = \App\Models\SuperAdmin\Client::with('abonnement')->orderBy('created_at', 'desc')->get();
        $transactions_mois = \App\Models\SuperAdmin\Transaction::where('statut', 'success')->where('created_at', '>=', $debut_mois)->get();
        $transactions_prec = \App\Models\SuperAdmin\Transaction::where('statut', 'success')->whereBetween('created_at', [$debut_prec, $fin_prec])->get();
        $stats = [
            'clients' => $clients->count(),
            'clients_actifs' => $clients->where('statut', 1)->count(),
            'clients_abonnes' => $clients->where('statut', 1)->filter(function ($c) use ($today) { return $c->date_fin != null && \Carbon\Carbon::parse($c->date_fin)->gte($today); })->count(),
            'clients_expires' => $clients->where('statut', 1)->filter(function ($c) use ($today) { return $c->date_fin != null && \Carbon\Carbon::parse($c->date_fin)->lt($today); })->count(),
            'clients_sans_abonnement' => $clients->where('statut', 1)->whereNull('id_abonnement')->count(),
            'abonnements' => \App\Models\SuperAdmin\Abonnement::where('statut', 1)->count(),
            'revenu_mois' => (float) $transactions_mois->sum('montant'),
            'revenu_prec' => (float) $transactions_prec->sum('montant'),
            'paiements_mois' => $transactions_mois->count(),
            'paiements_attente' => \App\Models\SuperAdmin\Transaction::where('statut', 'waiting')->count(),
            'paiements_echoues_mois' => \App\Models\SuperAdmin\Transaction::whereIn('statut', ['failed', 'cancelled'])->where('created_at', '>=', $debut_mois)->count(),
            'revenu_total' => (float) \App\Models\SuperAdmin\Transaction::where('statut', 'success')->sum('montant'),
        ];
        $echeances = $clients->where('statut', 1)->filter(function ($c) use ($today) {
            return $c->date_fin != null && \Carbon\Carbon::parse($c->date_fin)->lte($today->copy()->addDays(30));
        })->sortBy('date_fin')->values();
        $dernieres_transactions = \App\Models\SuperAdmin\Transaction::with(['client', 'abonnement'])->orderBy('created_at', 'desc')->take(8)->get();
        $repartition_abonnements = $clients->where('statut', 1)->whereNotNull('id_abonnement')
            ->groupBy(function ($c) { return $c->abonnement ? $c->abonnement->titre : 'Inconnu'; })
            ->map->count()->sortDesc();
        return view('superadmin.pages.home', compact('stats', 'echeances', 'dernieres_transactions', 'repartition_abonnements'));
    }
}
