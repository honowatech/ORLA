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
use App\Models\paiement\Paiement;
use App\Models\ville\Ville;
use App\Models\stock\Stock;
use App\Models\typeClient\TypeClient;
use App\Models\typeUtilisateur\TypeUtilisateur;
use App\Models\informations_personnels\Informations_personnels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PaiementController extends Controller 
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
    
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
  {
    
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
  {
    
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update($id)
  {
    
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id)
  {
    
  }
  
}

?>