{{-- Partiel AJAX : comparatif itinéraire optimisé / ordre classique. --}}
@php
    use App\Services\Routage\Horaire;
    $gainDuree = $reference->dureeMinutes > 0 ? round(($reference->dureeMinutes - $optimise->dureeMinutes) / $reference->dureeMinutes * 100, 1) : 0;
    $gainDistance = $reference->distanceKm > 0 ? round(($reference->distanceKm - $optimise->distanceKm) / $reference->distanceKm * 100, 1) : 0;
    $gainCout = $reference->cout > 0 ? round(($reference->cout - $optimise->cout) / $reference->cout * 100, 1) : 0;
    $libQuartier = fn ($id) => $quartiers[$id]->libelle ?? ('Quartier #'.$id);
@endphp
<div class="row text-center mb-1">
    <div class="col-md-3 col-6 mb-1">
        <div class="border rounded p-1 h-100">
            <small class="text-muted d-block">Distance</small>
            <h3 class="mb-0">{{ number_format($optimise->distanceKm, 1, ',', ' ') }} km</h3>
            <small class="text-{{ $gainDistance > 0 ? 'success' : 'muted' }}">{{ $gainDistance > 0 ? '-'.$gainDistance.' %' : 'idem' }} <span class="text-muted">(ordre classique : {{ number_format($reference->distanceKm, 1, ',', ' ') }} km)</span></small>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-1">
        <div class="border rounded p-1 h-100">
            <small class="text-muted d-block">Durée totale</small>
            <h3 class="mb-0">{{ Horaire::formatDuree($optimise->dureeMinutes) }}</h3>
            <small class="text-{{ $gainDuree > 0 ? 'success' : 'muted' }}">{{ $gainDuree > 0 ? '-'.$gainDuree.' %' : 'idem' }} <span class="text-muted">(ordre classique : {{ Horaire::formatDuree($reference->dureeMinutes) }})</span></small>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-1">
        <div class="border rounded p-1 h-100">
            <small class="text-muted d-block">Coût d'exploitation</small>
            <h3 class="mb-0">{{ $optimise->cout > 0 ? number_format($optimise->cout, 0, ',', ' ').' F' : '—' }}</h3>
            @if($reference->cout > 0)
                <small class="text-{{ $gainCout > 0 ? 'success' : 'muted' }}">{{ $gainCout > 0 ? '-'.$gainCout.' %' : 'idem' }} <span class="text-muted">(soit {{ number_format(max(0, $reference->cout - $optimise->cout), 0, ',', ' ') }} F économisés)</span></small>
            @else
                <small class="text-muted">Renseignez les coûts du type de véhicule</small>
            @endif
        </div>
    </div>
    <div class="col-md-3 col-6 mb-1">
        <div class="border rounded p-1 h-100">
            <small class="text-muted d-block">Fin estimée</small>
            <h3 class="mb-0">{{ date('H:i', $optimise->heureFin) }}</h3>
            <small class="text-{{ $optimise->retardMinutes > 0 ? 'danger' : 'success' }}">
                {{ $optimise->retardMinutes > 0 ? 'Retard cumulé : '.Horaire::formatDuree($optimise->retardMinutes) : 'Aucun retard' }}
                @if($reference->retardMinutes > $optimise->retardMinutes) <span class="text-muted">(ordre classique : {{ Horaire::formatDuree($reference->retardMinutes) }} de retard)</span> @endif
            </small>
        </div>
    </div>
</div>

@if(!empty($optimise->avertissements))
<div class="alert alert-warning p-1 mb-1">
    <strong><i data-feather="alert-triangle" class="mr-25"></i> Points d'attention</strong>
    <ul class="mb-0 pl-2">
        @foreach($optimise->avertissements as $a) <li>{{ $a }}</li> @endforeach
    </ul>
</div>
@endif

<div class="table-responsive">
    <table class="table table-sm mb-0">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Étape</th>
                <th>Quartier</th>
                <th>Arrivée</th>
                <th>Trajet</th>
                <th>Itinéraire</th>
            </tr>
        </thead>
        <tbody>
            @foreach($optimise->etapes as $e)
            @php $cmd = $commandes[$e['arret']->idCommande] ?? null; @endphp
            <tr class="{{ $e['retard_minutes'] > 0 ? 'table-danger' : '' }}">
                <td class="font-weight-bolder">{{ $e['ordre'] }}</td>
                <td>
                    <span class="badge badge-pill badge-light-{{ $e['arret']->estEnlevement() ? 'warning' : 'success' }}">{{ $e['arret']->estEnlevement() ? 'Enlèvement' : 'Livraison' }}</span>
                    n°{{ $e['arret']->idCommande }}
                    @if($cmd) <small class="text-muted d-block">{{ $cmd->nom_client ?: ($cmd->client->noms ?? '') }} · {{ $e['arret']->estEnlevement() ? $cmd->adresse_colis : $cmd->adresse_livraison }}</small> @endif
                </td>
                <td>{{ $libQuartier($e['arret']->idQuartier) }}</td>
                <td>
                    {{ date('H:i', $e['heure_arrivee']) }}
                    @if($e['attente_minutes'] > 0) <small class="text-warning d-block">attente {{ Horaire::formatDuree($e['attente_minutes']) }}</small> @endif
                    @if($e['retard_minutes'] > 0) <small class="text-danger d-block">retard {{ Horaire::formatDuree($e['retard_minutes']) }}</small> @endif
                </td>
                <td>{{ number_format($e['distance_km'], 1, ',', ' ') }} km · {{ Horaire::formatDuree($e['duree_minutes']) }}{!! $e['estime'] ? ' <small class="text-muted" title="Aucun tronçon renseigné : estimation">≈</small>' : '' !!}</td>
                <td><small>{{ implode(' → ', array_map($libQuartier, $e['chemin'])) }}</small></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<p class="text-muted mt-1 mb-0"><small>Ordre classique = enchaînement des livraisons par date promise. Les durées tiennent compte de l'état des routes, du trafic horaire, des fermetures de tronçons et de la plage de circulation du véhicule.</small></p>
