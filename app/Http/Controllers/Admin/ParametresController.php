<?php

namespace App\Http\Controllers\Admin;

use App\Models\Entreprise;
use App\Models\typeUtilisateur\TypeUtilisateur;
use App\Tenancy\EntrepriseCourante;
use App\Tenancy\InscriptionEntreprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Paramètres de l'entreprise : identité, coordonnées et personnalisation
 * (logo, couleur). Réservé à l'administrateur de l'entreprise.
 */
class ParametresController extends Controller
{
  public const FUSEAUX = ['Africa/Douala', 'Africa/Lagos', 'Africa/Abidjan', 'Africa/Dakar', 'Africa/Kinshasa', 'Europe/Paris'];

  public function edit(EntrepriseCourante $courante)
  {
    if ($redirection = $this->reserveAdministrateur()) {
        return $redirection;
    }
    $entreprise = $courante->entreprise();
    $fuseaux = self::FUSEAUX;
    return view('admin.pages.parametres', compact('entreprise', 'fuseaux'));
  }

  public function update(Request $request, EntrepriseCourante $courante)
  {
    if ($redirection = $this->reserveAdministrateur()) {
        return $redirection;
    }
    $entreprise = $courante->entreprise();
    $donnees = $request->validate([
        'name' => 'bail|required|string|min:2|max:255',
        'email' => 'nullable|email|max:255',
        'telephone' => ['nullable', 'regex:/^[6,2][0-9]{8}$/'],
        'telephone_secondaire' => ['nullable', 'regex:/^[6,2][0-9]{8}$/'],
        'adresse' => 'nullable|string|max:255',
        'couleur_principale' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
        'fuseau_horaire' => 'required|in:'.implode(',', self::FUSEAUX),
        'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:1024',
        'supprimer_logo' => 'nullable|boolean',
    ]);

    if ($request->hasFile('logo') || $request->boolean('supprimer_logo')) {
        if ($entreprise->logo) {
            Storage::disk('local')->delete($entreprise->logo);
        }
        $entreprise->logo = $request->hasFile('logo')
            ? $request->file('logo')->store('entreprises/'.$entreprise->id, 'local')
            : null;
    }

    $entreprise->fill(collect($donnees)->except(['logo', 'supprimer_logo'])->all());
    $entreprise->save();

    session()->flash('message', "Paramètres enregistrés avec <b class='text-success'>Succès</b>");
    return redirect()->route('parametres.edit');
  }

  /**
   * Logo d'une entreprise, servi depuis le stockage privé (pas besoin de storage:link).
   */
  public function logo(string $slug)
  {
    $entreprise = Entreprise::where('slug', $slug)->firstOrFail();
    abort_if(! $entreprise->logo || ! Storage::disk('local')->exists($entreprise->logo), 404);

    return Storage::disk('local')->response($entreprise->logo, null, ['Cache-Control' => 'public, max-age=86400']);
  }

  protected function reserveAdministrateur()
  {
    $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
    if ($type != strtoupper(InscriptionEntreprise::ROLE_ADMINISTRATEUR)) {
        session()->flash('message', "<b class='text-danger'>Accès réservé</b> à l'administrateur de l'entreprise.");
        return redirect()->route('home');
    }
    return null;
  }
}
