@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')

@section('title')
{{'Feuille de route — Tournée #'.$tournee->id}}
@endsection
@section('contenu')
@php
    use App\Services\Routage\Horaire;
    $libQuartier = fn ($id) => $quartiers[$id]->libelle ?? ('Quartier #'.$id);
    $couleur = ['planifiee' => 'info', 'encours' => 'warning', 'terminee' => 'success', 'annulee' => 'danger'][$tournee->statut] ?? 'secondary';
    $points = collect([$tournee->id_quartier_depart])->merge($tournee->etapes->pluck('id_quartier'))
        ->map(fn ($id) => isset($quartiers[$id]) && $quartiers[$id]->latitude !== null ? ['lat' => (float) $quartiers[$id]->latitude, 'lng' => (float) $quartiers[$id]->longitude, 'nom' => $quartiers[$id]->libelle] : null)
        ->filter()->values();
@endphp
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-12 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Tournée #{{ $tournee->id }} <span class="badge badge-pill badge-light-{{ $couleur }} ml-1">{{ \App\Models\tournee\Tournee::STATUTS[$tournee->statut] }}</span></h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a></li>
                                <li class="breadcrumb-item"><a href="{{route('tournee.index')}}">Tournées</a></li>
                                <li class="breadcrumb-item active">Feuille de route</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="row">
                <div class="col-lg-4 col-12">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><i data-feather="info" class="mr-50"></i> Informations</h4></div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-5">Livreur</dt><dd class="col-7">{{ $tournee->coursier->noms ?? '' }} {{ $tournee->coursier->prenoms ?? '' }}<br><small class="text-muted">{{ $tournee->coursier->telephone ?? '' }}</small></dd>
                                <dt class="col-5">Véhicule</dt><dd class="col-7">{{ $tournee->vehicule ? $tournee->vehicule->immatriculation.' — '.($tournee->vehicule->type->libelle ?? '') : 'Non précisé' }}</dd>
                                <dt class="col-5">Départ</dt><dd class="col-7">{{ $tournee->date_depart->format('d/m/Y H:i') }}<br><small class="text-muted">{{ $tournee->quartier_depart->libelle ?? '' }}</small></dd>
                                <dt class="col-5">Fin estimée</dt><dd class="col-7">{{ $tournee->date_fin_estimee?->format('d/m/Y H:i') }}</dd>
                                <dt class="col-5">Ville</dt><dd class="col-7">{{ $tournee->ville->libelle ?? '' }}</dd>
                                <dt class="col-5">Planifiée par</dt><dd class="col-7">{{ $tournee->enregistreur->noms ?? '' }} <small class="text-muted">le {{ $tournee->created_at->format('d/m/Y H:i') }}</small></dd>
                            </dl>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><i data-feather="trending-down" class="mr-50"></i> Performance</h4></div>
                        <div class="card-body">
                            <table class="table table-sm mb-1">
                                <thead><tr><th></th><th class="text-right">Optimisé</th><th class="text-right text-muted">Classique</th><th class="text-right">Gain</th></tr></thead>
                                <tbody>
                                    <tr><td>Distance</td><td class="text-right">{{ number_format($tournee->distance_totale_km, 1, ',', ' ') }} km</td><td class="text-right text-muted">{{ number_format($tournee->distance_reference_km, 1, ',', ' ') }} km</td><td class="text-right text-{{ $tournee->gainPourcent('distance') > 0 ? 'success' : 'muted' }}">{{ $tournee->gainPourcent('distance') }} %</td></tr>
                                    <tr><td>Durée</td><td class="text-right">{{ Horaire::formatDuree($tournee->duree_totale_minutes) }}</td><td class="text-right text-muted">{{ Horaire::formatDuree($tournee->duree_reference_minutes) }}</td><td class="text-right text-{{ $tournee->gainPourcent('duree') > 0 ? 'success' : 'muted' }}">{{ $tournee->gainPourcent('duree') }} %</td></tr>
                                    <tr><td>Coût</td><td class="text-right">{{ $tournee->cout_total > 0 ? number_format($tournee->cout_total, 0, ',', ' ').' F' : '—' }}</td><td class="text-right text-muted">{{ $tournee->cout_reference > 0 ? number_format($tournee->cout_reference, 0, ',', ' ').' F' : '—' }}</td><td class="text-right text-{{ $tournee->gainPourcent('cout') > 0 ? 'success' : 'muted' }}">{{ $tournee->cout_reference > 0 ? $tournee->gainPourcent('cout').' %' : '—' }}</td></tr>
                                </tbody>
                            </table>
                            @if(!empty($tournee->avertissements))
                                <div class="alert alert-warning p-1 mb-0">
                                    <ul class="mb-0 pl-1">
                                        @foreach($tournee->avertissements as $a) <li><small>{{ $a }}</small></li> @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            @if($tournee->statut === 'planifiee')
                                <form method="POST" action="{{ route('tournee.update', $tournee->id) }}" class="mb-1">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="statut" value="encours">
                                    <button class="btn btn-warning btn-block"><i data-feather="play" class="mr-50"></i> Démarrer la tournée</button>
                                </form>
                            @endif
                            @if($tournee->statut === 'encours')
                                <form method="POST" action="{{ route('tournee.update', $tournee->id) }}" class="mb-1">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="statut" value="terminee">
                                    <button class="btn btn-success btn-block"><i data-feather="check" class="mr-50"></i> Clôturer la tournée</button>
                                </form>
                            @endif
                            @if(in_array($tournee->statut, ['planifiee', 'encours']))
                                <form method="POST" action="{{ route('tournee.destroy', $tournee->id) }}" onsubmit="return confirm('Annuler cette tournée ? Les commandes repasseront en attente.');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-block"><i data-feather="x" class="mr-50"></i> Annuler la tournée</button>
                                </form>
                            @endif
                            <a href="{{ route('tournee.index') }}" class="btn btn-outline-secondary btn-block mt-1"><i data-feather="arrow-left" class="mr-50"></i> Retour à la liste</a>
                            <button type="button" class="btn btn-outline-primary btn-block" onclick="window.print()"><i data-feather="printer" class="mr-50"></i> Imprimer la feuille de route</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-12">
                    @if($points->count() >= 2)
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><i data-feather="map" class="mr-50"></i> Carte</h4></div>
                        <div class="card-body p-0"><div id="carte" style="height: 380px; width: 100%;"></div></div>
                    </div>
                    @endif
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><i data-feather="list" class="mr-50"></i> Étapes ({{ $tournee->etapes->count() }})</h4></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="thead-light">
                                        <tr><th>#</th><th>Étape</th><th>Quartier / adresse</th><th>Client</th><th>Arrivée</th><th>Trajet</th><th>Itinéraire</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-active">
                                            <td>0</td><td><span class="badge badge-pill badge-light-primary">Départ</span></td>
                                            <td>{{ $tournee->quartier_depart->libelle ?? '' }}</td><td></td>
                                            <td>{{ $tournee->date_depart->format('H:i') }}</td><td></td><td></td>
                                        </tr>
                                        @foreach($tournee->etapes as $e)
                                        @php $cmd = $e->commande; @endphp
                                        <tr class="{{ $e->retard_minutes > 0 ? 'table-danger' : '' }}">
                                            <td class="font-weight-bolder">{{ $e->ordre }}</td>
                                            <td><span class="badge badge-pill badge-light-{{ $e->type_etape == 'enlevement' ? 'warning' : 'success' }}">{{ $e->type_etape == 'enlevement' ? 'Enlèvement' : 'Livraison' }}</span><br><small>n°{{ $e->id_commande }}</small></td>
                                            <td>{{ $e->quartier->libelle ?? '' }}<br><small class="text-muted">{{ $cmd ? ($e->type_etape == 'enlevement' ? $cmd->adresse_colis : $cmd->adresse_livraison) : '' }}</small></td>
                                            <td>{{ $cmd ? ($cmd->nom_client ?: ($cmd->client->noms ?? '')) : '' }}<br><small class="text-muted">{{ $cmd->telephone ?? '' }}</small></td>
                                            <td>
                                                {{ $e->heure_arrivee_estimee?->format('H:i') }}
                                                @if($e->attente_minutes > 0) <small class="text-warning d-block">attente {{ Horaire::formatDuree($e->attente_minutes) }}</small> @endif
                                                @if($e->retard_minutes > 0) <small class="text-danger d-block">retard {{ Horaire::formatDuree($e->retard_minutes) }}</small> @endif
                                                @if($cmd && $cmd->statut == 'livre') <small class="text-success d-block">✔ livrée</small> @endif
                                            </td>
                                            <td>{{ number_format($e->distance_etape_km, 1, ',', ' ') }} km · {{ Horaire::formatDuree($e->duree_etape_minutes) }}</td>
                                            <td><small>{{ implode(' → ', array_map($libQuartier, $e->itineraire ?? [])) }}</small></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('css')
@if($points->count() >= 2)
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endif
@endsection
@section('javascript')
@if($points->count() >= 2)
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    (function () {
        var points = @json($points);
        var carte = L.map('carte');
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(carte);
        var latlngs = points.map(function (p) { return [p.lat, p.lng]; });
        points.forEach(function (p, i) {
            L.marker([p.lat, p.lng]).addTo(carte).bindTooltip((i === 0 ? 'Départ' : i) + ' · ' + p.nom, { permanent: i === 0, direction: 'top' });
        });
        L.polyline(latlngs, { color: '#7367f0', weight: 4, opacity: .8 }).addTo(carte);
        carte.fitBounds(L.latLngBounds(latlngs), { padding: [30, 30] });
    })();
</script>
@endif
@endsection
