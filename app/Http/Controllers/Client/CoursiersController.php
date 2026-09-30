<?php 

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Models\users\Users;
use App\Models\agents\Agents;
use App\Models\informations_personnels\Informations_personnels;
use App\Models\coursiers\Coursiers;
use App\Models\ville\Ville;
use App\Models\details_zone\Details_zone;
use App\Models\zone\Zone;
use App\Models\clients\Clients;
use App\Models\commandes\Commandes;
use App\Models\typeUtilisateur\TypeUtilisateur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CoursiersController extends Controller 
{







  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    $zones = [];
    $toutes_zones = Zone::where('statut',[1])->get();
    foreach($toutes_zones as $zone){
      if (Details_zone::where('id_zone',$zone->id)->where('id_coursier',$id)->count() ==0) {
        array_push($zones, $zone);
      }
    }
    $coursier = Coursiers::findOrFail($id);
    return view('client.pages.coursier.info',compact('coursier','zones'));
  }






  
}

?>