<?php

namespace App\Http\Controllers\Client;

use App\Models\users\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('client.pages.user.password');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('errors.404');
    }

    public function store(Request $request)
    {
        return view('errors.404');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Request $request, $id)
    {
        return view('errors.404');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        return view('errors.404');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $password = $request->input('password');
        $password_new = $request->input('password_new');
        $password_confirm = $request->input('password_confirm');
        $validated = $request->validate([
            'password' => 'bail|required|',
            'password_new' => 'bail|required|min:8',
            'password_confirm' => 'bail|required|min:8',
        ]);
        if ($password_new != $password_confirm) {
            $message = "<b class='text-danger'> Echec ! </b><br> Les mots de passes ne correspondent pas.";
            session()->flash('message', $message);

            return redirect()->back();
        }
        if (! password_verify($password, $user->password)) {
            $message = "<b class='text-danger'> Echec ! </b><br> Votre mot de passe est éroné.";
            session()->flash('message', $message);

            return redirect()->back();
        }
        // Seul le mot de passe du compte connecté peut être modifié.
        $user = Users::findOrFail(Auth()->user()->id);
        $user->password = password_hash($password_new, PASSWORD_BCRYPT);
        $user->save();
        $message = "Modifications enregistrées avec <b class='text-success'> Succès. </b>";
        session()->flash('message', $message);

        return redirect()->route('Coursierusers.show', Auth::user()->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        return view('errors.404');
    }
}
