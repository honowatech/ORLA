<?php 

namespace App\Http\Controllers\Coursier;

use Illuminate\Http\Request;
use App\Models\users\Users;
use App\Models\agents\Agents;
use App\Models\boutiques\Boutiques;
use App\Models\coursiers\Coursiers;
use App\Models\clients\Clients;
use App\Models\commandes\Commandes;
use App\Models\quartier\Quartier;
use App\Models\ville\Ville;
use App\Models\typeClient\TypeClient;
use App\Models\typeUtilisateur\TypeUtilisateur;
use App\Models\informations_personnels\Informations_personnels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientsController extends Controller 
{







  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    // Un coursier ne voit que les clients de ses propres commandes.
    $id_coursier = Auth()->user()->id_coursier;
    abort_if($id_coursier === null, 403);
    $client = Clients::whereHas('commandes', function ($query) use ($id_coursier) {
        $query->where('id_coursier', $id_coursier);
    })->findOrFail($id);
    $quartiers = Quartier::get();
    return view('coursier.pages.clients.info',compact('client','quartiers'));
  }






  
}

?>