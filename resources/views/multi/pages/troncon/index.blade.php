@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')

@section('title')
{{'Tronçons de route'}}
@endsection
@section('contenu')
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-12 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Tronçons de route</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a></li>
                                <li class="breadcrumb-item active">Tronçons de route</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card pl-1 pr-1">
            <div class="content-body">
                <div class="row justify-content-between bg-light-secondary">
                    <a class="btn btn-primary m-1" href="{{route('troncon.create')}}" title="Ajouter un tronçon"><i data-feather="plus"></i></a>
                    <form action="{{ route('troncon.index') }}" class="col-lg-3 mt-1" id="search" method="GET">
                        <div class="form-group">
                            <div class="input-group input-group-merge">
                                <div class="input-group-prepend"><span class="input-group-text"><i data-feather='search'></i></span></div>
                                <input type="search" class="form-control" name="recherche" value="{{ session()->get('search') }}" placeholder="Quartier, état de route…">
                            </div>
                        </div>
                    </form>
                </div>
                <p class="text-muted px-1 pt-1 mb-0">
                    <i data-feather="info" class="mr-25"></i> Les tronçons décrivent le réseau routier entre quartiers (distance, état de la chaussée, fermetures horaires, véhicules interdits).
                    L'optimiseur de tournées s'en sert pour calculer les meilleurs itinéraires ; sans tronçon, il estime les distances à partir des coordonnées GPS des quartiers.
                </p>
                <div class="row" id="table-without-card">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr class="text-center">
                                    <th>N°</th>
                                    <th>Départ</th>
                                    <th>Arrivée</th>
                                    <th>Distance</th>
                                    <th>Durée</th>
                                    <th>État</th>
                                    <th>Restrictions</th>
                                    @if(filter(['routeur','superviseur_ville'],auth()->user()) != 'true')
                                    <th>Ville</th>
                                    @endif
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $count = 20 * $troncons->currentPage() - 19; @endphp
                                @forelse($troncons as $troncon)
                                <tr class="text-center">
                                    <td>{{ $count++ }}</td>
                                    <td class="font-weight-bolder">{{ $troncon->quartier_depart->libelle ?? '—' }}</td>
                                    <td class="font-weight-bolder">{{ $troncon->bidirectionnel ? '⇄' : '→' }} {{ $troncon->quartier_arrivee->libelle ?? '—' }}</td>
                                    <td>{{ number_format($troncon->distance_km, 1, ',', ' ') }} km</td>
                                    <td>{{ $troncon->duree_minutes !== null ? round($troncon->duree_minutes).' min' : 'auto' }}</td>
                                    <td>
                                        @php $couleur = ['bon' => 'success', 'moyen' => 'info', 'degrade' => 'warning', 'impraticable' => 'danger'][$troncon->etat_route] ?? 'secondary'; @endphp
                                        <span class="badge badge-pill badge-light-{{ $couleur }}">{{ \App\Models\Troncon_route::ETATS[$troncon->etat_route] ?? $troncon->etat_route }}</span>
                                    </td>
                                    <td class="text-left">
                                        @if($troncon->heure_debut_fermeture)
                                            <small class="d-block"><i data-feather="clock" class="mr-25"></i> Fermé {{ substr($troncon->heure_debut_fermeture, 0, 5) }} → {{ substr($troncon->heure_fin_fermeture, 0, 5) }}</small>
                                        @endif
                                        @if(!empty($troncon->types_vehicule_interdits))
                                            <small class="d-block"><i data-feather="slash" class="mr-25"></i> {{ count($troncon->types_vehicule_interdits) }} type(s) de véhicule interdit(s)</small>
                                        @endif
                                        @if(!$troncon->heure_debut_fermeture && empty($troncon->types_vehicule_interdits))
                                            <small class="text-muted">Aucune</small>
                                        @endif
                                    </td>
                                    @if(filter(['routeur','superviseur_ville'],auth()->user()) != 'true')
                                    <td>{{ $troncon->ville->libelle ?? '—' }}</td>
                                    @endif
                                    <td>
                                        <span class="badge badge-pill badge-light-{{ $troncon->statut ? 'success' : 'danger' }}">{{ $troncon->statut ? 'Actif' : 'Désactivé' }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown" style="margin-bottom: 0px !important;"><i data-feather="more-vertical"></i></button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{route('troncon.edit', $troncon->id)}}"><i data-feather="edit-2" class="mr-50 text-info"></i><span class="ml-1">Editer</span></a>
                                                <span class="dropdown-item" data-toggle="modal" onclick="remplir('{{route('troncon.destroy', $troncon->id)}}','{{ ($troncon->quartier_depart->libelle ?? '').' → '.($troncon->quartier_arrivee->libelle ?? '') }}',' {{ $troncon->statut ? 'désactiver' : 'activer' }} le tronçon ','button_footer')" data-target="#danger">
                                                    <i data-feather='{{ $troncon->statut ? 'x' : 'check' }}' class="text-{{ $troncon->statut ? 'danger' : 'success' }}"></i>
                                                    <span class="ml-1" type="button">{{ $troncon->statut ? 'Désactiver' : 'Activer' }}</span>
                                                </span>
                                                <span class="dropdown-item" data-toggle="modal" onclick="remplir('{{route('troncon.destroy', $troncon->id)}}','{{ ($troncon->quartier_depart->libelle ?? '').' → '.($troncon->quartier_arrivee->libelle ?? '') }}',' supprimer le tronçon ','button_delete')" data-target="#danger">
                                                    <i data-feather='trash-2' class="text-danger"></i>
                                                    <span class="ml-1" type="button">Supprimer</span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="10" class="text-center text-muted p-2">Aucun tronçon enregistré. <a href="{{ route('troncon.create') }}">Ajouter le premier tronçon</a>.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-1">
                    {{ $troncons->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
<div id="button_delete" hidden>
    <form method="POST" class="destroy_form">
        @csrf
        @method('DELETE')
        <input type="hidden" name="type" value="delete">
        <button type="submit" class="btn btn-gradient-danger btn-danger">Supprimer</button>
    </form>
    <button type="button" class="btn btn-gradient-secondary btn-secondary" data-dismiss="modal">Annuler</button>
</div>
<div id="button_footer" hidden>
    <form method="POST" class="destroy_form">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-gradient-success btn-success">Confirmer</button>
    </form>
    <button type="button" class="btn btn-gradient-danger btn-danger" data-dismiss="modal">Annuler</button>
</div>
@endsection
