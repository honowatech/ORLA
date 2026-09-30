<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\SuperAdmin\Abonnement;
use App\Models\SuperAdmin\Api;
use App\Models\SuperAdmin\Client;
use App\Models\SuperAdmin\Contact;
use App\Models\SuperAdmin\Info_transaction;
use App\Models\SuperAdmin\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $check = check_superadmin();
        if ($check != 'true') {
            session()->put('dernier_url', url()->current());

            return redirect()->route($check);
        }

        return view('superadmin.pages.transaction.index');
    }

    /**
     * Ajax de la liste
     */
    public function index_ajax(Request $request)
    {
        // / ici on récupère les clients en fonction de ce qui est entré dans le champ de recherche et aussi avec la pagination laravel ///
        $types_periode = [
            'jour' => 'Jour(s)',
            'semaine' => 'Semaine(s)',
            'mois' => 'Mois',
            'annee' => 'Année(s)',
        ];
        $clients = Client::where('name', 'like', '%'.$request->input('recherche').'%')
            ->orWhere('adresse', 'like', '%'.$request->input('recherche').'%')
            ->orWhere('telephone', 'like', '%'.$request->input('recherche').'%')
            ->orWhere('telephone_secondaire', 'like', '%'.$request->input('recherche').'%')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
        $search = $request->input('recherche');

        return view('superadmin.pages.transaction.index_ajax', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types_periode = [
            'jour' => 'Jour(s)',
            'semaine' => 'Semaine(s)',
            'mois' => 'Mois',
            'annee' => 'An(s)',
        ];
        $client = Client::where('statut', [1])->first();
        $api = Api::where('statut', [1])->where('name', 'Monetbill')->whereNotNull('key')->whereNotNull('secret')->first();
        if ($client == null) {
            $abonnements = Abonnement::where('statut', [2])->orderBy('montant')->get();
        } else {
            $abonnements = Abonnement::where('statut', [1])->orderBy('montant')->get();
        }
        $phones = Contact::where('name', 'phone')->first();
        $email = Contact::where('name', 'email')->first();

        return view('superadmin.pages.transaction.create', compact('abonnements', 'types_periode', 'client', 'phones', 'email', 'api'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $id_abonnement = $request->input('id_abonnement');
        $id_client = $request->input('id_client');
        $date_debut = $request->input('date_debut');
        $methode = $request->input('methode');
        // L'attribution sans paiement est réservée au super admin connecté.
        abort_if($methode == 'application' && check_superadmin() != 'true', 403);
        $abonnement = Abonnement::findOrFail($id_abonnement);
        // dd($name,$telephone,$telephone_secondaire,$salaire,$cni);
        // On enregistre le client
        $client = null;
        if ($methode == 'application') {
            $client = Client::findOrFail($id_client);
        } elseif ($methode == 'mobile') {
            $client = Client::where('statut', [1])->first();
        }
        if ($abonnement->statut != 1) {
            $message = "<b class='text-danger'> Erreur. </b></br> L'abonnement ".e($abonnement->titre)." n'est plus actif";
            session()->flash('message', $message);

            return redirect()->back();
        }
        // Seuls le paiement mobile et l'attribution manuelle existent ('bank' n'est pas implémenté).
        if (! in_array($methode, ['mobile', 'application']) || $client === null) {
            $message = "<b class='text-danger'> Erreur. </b></br> Echec du lancement du paiement";
            session()->flash('message', $message);

            return redirect()->back();
        }
        if ($date_debut == null) {
            $date_debut = $client->date_fin == null ? now() : $client->date_fin;
        }
        $date_fin = Sa_prochaine_date_paie($abonnement->type_periode, $abonnement->accumulateur, $date_debut, 1);
        $api = Api::where('statut', [1])->where('name', 'Monetbill')->first();
        $_SESSION['api'] = $api;
        $transaction = new Transaction;
        $transaction->id_abonnement = $id_abonnement;
        $transaction->id_client = $client->id;
        $transaction->methode = $methode;
        $transaction->montant = $abonnement->montant;
        $transaction->nbre_abonnement = 1;
        $transaction->date_debut = $date_debut;
        $transaction->date_fin = $date_fin;
        if ($methode == 'application') {
            $transaction->statut = 'success';
            $transaction->save();
            $client = Client::findOrFail($transaction->id_client);
            $client->date_fin = $transaction->date_fin;
            $client->id_abonnement = $transaction->abonnement->id;
            $client->date_dernier_paiement = $transaction->created_at;
            $client->save();
            $message = 'Abonnement <b>'.e($abonnement->titre).'</b> Attribué à <b>'.e($client->name)."</b> avec <b class='text-success'>Succès </b>";
            session()->flash('message', $message);

            return redirect()->back();
        } elseif ($methode == 'mobile') {
            $transaction->statut = 'waiting';
            $transaction->save();
            // Autorise ce navigateur à consulter la transaction au retour de Monetbil.
            session()->put('Sc-transaction.'.$transaction->id, true);
            session()->save();
            Sa_pay($abonnement->montant, $transaction->id);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function checkpay($id)
    {
        // Transaction, détails et abonnement du client : tout ou rien.
        return DB::transaction(fn () => $this->traiterRetourPaiement($id));
    }

    private function traiterRetourPaiement($id)
    {
        $transaction = Transaction::findOrFail($id);
        // Une transaction déjà traitée n'est jamais retraitée.
        if ($transaction->statut != 'waiting') {
            abort_unless($this->peutVoir($transaction), 403);

            return redirect()->route('Sc-transaction.show', $transaction->id);
        }
        $api = Api::where('statut', [1])->where('name', 'Monetbill')->first();
        $_SESSION['api'] = $api;
        $check = Sa_checkpay();
        // Signature Monetbil invalide, ou paiement fait pour une autre transaction.
        abort_if($check === null || $check['payment_ref'] !== Sa_payment_ref($transaction->id), 403);
        $transaction->statut = $check['statut'];
        $transaction->save();
        foreach ($check as $key => $value) {
            if ($value == null) {
                continue;
            }
            $info = Info_transaction::where('id_transaction', $transaction->id)
                ->where('name', $key)
                ->where('value', $value)->exists();
            if (! $info) {
                $info_transaction = new Info_transaction;
                $info_transaction->id_transaction = $transaction->id;
                $info_transaction->name = $key;
                $info_transaction->value = $value;
                $info_transaction->save();
            }
        }
        if ($check['statut'] == 'success') {
            $client = Client::findOrFail($transaction->id_client);
            $client->date_fin = $transaction->date_fin;
            $client->id_abonnement = $transaction->abonnement->id;
            $client->date_dernier_paiement = $transaction->created_at;
            $client->save();

            return redirect()->route('Sc-transaction.show', $transaction->id);
        } elseif ($check['statut'] == 'cancelled') {
            $message = "la Transaction a été <b class='text-danger'> Interrompue.</b><br> <a class='btn btn-primary btn-sm' href='".route('Sc-transaction.show', $transaction->id)."'>voir<a>";
            session()->flash('message', $message);

            return redirect()->route('Sc-transaction.create');
        }
        $message = "la Transaction a <b class='text-danger'> Echouée.</b><br> <a class='btn btn-primary btn-sm' href='".route('Sc-transaction.show', $transaction->id)."'>voir<a>";
        session()->flash('message', $message);

        return redirect()->route('Sc-transaction.create');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id, Request $request)
    {
        $statuts = [
            'success' => 'success',
            'cancelled' => 'danger',
            'failed' => 'danger',
            'waiting' => 'secondary',
        ];
        $statuts_valeurs = [
            'success' => 'Réussi',
            'cancelled' => 'Annulé',
            'failed' => 'Echoué',
            'waiting' => 'En Attent',
        ];
        $statuts_icon = [
            'success' => 'check',
            'cancelled' => 'rotate-ccw',
            'failed' => 'x',
            'waiting' => 'loader',
        ];
        $transaction = Transaction::findOrFail($id);
        abort_unless($this->peutVoir($transaction), 403);

        return view('superadmin.pages.transaction.info', compact('transaction', 'statuts', 'statuts_valeurs', 'statuts_icon'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        $check = check_superadmin();
        if ($check != 'true') {
            session()->put('dernier_url', url()->current());

            return redirect()->route($check);
        }
        $client = Client::findOrFail($id);
        if ($client->statut == 0) {
            $client->statut = 1;
            $message = 'Client '.Sa_name(e($client->name))." Activé avec <b class='text-success'> Succès.</b>";
        } else {
            $client->statut = 0;
            $message = 'Client '.Sa_name(e($client->name))." Desactivé avec <b class='text-success'> Succès.</b>";
        }
        $client->save();
        session()->flash('message', $message);

        return redirect()->back();
    }

    /**
     * Le super admin voit toutes les transactions ; un visiteur seulement
     * celle qu'il a lui-même initiée dans ce navigateur.
     */
    private function peutVoir(Transaction $transaction)
    {
        return check_superadmin() == 'true' || session()->has('Sc-transaction.'.$transaction->id);
    }
}
