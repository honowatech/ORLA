<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\SuperAdmin\User;
use App\Models\SuperAdmin\Client;
use App\Models\SuperAdmin\Abonnement;

class AbonnementController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $check = check_superadmin();
        if($check != 'true'){
            session()->put('dernier_url',url()->current());
            return redirect()->route($check);
        }
        return view('superadmin.pages.abonnement.index');
    }

  /**
   * Ajax de la liste
   *
   * @return Response
   */
  public function index_ajax(Request $request)
  {
        $check = check_superadmin();
        if($check != 'true'){
            session()->put('dernier_url',url()->current());
            return redirect()->route($check);
        }
  /// ici on récupère les clients en fonction de ce qui est entré dans le champ de recherche et aussi avec la pagination laravel ///
        $types_periode = [
        'jour' => 'Jour(s)', 
        'semaine' => 'Semaine(s)', 
        'mois' => 'Mois', 
        'annee' => 'An(s)'
        ];
        $abonnements = Abonnement::where('titre', 'like', '%' . $request->input('recherche') . '%')
            ->orWhere('montant', 'like', '%' . str_replace(' ','',$request->input('recherche')) . '%')
            ->orWhere('type_periode', 'like', '%' . $request->input('recherche') . '%')
            ->orderBy('updated_at','desc')
            ->paginate(10);
    $search = $request->input('recherche');
    return view('superadmin.pages.abonnement.index_ajax',compact('abonnements','types_periode'));
  }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $check = check_superadmin();
        if($check != 'true'){
            session()->put('dernier_url',url()->current());
            return redirect()->route($check);
        }
        $types_periode = [
        'jour' => 'Jour(s)', 
        'semaine' => 'Semaine(s)', 
        'mois' => 'Mois', 
        'annee' => 'Année(s)'
        ];
        return view('superadmin.pages.abonnement.create',compact('types_periode'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function recap_create(Request $request)
    {
        $check = check_superadmin();
        if($check != 'true'){
            session()->put('dernier_url',url()->current());
            return redirect()->route($check);
        }
        $datas = $request->input('table_data');
        $periode = $datas['accumulateur'] == 1 ? str_replace('(s)','',$datas['type_periode']) : $datas['accumulateur'].' '.str_replace('(s)','s',$datas['type_periode']);
        // dd($datas);
        $entree = true;
        echo "<h4>";
            echo '</p> Vous allez enregistrer un <b> Abonnement </b>.</p>';
            echo '<p>Libellé : <b>'.$datas['titre'].'</b></p>';
            echo '<p>Montant : <b>'.Sa_montant($datas['montant']).'</b> FCFA à payer chaque  <b> '.$periode.' </b>. </p>';
            echo '<p> Avec une période de grace allant jusqu\'à <b>'.$datas['periode_grace'].'</b> Jours.</p>';
        echo "</h4>";
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $check = check_superadmin();
        if($check != 'true'){
            session()->put('dernier_url',url()->current());
            return redirect()->route($check);
        }
        // $types_periode = json_encode(['annee','semaine','mois','jour']);
      $titre = $request->input('titre');
      $montant = $request->input('montant');
      $accumulateur = $request->input('accumulateur');
      $type_periode = $request->input('type_periode');
      $periode_grace = $request->input('periode_grace');
      $validated = $request->validate([
          'titre' => 'bail|required|unique:super_admin_abonnement|max:255',
          'montant' => 'bail|required|numeric|min:100|max:9999999999999999999',
          'accumulateur' => 'bail|required|numeric|min:1',
          'periode_grace' => 'bail|required|numeric|min:0',
          'type_periode' => 'bail|required',
          // 'type_periode' => 'bail|required|in:'.$types_periode,
      ]);
  // dd($name,$telephone,$telephone_secondaire,$salaire,$cni);
  // On enregistre le client
      $abonnement = new Abonnement;
      $abonnement->titre = $titre;
      $abonnement->montant = $montant;
      $abonnement->accumulateur = $accumulateur;
      $abonnement->type_periode = $type_periode;
      $abonnement->periode_grace = $periode_grace;
      $abonnement->statut = 1;
      $abonnement->save();
      $message = "Abonnement ".Sa_name(e($titre))."créée avec <b class='text-success'> Succès.</b>";
      session()->flash('message',$message);
      return redirect()->route('Sa-abonnement.index');
      // return redirect()->route('Sa-abonnement.show',$abonnement->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id,Request $request)
    {
        $check = check_superadmin();
        if($check != 'true'){
            session()->put('dernier_url',url()->current());
            return redirect()->route($check);
        }
        $types_periode = [
        'jour' => 'Jour(s)', 
        'semaine' => 'Semaine(s)', 
        'mois' => 'Mois', 
        'annee' => 'Année(s)'
        ];
      $abonnement = Abonnement::findOrFail($id);
      return view('superadmin.pages.abonnement.info',compact('abonnement','types_periode'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function recap_edit(Request $request)
    {
        $check = check_superadmin();
        if($check != 'true'){
            session()->put('dernier_url',url()->current());
            return redirect()->route($check);
        }
        $datas = $request->input('table_data');
        $période = $datas['accumulateur'] == 1 ? str_replace('(s)','',$datas['type_periode']) : $datas['accumulateur'].' '.str_replace('(s)','s',$datas['type_periode']);
        // dd($datas);
        $entree = true;
        echo "<h4>";
            echo '</p> Vous allez enregistrer un <b> Abonnement </b>.</p>';
            echo '</p><u> Valeurs de après modification </u> :</b></p>';
            echo '<p>Libellé : <b>'.$datas['titre'].'</b></p>';
            echo '<p>Montant : <b>'.Sa_montant($datas['montant']).'</b> FCFA à payer chaque  <b> '.$période.' </b>. </p>';
            echo '<p> Avec une période de grace allant jusqu\'à <b>'.$datas['periode_grace'].'</b> Jours.</p>';
        echo "</h4>";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $check = check_superadmin();
        if($check != 'true'){
            session()->put('dernier_url',url()->current());
            return redirect()->route($check);
        }
        $types_periode = [
        'jour' => 'Jour(s)', 
        'semaine' => 'Semaine(s)', 
        'mois' => 'Mois', 
        'annee' => 'Année(s)'
        ];
      $abonnement = Abonnement::findOrFail($id);
      return view('superadmin.pages.abonnement.edit',compact('abonnement','types_periode'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request,$id)
    {
        $check = check_superadmin();
        if($check != 'true'){
            session()->put('dernier_url',url()->current());
            return redirect()->route($check);
        }
        // $types_periode = json_encode(['annee','semaine','mois','jour']);
      $titre = $request->input('titre');
      $montant = $request->input('montant');
      $accumulateur = $request->input('accumulateur');
      $type_periode = $request->input('type_periode');
      $periode_grace = $request->input('periode_grace');
      $validated = $request->validate([
          'titre' => 'bail|required|max:255',
          'montant' => 'bail|required|numeric|min:100|max:9999999999999999999',
          'accumulateur' => 'bail|required|numeric|min:1',
          'periode_grace' => 'bail|required|numeric|min:0',
          'type_periode' => 'bail|required',
          // 'type_periode' => 'bail|required|in:'.$types_periode,
      ]);
      if ( Abonnement::where('titre',$titre)->whereNotIn('id',[$id])->exists()) {
        $validated = $request->validate([
          'titre' => 'unique:super_admin_abonnement',
        ]);
      }
  // dd($name,$telephone,$telephone_secondaire,$cni,$adresse);
  // On enregistre le Client
      $abonnement = Abonnement::findOrFail($id);
      $abonnement->titre = $titre;
      $abonnement->montant = $montant;
      $abonnement->accumulateur = $accumulateur;
      $abonnement->type_periode = $type_periode;
      $abonnement->periode_grace = $periode_grace;
      $abonnement->statut = 1;
      $abonnement->save();
      $message = "Abonnement ".Sa_name(e($titre))." modifié avec <b class='text-success'> Succès.</b>";
      session()->flash('message',$message);
      return redirect()->route('Sa-abonnement.index');
      // return redirect()->route('Sa-abonnement.show',$abonnement->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $check = check_superadmin();
        if($check != 'true'){
            session()->put('dernier_url',url()->current());
            return redirect()->route($check);
        }
      $abonnement = Abonnement::findOrFail($id);
      if ($abonnement->statut == 0) {
        $abonnement->statut = 1;
        $message = "Abonnement ".Sa_name(e($abonnement->titre))." Activé avec <b class='text-success'> Succès.</b>";
      }else{
        $abonnement->statut = 0;
        $message = "Abonnement ".Sa_name(e($abonnement->titre))." Desactivé avec <b class='text-success'> Succès.</b>";
      }
      $abonnement->save();
      session()->flash('message',$message);
      return redirect()->back();
    }
}
