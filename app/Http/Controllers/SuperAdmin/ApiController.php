<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\SuperAdmin\Api;

class ApiController extends Controller
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
        // dd($datas);
        $entree = true;
        echo "<h4>";
            echo '<p>Vous allez modifier l\'api '.$datas['name'].' Money : </p>';
            if($datas['user']==null){
                echo '<p>User Name : <b>NULL</b>.</p>';
            }else{
                echo '<p>User Name : <b>'.$datas['user'].'</b>.</p>';
            }
            if($datas['password']==null){
                echo '<p>Mot de Passe : <b>NULL</b>.</p>';
            }else{
                echo '<p>Mot de Passe : <b>'.Sa_password($datas['password']).'</b>.</p>';
            }
            echo '<p>Clé de l\'api : <b>'.$datas['key'].'</b>.</p>';
            if($datas['secret']==null){
                echo '<p>Secret de l\'api : <b>NULL</b>.</p>';
            }else{
                echo '<p>Secret de l\'api : <b>'.$datas['secret'].'</b>.</p>';
            }
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
      $api = Api::findOrFail($id);
      return view('superadmin.pages.api.edit',compact('api'));
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
      $user = $request->input('user');
      $password = $request->input('password');
      $key = $request->input('key');
      $secret = $request->input('secret');
      $validated = $request->validate([
          'key' => 'bail|required|max:255',
      ]);
      if ($password != null) {
        $validated = $request->validate([
            'password' => 'bail|required|max:255',
        ]);
      }
      if ($secret != null) {
        $validated = $request->validate([
            'secret' => 'bail|required|max:255',
        ]);
      }
      if ($user != null) {
        $validated = $request->validate([
          'user' => 'bail|required|max:255',
        ]);
      }
  // dd($name,$telephone,$telephone_secondaire,$cni,$adresse);
  // On enregistre le Client
      $api = Api::findOrFail($id);
      $api->user = $user;
      $api->password = $password;
      $api->key = $key;
      $api->secret = $secret;
      $api->save();
      $message = "Api ".$api->name." modifié avec <b class='text-success'> Succès.</b>";
      session()->flash('message',$message);
      return redirect()->route('Sa-parametre.index');
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
      $api = Api::findOrFail($id);
      if ($api->statut == 0) {
        $api->statut = 1;
        $message = "Api <b>".Sa_name($api->name)."</b> <span class='text-success'> Disponible </span> chez le client";
      }else{
        $api->statut = 0;
        $message = "Api <b>".Sa_name($api->name)."</b> <span class='text-danger'> Indisponible </span> chez le client";
      }
      $api->save();
      session()->flash('message',$message);
      return redirect()->back();
    }
}