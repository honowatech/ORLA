<?php

namespace App\Http\Controllers\Facturation;

use App\Enums\EtatAbonnement;
use App\Http\Controllers\Controller;
use App\Models\SuperAdmin\Contact;
use App\Services\Abonnement\AbonnementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Pages d'information affichées à la place de l'espace de travail lorsque
 * l'abonnement de l'entreprise est expiré ou que l'espace est suspendu.
 */
class AbonnementStatutController extends Controller
{
    public function __construct(private readonly AbonnementService $abonnements) {}

    public function expire(Request $request): View|RedirectResponse
    {
        $etat = $this->abonnements->etat(entreprise());

        if ($etat->permetAcces()) {
            return redirect()->route('home');
        }

        if ($etat === EtatAbonnement::Suspendue) {
            return redirect()->route('abonnement.suspendue');
        }

        return view('abonnement.expire', $this->donnees($request));
    }

    public function suspendue(Request $request): View|RedirectResponse
    {
        $etat = $this->abonnements->etat(entreprise());

        if ($etat->permetAcces()) {
            return redirect()->route('home');
        }

        if ($etat !== EtatAbonnement::Suspendue) {
            return redirect()->route('abonnement.expire');
        }

        return view('abonnement.suspendue', $this->donnees($request));
    }

    private function donnees(Request $request): array
    {
        return [
            'entreprise' => entreprise(),
            'telephones' => array_filter(explode('/', (string) Contact::where('name', 'phone')->value('value'))),
            'email' => Contact::where('name', 'email')->value('value'),
            'peutSouscrire' => (bool) $request->user()?->estAdministrateur(),
        ];
    }
}
