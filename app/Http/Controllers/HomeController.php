<?php

namespace App\Http\Controllers;
use App\Models\typeUtilisateur\TypeUtilisateur;
use App\Models\commandes\Commandes;
use App\Models\users\Users;
use App\Models\coursiers\Coursiers;
use Carbon\Carbon;
use App\Models\clients\Clients;
use App\Models\Activity;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
        $statut = Auth()->user()->statut;
        if ($statut == 0) {
            return redirect()->route('home.error');
        }
        if($type == strtoupper('Super Admin') || $type == strtoupper('agent')){

            return redirect()->route('home.admin');

        }else if($type == strtoupper('coursier')){

            return redirect()->route('home.coursier');

        }else if($type == strtoupper('client')){

            return redirect()->route('home.client');
        }else{
            return view('errors.404');
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function admin()
    {
        $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
        $statut = Auth()->user()->statut;
        if ($statut == 0) {
            return redirect()->route('home.error');
        }
        if($type == strtoupper('Super Admin') || $type == strtoupper('agent')){

        }else if($type == strtoupper('coursier')){

            return redirect()->route('home.coursier');

        }else if($type == strtoupper('client')){

            return redirect()->route('home.client');
        }
        $debut_mois = Carbon::now()->startOfMonth();
        $debut_mois_precedent = Carbon::now()->subMonth()->startOfMonth();
        $fin_mois_precedent = Carbon::now()->subMonth()->endOfMonth();
        $datas = [
            'coursiers' => Coursiers::where('statut',[1])->get(),
            'clients_simple' => Clients::where('type_client',[2])->where('statut',[1])->get(),
            'clients_entreprise' => Clients::where('type_client',[1])->where('statut',[1])->get(),
            'commandes_reussies' => Commandes::where('statut','livre')->get(),
            'commandes_reussies_du_mois' => Commandes::whereMonth('date_livre', Carbon::now()->month)->whereYear('date_livre', Carbon::now()->year)->where('statut','livre')->get(),
            'commandes_reussies_mois_precedent' => Commandes::whereBetween('date_livre', [$debut_mois_precedent, $fin_mois_precedent])->where('statut','livre')->get(),
            'commandes_attente' => Commandes::where('statut','attente')->count(),
            'commandes_attribuees' => Commandes::where('statut','attribue')->count(),
            'commandes_encours' => Commandes::where('statut','encours')->count(),
            'commandes_du_jour' => Commandes::whereDate('date_livraison', Carbon::today())->whereIn('statut',['attente','attribue','encours'])->count(),
            'commandes_echouees_mois' => Commandes::where('created_at', '>=', $debut_mois)->whereIn('statut',['annulee','echoue'])->count(),
            'commandes_mois' => Commandes::where('created_at', '>=', $debut_mois)->whereNotIn('statut',['supprime'])->count(),
            'dernieres_commandes' => Commandes::with(['client','coursier','quartier_livraison'])->whereNotIn('statut',['supprime'])->orderBy('created_at','desc')->take(6)->get(),
            'top_coursiers' => Commandes::selectRaw('id_coursier, count(*) as total')->where('statut','livre')->where('date_livre', '>=', $debut_mois)->whereNotNull('id_coursier')->groupBy('id_coursier')->orderByDesc('total')->take(5)->with('coursier')->get(),
        ];
        $vehicules = \App\Models\Vehicule::where('statut', 1)->with(['type', 'coursier'])->get();
        $parc = [
            'actifs' => $vehicules->count(),
            'disponibles' => $vehicules->where('etat', 'disponible')->count(),
            'indisponibles' => $vehicules->whereIn('etat', ['en_maintenance', 'immobilise', 'hors_service'])->count(),
            'alertes' => app(\App\Services\ParcService::class)->alertes($vehicules),
        ];
        return view('admin.pages.home',compact('datas', 'parc'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function coursier(Request $request)
    {
        $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
        $statut = Auth()->user()->statut;
        if ($statut == 0) {
            return redirect()->route('home.error');
        }
        if($type == strtoupper('Super Admin') || $type == strtoupper('agent')){

            return redirect()->route('home.admin');

        }else if($type == strtoupper('coursier')){

        }else if($type == strtoupper('client')){

            return redirect()->route('home.client');
        }
        $date = $request->input('date') == null ? now() : $request->input('date');
        $commandes = Commandes::where('id_coursier',[Auth()->user()->coursier_utilisateur->id])->whereDate('date_livraison', '<=',$date)->whereIn('statut',['encours','attribue'])->orderBy('id_quartier_livraison')->orderBy('statut','desc')->get();
        $commandes2 = Commandes::where('id_coursier',[Auth()->user()->coursier_utilisateur->id])->whereDate('date_livre',$date)->whereIn('statut',['livre','annulee'])->orderBy('id_quartier_livraison')->orderBy('statut','desc')->get();
        $activities = Activity::where('id_user',[Auth()->user()->coursier_utilisateur->id])->whereDate('created_at',$date)->orderBy('created_at','desc')->get();
        return view('coursier.pages.home',compact('commandes','activities','commandes2'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function clients(Request $request)
    {
        $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
        $statut = Auth()->user()->statut;
        if ($statut == 0) {
            // auth()->logout();
            return redirect()->route('home.error');
        }
        if($type == strtoupper('Super Admin') || $type == strtoupper('agent')){

            return redirect()->route('home.admin');

        }else if($type == strtoupper('coursier')){

            return redirect()->route('home.coursier');

        }else if($type == strtoupper('client')){

        }
        //return view('coursier.pages.home');
        $date = $request->input('date') == null ? now() : $request->input('date');
        $commandes = Commandes::where('id_client',[Auth()->user()->client_utilisateur->id])->whereIn('statut',['encours','attribue','attente'])->orderBy('id_quartier_livraison')->orderBy('statut','desc')->orderBy('created_at','desc')->get();
        return view('client.pages.home',compact('commandes'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function error()
    {
        return 'Votre compte est désactivé';
    }
}
