<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Users\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Consultation et modification du profil du compte connecté, communes aux
 * espaces client et coursier. La classe fournit espace() et met à jour la
 * fiche liée (client ou coursier) dans synchroniserFiche().
 */
trait GereSonProfil
{
    abstract protected function espace(): string;

    abstract protected function synchroniserFiche(Users $utilisateur, string $noms, string $prenoms, string $telephone): void;

    public function show(Request $request, $id)
    {
        $user = Users::findOrFail(Auth::id());

        return view($this->espace().'.pages.user.info', compact('user'));
    }

    public function edit($id)
    {
        $user = Users::findOrFail(Auth::id());

        return view($this->espace().'.pages.user.edit', compact('user'));
    }

    /**
     * L'identifiant de l'URL est ignoré : seul le compte connecté est modifié.
     */
    public function update(Request $request, $id)
    {
        $id = Auth::id();
        $telephone = str_replace(' ', '', (string) $request->input('telephone'));
        $request->merge(['telephone' => $telephone]);
        $request->validate([
            'noms' => 'bail|required|max:255',
            'prenoms' => 'bail|required|max:255',
            'email' => "bail|required|max:255|unique:users,email,$id",
            'telephone' => "bail|required|regex:/^[6,2][0-9]{8}$/|unique:users,telephone,$id",
        ]);
        $noms = $request->input('noms');
        $prenoms = $request->input('prenoms');

        $utilisateur = Users::findOrFail($id);
        $utilisateur->noms = $noms.' '.$prenoms;
        $utilisateur->email = $request->input('email');
        $utilisateur->telephone = $telephone;
        $this->synchroniserFiche($utilisateur, $noms, $prenoms, $telephone);
        $utilisateur->save();
        session()->flash('message', "Informations Enregistrées avec <b class='text-success'> Succès. </b>");

        return redirect()->route(ucfirst($this->espace()).'users.show', $utilisateur->id);
    }
}
