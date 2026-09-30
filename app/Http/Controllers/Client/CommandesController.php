<?php

namespace App\Http\Controllers\Client;

use App\Models\Boutiques\Boutiques;
use App\Models\Clients\Clients;
use App\Models\Commandes\Commandes;
use App\Models\Coursiers\Coursiers;
use App\Models\Details_commande\Details_commande;
use App\Models\Montant_livraison\Montant_livraison;
use App\Models\Produits\Produits;
use App\Models\Quartier\Quartier;
use App\Models\TypeClient\TypeClient;
use App\Models\Ville\Ville;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CommandesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if (Quartier::where(strtoupper('libelle'), strtoupper('Speedex'))->count() == 0) {
            $quartier = new Quartier;
            $quartier->libelle = 'Speedex';
            $quartier->id_ville = 1;
            $quartier->save();
        }
        $coursiers = Coursiers::where('statut', [1])->get();
        $statut = $request->input('statut') == null || $request->input('statut') == 'all' ? '' : $request->input('statut');
        $mode_de_paiement = $request->input('mode_de_paiement') == null || $request->input('mode_de_paiement') == 'all' ? '' : $request->input('mode_de_paiement');
        $contenu = [
            'attente' => 'primary/clock/En attente',
            'attribue' => 'info/user-check/Attribuée',
            'encours' => 'dark/loader/En cours',
            'livre' => 'success/check/Livrée',
            'annulee' => 'danger/alert-triangle/Annulée',
            'echoue' => 'warning/slash/Echouée',
        ];
        $ids = [];
        $statuts = ['attente', 'attribue', 'encours', 'livre', 'annulee', 'echoue'];
        $modes_de_paiement = ['coursier', 'speedex'];
        $commandes_select = Commandes::where('statut', 'like', '%'.$statut.'%')
            ->where('mode_de_paiement', 'like', '%'.$mode_de_paiement.'%')
            ->orderBy('statut')
            ->get();
        $commandes_search = Commandes::where('nom_client', 'like', '%'.$request->input('search').'%')
            ->orWhere('telephone', 'like', '%'.$request->input('search').'%')
            ->orWhere('montant_livraison', 'like', '%'.$request->input('search').'%')
            ->orWhere('date_livraison', 'like', '%'.$request->input('search').'%')
            ->orWhere('type_commande', 'like', '%'.$request->input('search').'%')
            ->orWhere('adresse_colis', 'like', '%'.$request->input('search').'%')
            ->orWhere('adresse_livraison', 'like', '%'.$request->input('search').'%')
            ->orderBy('statut')
            ->get();
        foreach ($commandes_search as $commande_search) {
            foreach ($commandes_select as $commande_select) {
                if ($commande_select->id == $commande_search->id) {
                    array_push($ids, $commande_search->id);
                }
            }
        }
        $commandes = Commandes::whereIn('id', $ids)
            ->where('id_client', [Auth()->user()->client_utilisateur->id])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
        session()->flash('statut', $request->input('statut'));
        session()->flash('mode_de_paiement', $request->input('mode_de_paiement'));
        session()->flash('search', $request->input('search'));

        return view('client.pages.commandes.index', compact('commandes', 'contenu', 'modes_de_paiement', 'statuts', 'coursiers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function boutiqueLie(Request $request)
    {
        // Un client ne voit que ses propres boutiques.
        $id_client = Auth()->user()->id_client;
        abort_if($id_client === null, 403);
        $client = Clients::findOrFail($id_client);
        if (Quartier::where(strtoupper('libelle'), strtoupper('Speedex'))->count() == 0) {
            $quartier = new Quartier;
            $quartier->libelle = 'Speedex';
            $quartier->id_ville = 1;
            $quartier->save();
        } else {
            $id_quartier = Quartier::where(strtoupper('libelle'), strtoupper('Speedex'))
                ->get('id')
                ->value('id');
            $quartier = Quartier::findOrFail($id_quartier);
        }
        $boutiques = Boutiques::where('id_client', [$id_client])
            ->where('statut', [1])
            ->get();
        foreach ($boutiques as $boutique) {
            if (strtoupper($boutique->libelle) == strtoupper('Speedex') && $boutique->id_quartier != $quartier->id) {
                $boutique_change = Boutiques::findOrFail($boutique->id);
                $boutique_change->id_quartier = $quartier->id;
                $boutique_change->save();
            }
        }

        return view('client.pages.commandes.boutiqueLie', compact('boutiques', 'client'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function commandeClient(Request $request)
    {
        $type_client = TypeClient::where(strtoupper('libelle'), strtoupper($request->input('type_commande')))->limit(1)->get('id')->value('id');
        $clients = Clients::where('telephone', 'like', '%'.$request->input('search').'%')
            ->where('statut', [1])
            ->where('type_client', $type_client)
            ->get();
        if ($clients->count() == 0) {
            return "<b class='text-danger'> Aucune valeur ne correspond</b>";
        }

        return view('client.pages.commandes.client', compact('clients'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function lieu(Request $request)
    {
        $ville_collecte = $request->input('ville_collecte');
        $quartiers = Quartier::where('libelle', 'like', '%'.$request->input('search').'%')
            ->where('id_ville', $ville_collecte)
            ->get();
        if ($quartiers->count() == 0) {
            return "<b class='text-danger'> Aucune valeur ne correspond</b>";
        }
        $id_quartier = 'id_quartier_colis';
        $lieu = 'lieu_collecte';

        return view('client.pages.commandes.lieu', compact('quartiers', 'id_quartier', 'lieu'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function montant(Request $request)
    {
        return $this->tarifLivraison($request->input('id_depart'), $request->input('id_arrivee'));
    }

    /**
     * Frais de livraison entre deux quartiers, selon leurs zones.
     * Renvoie null si aucun tarif n'est défini entre ces zones.
     */
    private function tarifLivraison($id_depart, $id_arrivee)
    {
        if ($id_depart == null || $id_arrivee == null || $id_depart == $id_arrivee) {
            return 1000;
        }
        $quartiers_depart = Quartier::findOrFail($id_depart);
        $quartiers_arrivee = Quartier::findOrFail($id_arrivee);
        if ($quartiers_depart->zone == $quartiers_arrivee->zone || $quartiers_depart->zone == null || $quartiers_arrivee->zone == null) {
            return 1000;
        }

        return Montant_livraison::whereIn('id_zone_colis', [$quartiers_depart->zone->id, $quartiers_arrivee->zone->id])
            ->whereIn('id_zone_livraison', [$quartiers_depart->zone->id, $quartiers_arrivee->zone->id])
            ->limit(1)
            ->get('montant')
            ->value('montant');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function lieu2(Request $request)
    {
        $ville_livraison = $request->input('ville_livraison');
        $quartiers = Quartier::where('libelle', 'like', '%'.$request->input('search').'%')
            ->where('id_ville', $ville_livraison)
            ->get();
        if ($quartiers->count() == 0) {
            return "<b class='text-danger'> Aucune valeur ne correspond</b>";
        }
        $id_quartier = 'id_quartier_livraison';
        $lieu = 'lieu_livraison';

        return view('client.pages.commandes.lieu', compact('quartiers', 'id_quartier', 'lieu'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $client = Auth()->user()->client_utilisateur;
        $modes_de_paiement = ['coursier', 'speedex'];
        $entreprises = Clients::where('statut', [1])
            ->where('type_client', [1])
            ->get();
        $produits = Produits::where('statut', [1])
            ->get();
        $villes = Ville::orderBy('created_at')
            ->get();
        if (Quartier::where(strtoupper('libelle'), strtoupper('Speedex'))->count() == 0) {
            $quartier = new Quartier;
            $quartier->libelle = 'Speedex';
            $quartier->id_ville = 1;
            $quartier->save();
        }

        return view('client.pages.commandes.create', compact('modes_de_paiement', 'entreprises', 'produits', 'villes', 'client'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        // ici nous déclarons toutes les variables
        $adresse_colis = $request->input('contact_colis').'*/*'.$request->input('lieu_collecte').'*/*'.$request->input('description_collecte');
        $adresse_livraison = $request->input('contact_livraison').'*/*'.$request->input('lieu_livraison').'*/*'.$request->input('description_livraison').'*/*'.$request->input('nom_livraison');
        $type_commande = $request->input('type_commande');
        session()->flash('type_commande', $type_commande);
        $telephone = $request->input('telephone');
        // La commande est toujours rattachée au client connecté.
        $id_client = Auth()->user()->id_client;
        abort_if($id_client === null, 403);
        $id_client2 = $request->input('id_client2');
        $entreprise = $request->input('entreprise');
        $nom_client = $request->input('nom_client');
        $contact_colis = $request->input('contact_colis');
        $lieu_collecte = $request->input('lieu_collecte');
        $ville_livraison = $request->input('ville_livraison');
        $id_quartier_colis = $request->input('id_quartier_colis');
        $description_collecte = $request->input('description_collecte');
        $contact_livraison = $request->input('contact_livraison');
        $ville_collecte = $request->input('ville_collecte');
        $lieu_livraison = $request->input('lieu_livraison');
        $id_quartier_livraison = $request->input('id_quartier_livraison');
        $description_livraison = $request->input('description_livraison');
        $montant_livraison = $request->input('montant_livraison');
        $montant_collecter = $request->input('montant_collecter');
        $mode_de_paiement = $request->input('mode_de_paiement');
        $date = $request->input('date');
        $time = $request->input('time');
        $id_boutique = $request->input('id_boutique');
        $id_produit = $request->input('produit');
        $quantite = $request->input('quantite');
        $description = $request->input('description');
        session()->put('id_boutique', $id_boutique);
        // ici on fait les vérifications communes aux deux formulaires
        $validated = $request->validate([
            'type_commande' => 'bail|required|max:255',
            'contact_colis' => 'bail|required|regex:/^[6,2][0-9]{8}$/',
            'lieu_collecte' => 'bail|required|min:3',
            'contact_livraison' => 'bail|required|regex:/^[6,2][0-9]{8}$/',
            'lieu_livraison' => 'bail|required|min:3',
            'montant_livraison' => 'bail|required|numeric|min:50',
            'date' => 'bail|required|',
            'description' => 'bail|required|min:8|max:255',
        ]);
        if ($montant_collecter != null) {
            $validated = $request->validate([
                'montant_collecter' => 'bail|required|numeric',
            ]);
        }
        // ici on déclare et fait les vérifications concernants spécialement la commande de type entreprise
        $commande = new Commandes;
        if ($type_commande == 'entreprise') {
            // ici on fait les vérifications spécifiques à la commande de type entreprise
            if ($id_client2 == null) {
                $message = "<b class='text-danger text-center'>Erreur !!!</b> <br> <span> Nous ne trouvons pas cette Entreprise <br> Enregistrez là et réesayez en <a href='".route('clients.create')."' target='_blank'> cliquant ici </a></span> ";
                session()->flash('message', $message);
            }
            $table_produit = explode(',', $request->input('table_produit'));
            session()->flash('table_produit', $table_produit);
            $validated = $request->validate([
                'id_client2' => 'bail|numeric|min:1',
                'id_boutique' => 'bail|required|numeric|min:1',
                'table_produit' => 'bail|required',
            ]);
            session()->flash('entreprise', $entreprise);
            $produits = Produits::whereIn('id', $table_produit)->get();
            // on déclare le nom et le numéro du client choisit depuis la bd
            $client = Clients::findOrFail($id_client);
            // La boutique doit appartenir au client connecté.
            Boutiques::where('id_client', $id_client)->findOrFail($id_boutique);
            $nom_client = $client->noms.' '.$client->Prenoms;
            $telephone = $client->telephone;
            $id_client = $client->id;
            // ici on déclare et fait les vérifications concernants spécialement le premier formulaire
        } else {
            if ($id_client == null) {
                $validated = $request->validate([
                    'telephone' => 'bail|required|regex:/^[6,2][0-9]{8}$/',
                    'nom_client' => 'bail|required|min:3',
                ]);
                $name = explode(' ', $nom_client);
                $noms = $name[0];
                $prenoms = '';
                for ($i = 1; $i < count($name); $i++) {
                    $prenoms .= $name[$i].' ';
                }
                if ($prenoms == '') {
                    $prenoms = '?';
                }
                $client = new Clients;
                $client->noms = $noms;
                $client->Prenoms = $prenoms;
                $client->type_client = 2;
                $client->telephone = $telephone;
                $client->statut = 1;
                $client->save();
                $nom_client = $client->noms.' '.$client->Prenoms;
                $telephone = $client->telephone;
                $id_client = $client->id;
            }
        }
        if ($id_quartier_colis == null) {
            if (Quartier::where('libelle', $lieu_collecte)->where('id_ville', [$ville_collecte])->count() > 0) {
                $id = Quartier::where('libelle', $lieu_collecte)->where('id_ville', [$ville_collecte])->limit(1)->get('id')->value('id');
                $quartier_collecte = Quartier::findOrFail($id);
            } else {
                $quartier_collecte = new Quartier;
                $quartier_collecte->libelle = $lieu_collecte;
                $quartier_collecte->id_ville = $ville_collecte;
                $quartier_collecte->save();
            }
            $lieu_collecte = $quartier_collecte->libelle;
            $id_quartier_colis = $quartier_collecte->id;
            // on enregistre le nouveau quartier du colis
        }
        if ($id_quartier_livraison == null && $lieu_collecte != $lieu_livraison) {
            // on enregistre le nouveau quartier de la livraison
            if (Quartier::where('libelle', $lieu_livraison)->where('id_ville', [$ville_livraison])->count() > 0) {
                $id = Quartier::where('libelle', $lieu_livraison)->where('id_ville', [$ville_livraison])->limit(1)->get('id')->value('id');
                $quartier_livraison = Quartier::findOrFail($id);
            } else {
                $quartier_livraison = new Quartier;
                $quartier_livraison->libelle = $lieu_livraison;
                $quartier_livraison->id_ville = $ville_livraison;
                $quartier_livraison->save();
            }
            $lieu_livraison = $quartier_livraison->libelle;
            $id_quartier_livraison = $quartier_livraison->id;
        }
        // les frais de livraison sont recalculés côté serveur, jamais repris du formulaire
        $montant_livraison = $this->tarifLivraison($id_quartier_colis, $id_quartier_livraison);
        if ($montant_livraison === null) {
            throw ValidationException::withMessages([
                'montant_livraison' => "Aucun tarif de livraison n'est défini entre ces deux zones.",
            ]);
        }
        // ici on enregistre la commande avec les infos communs (indispensables à l'enregistrement)
        $commande->id_client = $id_client;
        $commande->telephone = $telephone;
        $commande->nom_client = $nom_client;
        // ici on enregistre la commande avec l'id du client et l'id de la boutique type_commande
        if ($type_commande == 'entreprise') {
            $commande->id_boutique = $id_boutique;
        }
        $commande->id_saver = Auth::user()->id;
        $commande->date_commande = now();
        $commande->type_commande = $type_commande;
        $commande->adresse_colis = $adresse_colis;
        $commande->id_quartier_colis = $id_quartier_colis;
        $commande->adresse_livraison = $adresse_livraison;
        $commande->id_quartier_livraison = $id_quartier_livraison;
        $commande->montant_livraison = $montant_livraison;
        $commande->montant_recuperer = $montant_collecter;
        $heure = $time == null ? '00:00:00' : $time.':00';
        $full_date = $date.' '.$heure;
        $date_livraison = Carbon::createFromFormat('Y-m-d H:i:s', $full_date);
        $commande->date_livraison = $date_livraison;
        // ici on trouve la valeur de l'id du montant en fonction des zones
        $quartier_depart = Quartier::findOrFail($id_quartier_colis);
        $quartier_arrivee = Quartier::findOrFail($id_quartier_livraison);
        if ($quartier_depart->zone == $quartier_arrivee->zone || $quartier_depart->zone == null || $quartier_arrivee->zone == null) {
            $id_montant_livraison = null;
        } else {
            $id_montant_livraison = Montant_livraison::whereIn('id_zone_colis', [$quartier_depart->zone->id, $quartier_arrivee->zone->id])
                ->whereIn('id_zone_livraison', [$quartier_depart->zone->id, $quartier_arrivee->zone->id])
                ->limit(1)
                ->get('id')
                ->value('id');
        }
        $commande->id_montant_livraison = $id_montant_livraison;
        $commande->description = $description;
        $commande->mode_de_paiement = $mode_de_paiement;
        $commande->statut = 'attente';
        $commande->save();
        // ici on enregistre le detail de la commande si celle ci contient un produit enregistré
        if ($type_commande == 'entreprise') {
            foreach ($produits as $produit) {
                $detail_commande = new Details_commande;
                $detail_commande->id_commande = $commande->id;
                $detail_commande->id_produit = $produit->id;
                $detail_commande->nom_produit = $produit->noms;
                $detail_commande->quantite = $request->input('quantite'.$produit->id);
                $detail_commande->prix = $montant_livraison;
                $detail_commande->save();
            }
        }
        // on retourne la vue avec le message de succès
        session()->forget('id_boutique');
        session()->forget('table_produit');

        $message = "<span> Commande créée avec </span><b class='text-success'> Succès.</b>";
        session()->flash('message', $message);
        if ($type_commande == 'entreprise') {
            $message = "<span> Commande créée avec </span><b class='text-success'> Succès.</b>";
            session()->flash('success', $message);

            return redirect()->route('commandes.create');
        }

        return redirect()->route('commandes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $commande = $this->commandeDuClient($id);
        $contenu = [
            'attente' => 'primary/clock/En attente',
            'attribue' => 'info/user-check/Attribuée',
            'encours' => 'dark/loader/En cours',
            'livre' => 'success/check/Livrée',
            'annulee' => 'danger/alert-triangle/Annulée',
            'echoue' => 'warning/slash/Echouée',
        ];
        $ids = [];
        $statuts = ['attente', 'attribue', 'encours', 'livre', 'annulee', 'echoue'];
        $coursiers = Coursiers::where('statut', [1])->get();

        return view('client.pages.commandes.info', compact('commande', 'contenu', 'statuts', 'coursiers'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $commande = $this->commandeDuClient($id);
        $modes_de_paiement = ['momo', 'collecte', 'livraison'];

        return view('client.pages.commandes.edit', compact('modes_de_paiement', 'commande'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // ici nous déclarons les variables du formulaire d'édition de commande
        $adresse_colis = $request->input('contact_colis').'*/*'.$request->input('lieu_colis').'*/*'.$request->input('description_collecte');
        $adresse_livraison = $request->input('contact_livraison').'*/*'.$request->input('lieu_livraison').'*/*'.$request->input('description_livraison').'*/*'.$request->input('nom_livraison');
        $montant_livraison = $request->input('montant_livraison');
        $date_livraison = $request->input('date');
        $mode_de_paiement = $request->input('mode_de_paiement');

        // ici on fait les vérifications des infos entrées dans le formulaire d'édition
        $validated = $request->validate([
            'contact_colis' => 'bail|required|regex:/^[6,2][0-9]{8}$/',
            'lieu_colis' => 'bail|required|min:3',
            'contact_livraison' => 'bail|required|regex:/^[6,2][0-9]{8}$/',
            'lieu_livraison' => 'bail|required|min:3',
            'montant_livraison' => 'bail|required|numeric|min:50',
            'date' => 'bail|required',
        ]);

        // ici on passe à la modification de la commande rien de plus simple
        $commande = $this->commandeDuClient($id);
        $commande->id_saver = Auth::user()->id;
        $commande->date_commande = now();
        $commande->adresse_colis = $adresse_colis;
        $commande->adresse_livraison = $adresse_livraison;
        $commande->date_livraison = $date_livraison;
        // $commande->mode_de_paiement = $mode_de_paiement;
        $commande->save();
        $message = "La commande a été modifiée avec <b class='text-success'> succès.</b>";
        session()->flash('message', $message);

        return redirect()->route('Clientcommandes.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        // ici nous déclarons les variables communes aux deux formulaires
        $statut = $request->input('statut');
        $id_coursier = $request->input('id_coursier');
        // on cherche la commande concernée
        $commande = $this->commandeDuClient($id);
        $message = "<b class='text-danger text-center'>Echec ! </br> Ce statut n'est pas correct.</b>";
        // ici on vérifie si le statut existe et est bel et bien une chaine de caractère
        session()->flash('message', $message);
        $validated = $request->validate([
            'statut' => 'bail|required|alpha',
        ]);
        session()->forget('message');
        if ($statut != 'annulee' || $id_coursier != null) {
            $message = "<b class='text-danger text-center'>Echec ! </br> Cette opération est impossible.</b>";
            session()->flash('message', $message);

            return redirect()->back();
        }
        // ici on vérifie si le coursier existe et est bel et bien un nombre positif supérieur à 0
        if ($id_coursier != null) {
            // on vérifie si la commande qui veut être attribuée est bel et bien en attente. si oui on insère l'id du coursier sinon on retourne un message d'erreur
            if ($commande->statut == 'attente') {
                session()->flash('id_commande', $id);
                $validated = $request->validate([
                    'id_coursier' => 'bail|required|numeric|min:1',
                ]);
                $commande->id_coursier = $id_coursier;
            } else {
                $message = "<b class='text-danger text-center'>Echec ! </br> Mauvaise pratique.</b>";
                session()->flash('message', $message);

                return redirect()->back();
            }
        }
        // ici on fait un algorithme qui va nous donner le tableau possibility qui va contenir les possibilité de changement de statut pour la dernière action
        $statuts_norm = ['attente', 'attribue', 'encours', 'livre', 'annulee', 'echoue'];
        $commande->statut == 'attente' ? $depart = 1 : $statuts_norm;
        $commande->statut == 'attribue' ? $depart = 2 : $statuts_norm;
        $commande->statut == 'encours' ? $depart = 3 : $statuts_norm;
        $possibility = [];
        for ($i = $depart; $i < count($statuts_norm); $i++) {
            if ($commande->statut == 'attente' && $i == 2) {
                continue;
            }
            if ($commande->statut == 'attente' && $i == 3) {
                continue;
            }
            array_push($possibility, $statuts_norm[$i]);
        }
        if (in_array($statut, $possibility)) {
            $commande->statut = $statut;
            if (in_array($statut, ['livre', 'echoue', 'annulee'])) {
                if ($commande->date_mise_encours == null) {
                    $commande->date_mise_encours = now();
                }
                $commande->date_livre = now();
            }
            if ($statut == 'encours') {
                $commande->date_mise_encours = now();
            }
            $commande->save();
            $message = "Statut de la commande modifié avec <b class='text-success text-center'> succès.</b>";
            session()->flash('message', $message);

            return redirect()->back();
        } else {
            $message = "<b class='text-danger text-center'>Echec ! </br> Cette opération est impossible.</b>";
            session()->flash('message', $message);

            return redirect()->back();
        }

    }
}
