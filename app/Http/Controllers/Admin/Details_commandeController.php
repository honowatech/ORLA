<?php

namespace App\Http\Controllers\Admin;

use App\Models\Clients\Clients;
use App\Models\Commandes\Commandes;
use App\Models\Paiement\Paiement;
use App\Models\TypeClient\TypeClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Details_commandeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $types_client = TypeClient::get();
        $commandes = Commandes::get();

        return view('admin.pages.details_commande.layout', compact('commandes', 'types_client'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $id_type_client = $request->input('table_data')[0];
        $clients = Clients::where('type_client', [$id_type_client])->get();

        // dd($clients);
        return view('admin.pages.details_commande.client', compact('clients'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $id_client = $request->input('table_data')[0];
        $date = $request->input('table_data')[1];
        // dd($date);
        // ////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($id_client == null || $date == null) {
            return '<center> <b class="text-danger"> Attention ! </b> Veuillez remplir les informatios </center>';
        }
        // ////////////////////////////////////////////////////////////////////////////////////////////////////
        $type_client = Clients::findOrFail($id_client)->type__client->libelle;
        if (strtoupper($type_client) == strtoupper('Simple')) {
            $titre = true;
        } else {
            $titre = false;
        }
        $commandes = Commandes::where('id_client', $id_client)
            ->where('statut', 'livre')
            ->whereDate('date_livre', $date)
            ->get();
        // ////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($commandes->count() == 0) {
            return '<center><b> Aucune information disponible </b></center>';
        }
        // ////////////////////////////////////////////////////////////////////////////////////////////////////
        $montant_recuperer = 0;
        $montant_livraison = 0;
        $dette_client = 0;
        $dette_speedex = 0;
        foreach ($commandes as $commande) {
            $montant_recuperer += $commande->montant_recuperer;
            $montant_livraison += $commande->montant_livraison;
        }
        $paiements = Paiement::where('id_client', $id_client)
            ->whereDate('date_commandes', $date)
            ->get();
        foreach ($paiements as $paiement) {
            if ($paiement->qui_paie == 'client') {
                $dette_client += $paiement->montant;
            } else {
                $dette_speedex += $paiement->montant;
            }
        }
        $client = Clients::findOrFail($id_client);
        $argent_du = $montant_recuperer - $montant_livraison + $dette_client - $dette_speedex;

        return view('admin.pages.details_commande.result', compact('titre', 'commandes', 'argent_du', 'client', 'date', 'paiements'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request)
    {
        $cles = ['montant', 'date', 'id_client', 'qui_paie', 'mode_paiement', 'telephone2'];
        $donnees = $request->input('data');
        $error = false;
        foreach ($donnees as $key => $value) {
            if ($donnees['telephone2'] == 'Speedex') {
                if ($value == '' || ! in_array($key, $cles) || $value == null) {
                    $error = true;
                    break;
                }
            } else {
                if ($value == '' || ! in_array($key, $cles) || $value == null || strlen($donnees['telephone2']) != 9 || $donnees['telephone2'][0] != 6) {
                    $error = true;
                    break;
                }
            }
        }
        if (empty($donnees) || $error) {
            $message = '<span class="text-danger text-center">Echec </span> du paiement';
            $statut = false;
            $table = [
                'message' => $message,
                'statut' => $statut,
            ];

            return $table;
        }
        $message = '<span class="text-success text-center">Succès </span> du paiement';
        $statut = true;
        $paiement = new Paiement;
        $paiement->montant = $donnees['montant'];
        $paiement->mode_paiement = $donnees['mode_paiement'];
        $paiement->id_saver = Auth::user()->id;
        $paiement->id_client = $donnees['id_client'];
        $paiement->date_paiement = now();
        $paiement->date_commandes = $donnees['date'];
        $paiement->qui_paie = $donnees['qui_paie'];
        $paiement->telephone = $donnees['telephone2'];
        $paiement->save();
        $table = [
            'message' => $message,
            'statut' => $statut,
        ];

        return $table;
    }
}
