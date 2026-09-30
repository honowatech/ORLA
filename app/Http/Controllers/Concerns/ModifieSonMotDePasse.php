<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Users\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Changement de mot de passe du compte connecté, commun aux espaces
 * client et coursier. La classe fournit espace() : « client » ou « coursier ».
 */
trait ModifieSonMotDePasse
{
    abstract protected function espace(): string;

    public function index()
    {
        return view($this->espace().'.pages.user.password');
    }

    /**
     * L'identifiant de l'URL est ignoré : seul le compte connecté est modifié.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'password' => 'bail|required',
            'password_new' => 'bail|required|min:8',
            'password_confirm' => 'bail|required|min:8',
        ]);
        if ($request->input('password_new') != $request->input('password_confirm')) {
            session()->flash('message', "<b class='text-danger'> Echec ! </b><br> Les mots de passes ne correspondent pas.");

            return redirect()->back();
        }
        $user = Users::findOrFail(Auth::id());
        if (! password_verify($request->input('password'), $user->password)) {
            session()->flash('message', "<b class='text-danger'> Echec ! </b><br> Votre mot de passe est éroné.");

            return redirect()->back();
        }
        $user->password = password_hash($request->input('password_new'), PASSWORD_BCRYPT);
        $user->save();
        session()->flash('message', "Modifications enregistrées avec <b class='text-success'> Succès. </b>");

        return redirect()->route(ucfirst($this->espace()).'users.show', $user->id);
    }
}
