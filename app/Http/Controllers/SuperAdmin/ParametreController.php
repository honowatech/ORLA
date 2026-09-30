<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\SuperAdmin\Api;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $apis = Api::get();

        return view('superadmin.pages.parametre.index', compact('apis'));
    }
}
