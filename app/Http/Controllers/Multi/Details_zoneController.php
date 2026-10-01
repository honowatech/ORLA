<?php 

namespace App\Http\Controllers\Multi;

use Illuminate\Http\Request;
use App\Models\TypeUtilisateur\TypeUtilisateur;
use App\Models\Details_zone\Details_zone;

class Details_zoneController extends Controller 
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
        $filter = filter(['routeur','admin','superviseur_ville'],Auth()->user());
        if($filter != 'true'){
            return redirect()->route($filter);
        }
    
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
        $filter = filter(['routeur','admin','superviseur_ville'],Auth()->user());
        if($filter != 'true'){
            return redirect()->route($filter);
        }
    
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
  {
        $filter = filter(['routeur','admin','superviseur_ville'],Auth()->user());
        if($filter != 'true'){
            return redirect()->route($filter);
        }
    $zones = $request->input('zones');
    $id_coursier = $request->input('id_coursier');

    $message = "<b class='text-danger'>Echec de la requete! </b>, Informations éronnés ";
      session()->flash('message',$message);
    $validated = $request->validate([
      'id_coursier' => 'numeric',
      'zones' => 'min:1',
    ]);
    foreach($zones as $id_zone){
      $details = new Details_zone;
      $details->id_coursier = $id_coursier;
      $details->id_zone = $id_zone;
      $details->save();
    }
      $message = "Zones attribuées avec <b class='text-success'> Succès.</b>";
      session()->flash('message',$message);
    return redirect()->back();
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
        $filter = filter(['routeur','admin','superviseur_ville'],Auth()->user());
        if($filter != 'true'){
            return redirect()->route($filter);
        }
    
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
  {
        $filter = filter(['routeur','admin','superviseur_ville'],Auth()->user());
        if($filter != 'true'){
            return redirect()->route($filter);
        }
    
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Request $request,$id)
  {
        $filter = filter(['routeur','admin','superviseur_ville'],Auth()->user());
        if($filter != 'true'){
            return redirect()->route($filter);
        }
    $id_zone = $request->input('id_zone');
    $id_coursier = $request->input('id_coursier');
    $validated = $request->validate([
      'id_zone' => 'numeric',
    ]);

    $details = Details_zone::where('id_coursier',[$id])->where('id_zone',[$id_zone])->get();
    foreach($details as $detail){
      $detail->delete();
    }
      $message = "Zone désattribuée avec <b class='text-success'> Succès.</b>";
      session()->flash('message',$message);
    return redirect()->back();
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id)
  {
        $filter = filter(['routeur','admin','superviseur_ville'],Auth()->user());
        if($filter != 'true'){
            return redirect()->route($filter);
        }
    
  }
  
}

?>