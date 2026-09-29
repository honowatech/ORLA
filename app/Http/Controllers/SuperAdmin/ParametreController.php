<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\SuperAdmin\Api;

class ParametreController extends Controller
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
        $apis = Api::get();
        return view('superadmin.pages.parametre.index',compact('apis'));
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
    }
}