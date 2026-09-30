<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Clients\Clients;
use App\Models\Commandes\Commandes;
use App\Models\Coursiers\Coursiers;
use App\Models\TypeUtilisateur\TypeUtilisateur;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
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
     * @return Renderable
     */
    public function index()
    {
        $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
        $statut = Auth()->user()->statut;
        if ($statut == 0) {
            return redirect()->route('home.error');
        }
        if ($type == strtoupper('Super Admin') || $type == strtoupper('agent')) {

            return redirect()->route('home.admin');

        } elseif ($type == strtoupper('coursier')) {

            return redirect()->route('home.coursier');

        } elseif ($type == strtoupper('client')) {

            return redirect()->route('home.client');
        } else {
            return view('errors.404');
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function admin()
    {
        $datas = [
            'coursiers' => Coursiers::where('statut', [1])->get(),
            'clients_simple' => Clients::where('type_client', [2])->where('statut', [1])->get(),
            'clients_entreprise' => Clients::where('type_client', [1])->where('statut', [1])->get(),
            'commandes_reussies' => Commandes::where('statut', 'livre')->get(),
            'commandes_reussies_du_mois' => Commandes::whereMonth('date_livre', Carbon::now()->month)->where('statut', 'livre')->get(),
        ];

        return view('admin.pages.home', compact('datas'));
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function coursier(Request $request)
    {
        $date = $request->input('date') == null ? now() : $request->input('date');
        $commandes = Commandes::where('id_coursier', [Auth()->user()->coursier_utilisateur->id])->whereDate('date_livraison', '<=', $date)->whereIn('statut', ['encours', 'attribue'])->orderBy('id_quartier_livraison')->orderBy('statut', 'desc')->get();
        $commandes2 = Commandes::where('id_coursier', [Auth()->user()->coursier_utilisateur->id])->whereDate('date_livre', $date)->whereIn('statut', ['livre', 'annulee'])->orderBy('id_quartier_livraison')->orderBy('statut', 'desc')->get();
        $activities = Activity::where('id_user', [Auth()->user()->coursier_utilisateur->id])->whereDate('created_at', $date)->orderBy('created_at', 'desc')->get();

        return view('coursier.pages.home', compact('commandes', 'activities', 'commandes2'));
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function clients(Request $request)
    {
        $date = $request->input('date') == null ? now() : $request->input('date');
        $commandes = Commandes::where('id_client', [Auth()->user()->client_utilisateur->id])->whereIn('statut', ['encours', 'attribue', 'attente'])->orderBy('id_quartier_livraison')->orderBy('statut', 'desc')->orderBy('created_at', 'desc')->get();

        return view('client.pages.home', compact('commandes'));
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function error()
    {
        return 'Votre compte est désactivé';
    }
}
