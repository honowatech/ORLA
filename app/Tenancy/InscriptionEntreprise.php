<?php

namespace App\Tenancy;

use App\Models\Entreprise;
use App\Models\typeUtilisateur\TypeUtilisateur;
use App\Models\User;
use App\Models\ville\Ville;
use App\Notifications\InvitationUtilisateur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * Création d'une entreprise et de son administrateur, que ce soit par
 * l'inscription en libre-service ou par le Super Admin.
 */
class InscriptionEntreprise
{
    /** Libellé du rôle « administrateur d'entreprise » (historique : « Super Admin »). */
    public const ROLE_ADMINISTRATEUR = 'Super Admin';

    public function __construct(protected EntrepriseCourante $courante)
    {
    }

    /**
     * Inscription en libre-service : entreprise en essai + administrateur.
     *
     * @param  array{entreprise: string, telephone: string, ville: string, noms: string, email: string, password: string}  $donnees
     */
    public function inscrire(array $donnees): User
    {
        return DB::transaction(function () use ($donnees) {
            $entreprise = Entreprise::create([
                'name' => $donnees['entreprise'],
                'email' => $donnees['email'],
                'telephone' => $donnees['telephone'],
                'statut' => 1,
                'essai_jusqu_au' => now()->addDays(config('tenancy.essai_jours')),
            ]);

            return $this->courante->pour($entreprise, function () use ($entreprise, $donnees) {
                $ville = $this->ville($donnees['ville']);

                return $this->creerAdministrateur($entreprise, $donnees['noms'], $donnees['email'], $donnees['telephone'], $donnees['password'], $ville->id);
            });
        });
    }

    /**
     * Crée l'administrateur d'une entreprise ; il devient son propriétaire
     * si elle n'en a pas encore. Sans mot de passe, un mot de passe aléatoire
     * est posé et l'utilisateur le choisira via son invitation.
     */
    public function creerAdministrateur(Entreprise $entreprise, string $noms, string $email, ?string $telephone = null, ?string $password = null, ?int $idVille = null): User
    {
        return $this->courante->pour($entreprise, function () use ($entreprise, $noms, $email, $telephone, $password, $idVille) {
            $user = new User;
            $user->noms = $noms;
            $user->email = $email;
            $user->telephone = $telephone;
            $user->password = $password ?? Str::random(40);
            $user->id_type_utilisateur = $this->roleAdministrateur();
            $user->id_ville = $idVille;
            $user->statut = 1;
            $user->save();

            if ($entreprise->id_proprietaire === null) {
                $entreprise->id_proprietaire = $user->id;
                $entreprise->save();
            }

            return $user;
        });
    }

    /**
     * Envoie à l'utilisateur un lien pour choisir son mot de passe.
     */
    public function inviter(User $user): void
    {
        $user->notify(new InvitationUtilisateur(Password::broker()->createToken($user)));
    }

    /**
     * Ville commune du même nom si elle existe, sinon ville propre à l'entreprise.
     */
    protected function ville(string $libelle): Ville
    {
        $libelle = trim($libelle);
        $ville = Ville::whereRaw('lower(libelle) = ?', [mb_strtolower($libelle)])->first();
        if ($ville !== null) {
            return $ville;
        }

        $ville = new Ville;
        $ville->libelle = $libelle;
        $ville->code = mb_strtoupper(mb_substr(Str::ascii($libelle), 0, 3));
        $ville->save();

        return $ville;
    }

    protected function roleAdministrateur(): int
    {
        $role = TypeUtilisateur::where('libelle', self::ROLE_ADMINISTRATEUR)->first();
        if ($role === null) {
            $role = new TypeUtilisateur;
            $role->libelle = self::ROLE_ADMINISTRATEUR;
            $role->save();
        }

        return $role->id;
    }
}
