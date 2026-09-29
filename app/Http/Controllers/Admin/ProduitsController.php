<?php 

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\users\Users;
use App\Models\agents\Agents;
use App\Models\coursiers\Coursiers;
use App\Models\clients\Clients;
use App\Models\commandes\Commandes;
use App\Models\details_commande\Details_commande;
use App\Models\boutiques\Boutiques;
use App\Models\produits\Produits;
use App\Models\quartier\Quartier;
use App\Models\ville\Ville;
use App\Models\stock\Stock;
use App\Models\typeClient\TypeClient;
use App\Models\typeUtilisateur\TypeUtilisateur;
use App\Models\informations_personnels\Informations_personnels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProduitsController extends Controller 
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
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

    $request = request();
    $produits = Produits::where('libelle', 'like', '%' . $request->input('recherche') . '%')
        ->orWhere('description', 'like', '%' . $request->input('recherche') . '%')
        ->orWhere('noms', 'like', '%' . $request->input('recherche') . '%')
        ->orderBy('updated_at','desc')
        ->paginate(20);
      session()->flash('search',$request->input('recherche'));
    return view('admin.pages.produits.index',compact('produits'));
    
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
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
    return view('admin.pages.produits.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
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
     $noms = $request->input('noms');
     $libelle = $request->input('libelle');
     $description = $request->input('description');
     $request->validate([
      'noms' => 'bail|required|min:2|unique:produits',
      'libelle' => 'bail|required|min:2|unique:produits',
      'description' => 'bail|required|min:5',
    ]);
      $produits = new Produits ;
      $produits->noms = $noms;
      $produits->libelle = $libelle;
      $produits->description = $description;
      $produits->statut = 1;
     $produits->save();
     $message = "Produit créée avec <b class='text-success'> Succès.</b>";
    session()->flash('message',$message);
     return redirect()->route('produits.index');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
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
    $produit = Produits::findOrFail($id);
    return view('admin.pages.produits.info',compact('produit'));

    
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
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
    $produit = Produits::findOrFail($id);
    return view('admin.pages.produits.edit',compact('produit'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Request $request,$id)
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
    
    $noms = $request->input('noms');
    $libelle = $request->input('libelle');
    $description = $request->input('description');
    $produit = Produits::findOrFail($id);
      $validated = $request->validate([
        'description' => 'bail|required|min:5',
      ]);
    if($produit->details_commande->count() == 0 && $produit->stock->count() == 0){
      $validated = $request->validate([
        'noms' => 'bail|required|min:2',
        'libelle' => 'bail|required|min:2',
        ]);
      if (  Produits::where('libelle',[$libelle])->whereNotIn('id',[$id])->count() > 0) {
        $validated = $request->validate([
        'libelle' => 'unique:produits',
        ]);
      }
      if (  Produits::where('noms',[$noms])->whereNotIn('id',[$id])->count() > 0) {
        $validated = $request->validate([
        'noms' => 'unique:produits',
        ]);
      }
    }

      $produit->noms = $noms;
      $produit->libelle = $libelle;
      $produit->description = $description;
     $produit->save();
    $message = "Produit modifié avec <b class='text-success'> Succès.</b>";
    session()->flash('message',$message);
    return redirect()->route('produits.index');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy(Request $request,$id)
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
    $produit = Produits::findOrFail($id);
    if($produit->details_commande->count() == 0 && $produit->stock->count() == 0){
      $produit = Produits::findOrFail($id);
      $produit->delete();
      $message = "Produit supprimé <b class='text-success'> Succès.</b>";
      session()->flash('message',$message);
      return redirect()->route('produits.index');
    }
        $message = "<b class='text-danger'>Echec... </b> Ce Produit possède plusieurs connexions";
        session()->flash('message',$message);
        return redirect()->back();
  }
  
}

?>