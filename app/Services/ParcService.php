<?php

namespace App\Services;

use App\Models\coursiers\Coursiers;
use App\Models\Parc\Vehicule_attribution;
use App\Models\Parc\Vehicule_document;
use App\Models\Parc\Vehicule_incident;
use App\Models\Parc\Vehicule_maintenance;
use App\Models\Vehicule;
use Carbon\Carbon;

/**
 * Règles métier de la gestion de parc (attribution, alertes, indicateurs).
 */
class ParcService
{
    /**
     * Attribue un véhicule à un coursier en journalisant l'attribution.
     * Une attribution en cours est clôturée au préalable.
     */
    public function attribuer(Vehicule $vehicule, $id_coursier, $kilometrage = null, $id_saver = null)
    {
        $coursier = Coursiers::findOrFail($id_coursier);
        $this->cloturerAttribution($vehicule, $kilometrage, $id_saver);

        if ($kilometrage !== null && $kilometrage !== '') {
            $vehicule->enregistrerKilometrage($kilometrage, 'attribution', null, 'Attribution à '.$coursier->noms.' '.$coursier->prenoms, $id_saver);
        }

        $attribution = new Vehicule_attribution;
        $attribution->id_vehicule = $vehicule->id;
        $attribution->id_coursier = $coursier->id;
        $attribution->date_debut = Carbon::now();
        $attribution->kilometrage_debut = $kilometrage !== null && $kilometrage !== '' ? (int) $kilometrage : $vehicule->kilometrage;
        $attribution->id_saver = $id_saver;
        $attribution->save();

        $vehicule->id_coursier = $coursier->id;
        if ($vehicule->etat == 'disponible') {
            $vehicule->etat = 'en_service';
        }
        $vehicule->save();

        return $attribution;
    }

    /**
     * Retire le véhicule à son coursier actuel et clôture l'attribution.
     */
    public function desattribuer(Vehicule $vehicule, $kilometrage = null, $id_saver = null)
    {
        $this->cloturerAttribution($vehicule, $kilometrage, $id_saver);
        if ($kilometrage !== null && $kilometrage !== '') {
            $vehicule->enregistrerKilometrage($kilometrage, 'attribution', null, 'Restitution du véhicule', $id_saver);
        }
        $vehicule->id_coursier = null;
        if ($vehicule->etat == 'en_service') {
            $vehicule->etat = 'disponible';
        }
        $vehicule->save();
    }

    protected function cloturerAttribution(Vehicule $vehicule, $kilometrage = null, $id_saver = null)
    {
        $en_cours = $vehicule->attributionEnCours();
        if ($en_cours === null && $vehicule->id_coursier !== null) {
            // Véhicule attribué avant la mise en place de l'historique : on reconstitue l'entrée.
            $en_cours = new Vehicule_attribution;
            $en_cours->id_vehicule = $vehicule->id;
            $en_cours->id_coursier = $vehicule->id_coursier;
            $en_cours->date_debut = $vehicule->updated_at ?: Carbon::now();
            $en_cours->id_saver = $id_saver;
        }
        if ($en_cours !== null) {
            $en_cours->date_fin = Carbon::now();
            $en_cours->kilometrage_fin = $kilometrage !== null && $kilometrage !== '' ? (int) $kilometrage : $vehicule->kilometrage;
            $en_cours->save();
        }
    }

    /**
     * Construit la liste des alertes du parc pour un ensemble de véhicules :
     * documents expirés / à renouveler, entretiens en retard / à venir,
     * incidents ouverts, véhicules immobilisés.
     *
     * @return \Illuminate\Support\Collection  éléments [niveau, type, vehicule, message, date, lien]
     */
    public function alertes($vehicules, $jours_documents = 30, $jours_maintenance = 15)
    {
        $alertes = collect();
        $ids = $vehicules->pluck('id');
        if ($ids->isEmpty()) {
            return $alertes;
        }
        $today = Carbon::now()->startOfDay();
        $vehicules_par_id = $vehicules->keyBy('id');

        // Documents
        $limite_doc = $today->copy()->addDays($jours_documents)->toDateString();
        $documents = Vehicule_document::whereIn('id_vehicule', $ids)
            ->whereNotNull('date_expiration')
            ->where('date_expiration', '<=', $limite_doc)
            ->orderBy('date_expiration')
            ->get();
        foreach ($documents as $doc) {
            $vehicule = $vehicules_par_id[$doc->id_vehicule];
            $jours = $doc->jours_restants;
            $alertes->push([
                'niveau'   => $jours < 0 ? 'danger' : 'warning',
                'type'     => 'document',
                'icone'    => 'file-text',
                'vehicule' => $vehicule,
                'titre'    => $doc->type_libelle,
                'message'  => $jours < 0
                    ? 'Expiré depuis '.abs($jours).' jour(s)'
                    : ($jours == 0 ? 'Expire aujourd\'hui' : 'Expire dans '.$jours.' jour(s)'),
                'date'     => $doc->date_expiration,
                'onglet'   => 'documents',
            ]);
        }

        // Entretiens
        $limite_maint = $today->copy()->addDays($jours_maintenance)->toDateString();
        $maintenances = Vehicule_maintenance::whereIn('id_vehicule', $ids)
            ->whereIn('statut', ['planifie', 'en_cours'])
            ->get();
        foreach ($maintenances as $m) {
            $vehicule = $vehicules_par_id[$m->id_vehicule];
            $message = null;
            $niveau = 'info';
            if ($m->date_prevue != null && $m->date_prevue <= $limite_maint) {
                $jours = (int) $today->diffInDays(Carbon::parse($m->date_prevue), false);
                if ($jours < 0) {
                    $niveau = 'danger';
                    $message = 'En retard de '.abs($jours).' jour(s)';
                } else {
                    $niveau = 'warning';
                    $message = $jours == 0 ? 'Prévu aujourd\'hui' : 'Prévu dans '.$jours.' jour(s)';
                }
            } elseif ($m->prochain_kilometrage != null && $vehicule->kilometrage + 500 >= $m->prochain_kilometrage) {
                $reste = $m->prochain_kilometrage - $vehicule->kilometrage;
                $niveau = $reste <= 0 ? 'danger' : 'warning';
                $message = $reste <= 0
                    ? 'Kilométrage dépassé de '.abs($reste).' km'
                    : 'Dans '.$reste.' km';
            }
            if ($message !== null) {
                $alertes->push([
                    'niveau'   => $niveau,
                    'type'     => 'maintenance',
                    'icone'    => 'tool',
                    'vehicule' => $vehicule,
                    'titre'    => $m->libelle,
                    'message'  => $message,
                    'date'     => $m->date_prevue,
                    'onglet'   => 'entretiens',
                ]);
            }
        }

        // Incidents ouverts
        $incidents = Vehicule_incident::whereIn('id_vehicule', $ids)
            ->whereIn('statut', ['ouvert', 'en_cours'])
            ->orderBy('date', 'desc')
            ->get();
        foreach ($incidents as $i) {
            $alertes->push([
                'niveau'   => $i->immobilise ? 'danger' : 'warning',
                'type'     => 'incident',
                'icone'    => 'alert-triangle',
                'vehicule' => $vehicules_par_id[$i->id_vehicule],
                'titre'    => $i->type_libelle,
                'message'  => ($i->immobilise ? 'Véhicule immobilisé. ' : '').cutText($i->description, 60),
                'date'     => $i->date,
                'onglet'   => 'incidents',
            ]);
        }

        // Véhicules immobilisés / hors service
        foreach ($vehicules as $vehicule) {
            if (in_array($vehicule->etat, ['immobilise', 'hors_service'])) {
                $alertes->push([
                    'niveau'   => 'danger',
                    'type'     => 'etat',
                    'icone'    => 'slash',
                    'vehicule' => $vehicule,
                    'titre'    => $vehicule->etat_libelle,
                    'message'  => 'Le véhicule n\'est pas opérationnel',
                    'date'     => $vehicule->updated_at,
                    'onglet'   => 'resume',
                ]);
            }
        }

        $ordre = ['danger' => 0, 'warning' => 1, 'info' => 2];
        return $alertes->sortBy(function ($a) use ($ordre) {
            return $ordre[$a['niveau']].'-'.($a['date'] ?? '9999');
        })->values();
    }
}
