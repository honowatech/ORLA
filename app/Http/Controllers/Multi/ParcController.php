<?php

namespace App\Http\Controllers\Multi;

use App\Http\Controllers\Multi\Concerns\ParcAccess;
use App\Models\Parc\Vehicule_carburant;
use App\Models\Parc\Vehicule_document;
use App\Models\Parc\Vehicule_incident;
use App\Models\Parc\Vehicule_maintenance;
use App\Models\Vehicule;
use App\Services\ParcService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Tableau de bord, alertes, analyse des coûts et export du parc automobile.
 */
class ParcController extends Controller
{
  use ParcAccess;

  /**
   * Résout la période demandée (par défaut : le mois en cours).
   *
   * @return array [debut, fin] au format Y-m-d
   */
  protected function periode(Request $request)
  {
    $debut = $request->input('debut');
    $fin = $request->input('fin');
    try {
      $debut = $debut ? Carbon::parse($debut)->toDateString() : Carbon::now()->startOfMonth()->toDateString();
      $fin = $fin ? Carbon::parse($fin)->toDateString() : Carbon::now()->endOfMonth()->toDateString();
    } catch (\Exception $e) {
      $debut = Carbon::now()->startOfMonth()->toDateString();
      $fin = Carbon::now()->endOfMonth()->toDateString();
    }
    if ($debut > $fin) {
      [$debut, $fin] = [$fin, $debut];
    }
    return [$debut, $fin];
  }

  /**
   * Tableau de bord du parc.
   */
  public function index(Request $request, ParcService $parc)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $vehicules = $this->vehiculesAccessibles()->with(['type', 'coursier', 'ville'])->get();
    $ids = $vehicules->pluck('id');
    $actifs = $vehicules->where('statut', 1);

    $par_etat = [];
    foreach (Vehicule::ETATS as $cle => $libelle) {
      $par_etat[$cle] = $actifs->where('etat', $cle)->count();
    }

    $today = Carbon::now();
    $debut_mois = $today->copy()->startOfMonth()->toDateString();
    $fin_mois = $today->copy()->endOfMonth()->toDateString();
    $debut_prec = $today->copy()->subMonth()->startOfMonth()->toDateString();
    $fin_prec = $today->copy()->subMonth()->endOfMonth()->toDateString();

    $couts = [
      'mois' => $this->coutsPeriode($ids, $debut_mois, $fin_mois),
      'precedent' => $this->coutsPeriode($ids, $debut_prec, $fin_prec),
    ];

    $limite_doc = $today->copy()->addDays(30)->toDateString();
    $stats = [
      'total' => $vehicules->count(),
      'actifs' => $actifs->count(),
      'attribues' => $actifs->whereNotNull('id_coursier')->count(),
      'disponibles' => $par_etat['disponible'],
      'documents_expires' => Vehicule_document::whereIn('id_vehicule', $ids)->whereNotNull('date_expiration')->where('date_expiration', '<', $today->toDateString())->count(),
      'documents_bientot' => Vehicule_document::whereIn('id_vehicule', $ids)->whereNotNull('date_expiration')->whereBetween('date_expiration', [$today->toDateString(), $limite_doc])->count(),
      'maintenances_retard' => Vehicule_maintenance::whereIn('id_vehicule', $ids)->whereIn('statut', ['planifie', 'en_cours'])->whereNotNull('date_prevue')->where('date_prevue', '<', $today->toDateString())->count(),
      'maintenances_planifiees' => Vehicule_maintenance::whereIn('id_vehicule', $ids)->whereIn('statut', ['planifie', 'en_cours'])->count(),
      'incidents_ouverts' => Vehicule_incident::whereIn('id_vehicule', $ids)->whereIn('statut', ['ouvert', 'en_cours'])->count(),
      'litres_mois' => (float) Vehicule_carburant::whereIn('id_vehicule', $ids)->whereBetween('date', [$debut_mois, $fin_mois])->sum('litres'),
      'kilometrage_total' => (int) $vehicules->sum('kilometrage'),
    ];

    $alertes = $parc->alertes($vehicules);

    // Top véhicules par coût du mois
    $top_couts = $vehicules->map(function ($v) use ($debut_mois, $fin_mois) {
      return ['vehicule' => $v, 'cout' => $v->coutTotal($debut_mois, $fin_mois)];
    })->filter(function ($l) { return $l['cout'] > 0; })->sortByDesc('cout')->take(5)->values();

    $prochains_entretiens = Vehicule_maintenance::whereIn('id_vehicule', $ids)
      ->whereIn('statut', ['planifie', 'en_cours'])
      ->with('vehicule')
      ->orderByRaw('date_prevue IS NULL')
      ->orderBy('date_prevue')
      ->take(8)->get();

    $derniers_incidents = Vehicule_incident::whereIn('id_vehicule', $ids)
      ->with(['vehicule', 'coursier'])
      ->orderBy('date', 'desc')->take(6)->get();

    // Répartition par type de véhicule
    $par_type = $actifs->groupBy(function ($v) { return $v->type ? $v->type->libelle : 'Non défini'; })
      ->map->count()->sortDesc();

    return view('multi.pages.parc.index', compact(
      'stats', 'par_etat', 'couts', 'alertes', 'top_couts', 'prochains_entretiens', 'derniers_incidents', 'par_type', 'vehicules'
    ));
  }

  /**
   * Somme des coûts par poste sur une période.
   */
  protected function coutsPeriode($ids, $debut, $fin)
  {
    $carburant = (float) Vehicule_carburant::whereIn('id_vehicule', $ids)->whereBetween('date', [$debut, $fin])->sum('montant');
    $maintenance = (float) Vehicule_maintenance::whereIn('id_vehicule', $ids)->where('statut', 'realise')->whereBetween('date_realisation', [$debut, $fin])->sum('cout');
    $incidents = (float) Vehicule_incident::whereIn('id_vehicule', $ids)->whereBetween('date', [$debut.' 00:00:00', $fin.' 23:59:59'])->sum('cout');
    $documents = (float) Vehicule_document::whereIn('id_vehicule', $ids)->whereBetween('date_debut', [$debut, $fin])->sum('montant');
    return [
      'carburant' => $carburant,
      'maintenance' => $maintenance,
      'incidents' => $incidents,
      'documents' => $documents,
      'total' => $carburant + $maintenance + $incidents + $documents,
    ];
  }

  /**
   * Liste complète des alertes du parc.
   */
  public function alertes(Request $request, ParcService $parc)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $vehicules = $this->vehiculesAccessibles()->where('statut', 1)->with(['type', 'coursier'])->get();
    $alertes = $parc->alertes($vehicules, 60, 30);
    $type = $request->input('type');
    if (in_array($type, ['document', 'maintenance', 'incident', 'etat'])) {
      $alertes = $alertes->where('type', $type)->values();
    }
    return view('multi.pages.parc.alertes', compact('alertes', 'type'));
  }

  /**
   * Analyse des coûts par véhicule sur une période.
   */
  public function couts(Request $request)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    [$debut, $fin] = $this->periode($request);
    $vehicules = $this->vehiculesAccessibles()->with(['type', 'coursier'])->orderBy('immatriculation')->get();
    $lignes = $vehicules->map(function ($v) use ($debut, $fin) {
      $carburant = $v->coutCarburant($debut, $fin);
      $maintenance = $v->coutMaintenance($debut, $fin);
      $incidents = $v->coutIncidents($debut, $fin);
      $documents = $v->coutDocuments($debut, $fin);
      $litres = (float) $v->carburants()->whereBetween('date', [$debut, $fin])->sum('litres');
      $releves = $v->releves_kilometrage()->whereBetween('date', [$debut, $fin])->orderBy('kilometrage')->get();
      $distance = $releves->count() >= 2 ? max(0, $releves->last()->kilometrage - $releves->first()->kilometrage) : null;
      $total = $carburant + $maintenance + $incidents + $documents;
      return [
        'vehicule' => $v,
        'carburant' => $carburant,
        'litres' => $litres,
        'maintenance' => $maintenance,
        'incidents' => $incidents,
        'documents' => $documents,
        'total' => $total,
        'distance' => $distance,
        'cout_km' => $distance ? round($total / $distance, 2) : null,
      ];
    })->sortByDesc('total')->values();
    $totaux = [
      'carburant' => $lignes->sum('carburant'),
      'litres' => $lignes->sum('litres'),
      'maintenance' => $lignes->sum('maintenance'),
      'incidents' => $lignes->sum('incidents'),
      'documents' => $lignes->sum('documents'),
      'total' => $lignes->sum('total'),
      'distance' => $lignes->sum('distance'),
    ];
    return view('multi.pages.parc.couts', compact('lignes', 'totaux', 'debut', 'fin'));
  }

  /**
   * Export CSV (compatible Excel) de l'état du parc et des coûts de la période.
   */
  public function export(Request $request)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    [$debut, $fin] = $this->periode($request);
    $vehicules = $this->vehiculesAccessibles()->with(['type', 'ville', 'coursier'])->orderBy('immatriculation')->get();
    $nom = 'parc_automobile_'.$debut.'_'.$fin.'.csv';

    $response = new StreamedResponse(function () use ($vehicules, $debut, $fin) {
      $sortie = fopen('php://output', 'w');
      fwrite($sortie, "\xEF\xBB\xBF"); // BOM UTF-8 pour Excel
      fputcsv($sortie, [
        'Immatriculation', 'Type', 'Marque', 'Modèle', 'Année', 'Carburant', 'Ville', 'Coursier',
        'Statut', 'État', 'Kilométrage', 'Date acquisition', 'Prix acquisition',
        'Documents expirés', 'Entretiens planifiés', 'Incidents ouverts',
        'Carburant ('.$debut.' au '.$fin.')', 'Litres', 'Entretien', 'Incidents', 'Documents', 'Coût total période', 'Coût total cumulé',
      ], ';');
      foreach ($vehicules as $v) {
        fputcsv($sortie, [
          $v->immatriculation,
          $v->type ? $v->type->libelle : '',
          $v->marque,
          $v->modele,
          $v->annee,
          $v->carburant_libelle,
          $v->ville ? $v->ville->libelle : '',
          $v->coursier ? $v->coursier->noms.' '.$v->coursier->prenoms : '',
          $v->statut ? 'Actif' : 'Désactivé',
          $v->etat_libelle,
          $v->kilometrage,
          $v->date_acquisition,
          $v->prix_acquisition,
          $v->documents()->whereNotNull('date_expiration')->where('date_expiration', '<', Carbon::now()->toDateString())->count(),
          $v->maintenances()->whereIn('statut', ['planifie', 'en_cours'])->count(),
          $v->incidents()->whereIn('statut', ['ouvert', 'en_cours'])->count(),
          $v->coutCarburant($debut, $fin),
          (float) $v->carburants()->whereBetween('date', [$debut, $fin])->sum('litres'),
          $v->coutMaintenance($debut, $fin),
          $v->coutIncidents($debut, $fin),
          $v->coutDocuments($debut, $fin),
          $v->coutTotal($debut, $fin),
          $v->coutTotal(),
        ], ';');
      }
      fclose($sortie);
    });
    $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
    $response->headers->set('Content-Disposition', 'attachment; filename="'.$nom.'"');
    return $response;
  }
}
