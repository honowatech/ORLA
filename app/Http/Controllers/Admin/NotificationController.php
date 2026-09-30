<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\users\Users;
use App\Models\agents\Agents;
use App\Models\coursiers\Coursiers;
use App\Models\ville\Ville;
use App\Models\clients\Clients;
use App\Models\commandes\Commandes;
use App\Models\informations_personnels\Informations_personnels;
use App\Models\typeUtilisateur\TypeUtilisateur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class NotificationController extends Controller
{
     /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    return view('admin.pages.notification');
  }












  
}

?>
