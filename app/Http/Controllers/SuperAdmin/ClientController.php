<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\SuperAdmin\Abonnement;
use App\Models\SuperAdmin\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return view('superadmin.pages.client.index');
    }

    /**
     * Ajax de la liste
     *
     * @return Response
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

        return view('superadmin.pages.client.index_ajax', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('superadmin.pages.client.create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function recap_create(Request $request)
    {
        $datas = $request->input('table_data');
        // dd($datas);
        $entree = true;
        $telephone_secondaire = $datas['telephone_secondaire'] == null ? '' : ' / '.Sa_phone2($datas['telephone_secondaire']);
        echo '<h4>';
        echo '</p> Vous allez enregistrer <b>'.$datas['name'].'</b> en tant que Client.</p>';
        echo '<p>Numéro de CNI : <b>'.$datas['cni'].'</b></p>';
        echo '<p>Contact : <b>'.Sa_phone($datas['telephone']).$telephone_secondaire.'</b>.</p>';
        if ($datas['adresse'] == null) {
            echo '<p>Adresse : <b>Aucune adresse</b>.</p>';
        } else {
            echo '<p>Adresse : <b>'.$datas['adresse'].'</b>.</p>';
        }
        echo '</h4>';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $name = $request->input('name');
        $telephone = $request->input('telephone');
        $telephone_secondaire = $request->input('telephone_secondaire');
        $adresse = $request->input('adresse');
        $cni = $request->input('cni');
        $validated = $request->validate([
            'name' => 'bail|required|max:255',
            'telephone' => 'bail|required|unique:super_admin_client|regex:/^[6,2][0-9]{8}$/',
            'cni' => 'bail|required|min:9',
        ]);
        if ($telephone_secondaire != null) {
            $validated = $request->validate([
                'telephone_secondaire' => 'bail|unique:super_admin_client|regex:/^[6,2][0-9]{8}$/',
            ]);
        }
        if ($adresse != null) {
            $validated = $request->validate([
                'adresse' => 'bail|min:4',
            ]);
        }
        // dd($name,$telephone,$telephone_secondaire,$salaire,$cni);
        // On enregistre le client
        $client = new Client;
        $client->name = $name;
        $client->telephone_secondaire = $telephone_secondaire;
        $client->telephone = $telephone;
        $client->adresse = $adresse;
        $client->cni = $cni;
        $client->statut = 1;
        $client->save();
        $message = 'Client '.e($name)." créé avec <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->route('Sa-client.show', $client->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id, Request $request)
    {

        $types_periode = [
            'jour' => 'Jour(s)',
            'semaine' => 'Semaine(s)',
            'mois' => 'Mois',
            'annee' => 'An(s)',
        ];
        $abonnements = Abonnement::where('statut', [1])->orderBy('montant')->get();
        $client = Client::findOrFail($id);

        return view('superadmin.pages.client.info', compact('abonnements', 'types_periode', 'client'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function recap_edit(Request $request)
    {
        $datas = $request->input('table_data');
        // dd($datas);
        $entree = true;
        $telephone_secondaire = $datas['telephone_secondaire'] == null ? '' : ' / '.Sa_phone2($datas['telephone_secondaire']);
        echo '<h4>';
        echo '</p> Vous allez enregistrer <b>'.$datas['name'].'</b> en tant que Client.</p>';
        echo '<p>Numéro de CNI : <b>'.$datas['cni'].'</b></p>';
        echo '<p>Contact : <b>'.Sa_phone($datas['telephone']).$telephone_secondaire.'</b>.</p>';
        if ($datas['adresse'] == null) {
            echo '<p>Adresse : <b>Aucune adresse</b>.</p>';
        } else {
            echo '<p>Adresse : <b>'.$datas['adresse'].'</b>.</p>';
        }
        echo '</h4>';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function recap_abonate(Request $request)
    {
        $datas = $request->input('table_data');
        $types_periode = [
            'jour' => 'Jour(s)',
            'semaine' => 'Semaine(s)',
            'mois' => 'Mois',
            'annee' => 'Année(s)',
        ];
        $abonnement = Abonnement::findOrFail($datas['id_abonnement']);
        $client = Client::findOrFail($datas['id_client']);
        $periode = $abonnement->accumulateur == 1 ? '1 '.str_replace('(s)', '', $types_periode[$abonnement->type_periode]) : $abonnement->accumulateur.' '.str_replace('(s)', 's', $types_periode[$abonnement->type_periode]);
        // dd($datas);
        $entree = true;
        echo '<h4>';
        echo '</p> Vous allez enregistrer une nouvelle Transaction.</p>';
        echo '<p>Client : <b>'.$client->name.'</b></p>';
        echo '<p>Abonnement : <b>'.$abonnement->titre.'</b></p>';
        echo '<p>Montant : <b>'.Sa_montant($abonnement->montant).'</b> FCFA Valide pour <b> '.$periode.' </b>.</p>';
        if ($datas['date_debut'] == null) {
            if ($client->date_fin == null) {
                echo '<p>Débute le <b>'.Sa_Ladate(now()).'  à '.Sa_Heure(now()).'</b>.</p>';
            } else {
                echo '<p>Débute le <b>'.Sa_Ladate($client->date_fin).' à '.Sa_Heure($client->date_fin).'</b>.</p>';
            }
        } else {
            echo '<p>Débute le <b>'.Sa_Ladate($datas['date_debut']).'  à '.Sa_Heure($datas['date_debut']).'</b>.</p>';
        }
        echo '</h4>';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $client = Client::findOrFail($id);

        return view('superadmin.pages.client.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $name = $request->input('name');
        $cni = $request->input('cni');
        $telephone = str_replace(' ', '', $request->input('telephone'));
        $telephone_secondaire = str_replace(' ', '', $request->input('telephone_secondaire'));
        $adresse = $request->input('adresse');
        $validated = $request->validate([
            'name' => 'bail|required|max:255',
            'telephone' => 'bail|required|regex:/^[6,2][0-9]{8}$/',
            'cni' => 'bail|required|min:9',
        ]);
        if ($telephone_secondaire != null) {
            $validated = $request->validate([
                'telephone' => 'bail|regex:/^[6,2][0-9]{8}$/',
            ]);
            if ($adresse != null) {
                $validated = $request->validate([
                    'adresse' => 'bail|min:4',
                ]);
            }
            if (Client::where('telephone_secondaire', $telephone_secondaire)->whereNotIn('id', [$id])->exists()) {
                $validated = $request->validate([
                    'telephone_secondaire' => 'unique:super_admin_client',
                ]);
            }
        } else {
            $telephone_secondaire = null;
        }
        if (Client::where('telephone', $telephone)->whereNotIn('id', [$id])->exists()) {
            $validated = $request->validate([
                'telephone' => 'unique:super_admin_client',
            ]);
        }
        // dd($name,$telephone,$telephone_secondaire,$cni,$adresse);
        // On enregistre le Client
        $client = Client::findOrFail($id);
        $client->name = $name;
        $client->telephone_secondaire = $telephone_secondaire;
        $client->telephone = $telephone;
        $client->adresse = $adresse;
        $client->cni = $cni;
        $client->save();
        $message = 'Client '.Sa_name(e($name))." modifié avec <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->route('Sa-client.show', $client->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
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
}
