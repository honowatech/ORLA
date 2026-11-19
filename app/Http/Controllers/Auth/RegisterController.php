<?php

namespace App\Http\Controllers\Auth;

use App\Demo\Demo;
use App\Http\Controllers\Controller;
use App\Models\clients\Clients;
use App\Models\typeUtilisateur\TypeUtilisateur;
use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = AppServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /** type_client des particuliers (1 = entreprise), comme dans le reste du code. */
    private const TYPE_CLIENT_PARTICULIER = 2;

    /**
     * Inscription publique : la personne devient un client particulier, seul
     * rôle ouvert à tous. Administrateur, agent et coursier sont créés par un
     * administrateur, car ils donnent accès aux commandes des autres.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'noms' => ['required', 'string', 'max:255'],
            'prenoms' => ['nullable', 'string', 'max:255'],
            'telephone' => ['required', 'regex:/^[62][0-9]{8}$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email',
                // Domaine réservé à la démo : un tel compte ne pourrait jamais se connecter.
                fn ($attribute, $value, $fail) => Demo::isReservedAddress($value) ? $fail('Cette adresse e-mail n\'est pas valide.') : null],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'noms.required' => 'Indiquez votre nom.',
            'telephone.required' => 'Indiquez votre numéro de téléphone.',
            'telephone.regex' => 'Le numéro doit compter 9 chiffres et commencer par 6 ou 2.',
            'email.required' => 'Indiquez votre adresse e-mail.',
            'email.email' => 'Cette adresse e-mail n\'est pas valide.',
            'email.unique' => 'Un compte existe déjà avec cette adresse. Connectez-vous ou réinitialisez votre mot de passe.',
            'password.required' => 'Choisissez un mot de passe.',
            'password.min' => 'Le mot de passe doit compter au moins 8 caractères.',
            'password.confirmed' => 'Les deux mots de passe ne correspondent pas.',
        ]);
    }

    /**
     * Crée la fiche client et l'utilisateur lié, ensemble ou pas du tout.
     */
    protected function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $prenoms = trim($data['prenoms'] ?? '');

            $client = new Clients;
            $client->noms = trim($data['noms']);
            $client->Prenoms = $prenoms;
            $client->type_client = self::TYPE_CLIENT_PARTICULIER;
            $client->telephone = $data['telephone'];
            $client->statut = 1;
            $client->save();

            $user = new User;
            $user->noms = trim($client->noms.' '.$prenoms);
            $user->email = $data['email'];
            $user->password = Hash::make($data['password']);
            $user->telephone = $data['telephone'];
            $user->id_type_utilisateur = TypeUtilisateur::where('libelle', 'Client')->firstOrFail()->id;
            $user->id_client = $client->id;
            $user->statut = 1;
            $user->save();

            $client->id_utilisateur = $user->id;
            $client->save();

            return $user;
        });
    }
}
