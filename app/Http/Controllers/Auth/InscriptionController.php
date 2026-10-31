<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Tenancy\InscriptionEntreprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

/**
 * Inscription en libre-service d'une entreprise : elle obtient son espace,
 * un essai gratuit et un compte administrateur.
 */
class InscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function create()
    {
        return view('auth.inscription', ['essai_jours' => config('tenancy.essai_jours')]);
    }

    public function store(Request $request, InscriptionEntreprise $inscription)
    {
        $donnees = $request->validate([
            'entreprise' => 'bail|required|string|min:2|max:255',
            'telephone' => ['bail', 'required', 'regex:/^[6,2][0-9]{8}$/'],
            'ville' => 'bail|required|string|min:2|max:255',
            'noms' => 'bail|required|string|min:2|max:255',
            'email' => 'bail|required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'conditions' => 'accepted',
        ], [
            'telephone.regex' => 'Le numéro doit comporter 9 chiffres et commencer par 6 ou 2.',
            'email.unique' => 'Un compte existe déjà avec cet email. Connectez-vous ou utilisez « Mot de passe oublié ».',
            'conditions.accepted' => "Vous devez accepter les conditions d'utilisation.",
        ]);

        $user = $inscription->inscrire($donnees);

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            // Le compte est créé : l'utilisateur pourra redemander l'email de vérification.
            report($e);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('demarrage');
    }
}
