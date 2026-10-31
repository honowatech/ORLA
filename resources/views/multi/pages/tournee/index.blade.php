@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')

@section('title')
{{'Tournées optimisées'}}
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
                        <h2 class="content-header-title float-left mb-0">Tournées optimisées</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a></li>
                                <li class="breadcrumb-item active">Tournées</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card pl-1 pr-1">
            <div class="content-body">
                <div class="row justify-content-between bg-light-secondary">
                    <a class="btn btn-primary m-1" href="{{route('tournee.create')}}"><i data-feather="plus" class="mr-25"></i> Planifier une tournée</a>
                    <form action="{{ route('tournee.index') }}" class="col-lg-3 mt-1" id="search" method="GET">
                        <div class="form-group">
                            <div class="input-group input-group-merge">
                                <div class="input-group-prepend"><span class="input-group-text"><i data-feather='search'></i></span></div>
                                <input type="search" class="form-control" name="recherche" value="{{ session()->get('search') }}" placeholder="Livreur, statut, date (AAAA-MM-JJ)">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row" id="table-without-card">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr class="text-center">
                                    <th>N°</th>
                                    <th>Départ</th>
                                    <th>Livreur</th>
                                    <th>Véhicule</th>
                                    <th>Arrêts</th>
                                    <th>Distance</th>
                                    <th>Durée</th>
                                    <th>Coût</th>
                                    <th>Gain vs. ordre classique</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tournees as $tournee)
                                <tr class="text-center">
                                    <td><a href="{{ route('tournee.show', $tournee->id) }}" class="font-weight-bolder">#{{ $tournee->id }}</a></td>
                                    <td>
                                        {{ $tournee->date_depart->format('d/m/Y H:i') }}<br>
                                        <small class="text-muted">{{ $tournee->quartier_depart->libelle ?? '' }}</small>
                                    </td>
                                    <td>{{ $tournee->coursier->noms ?? '' }} {{ $tournee->coursier->prenoms ?? '' }}</td>
                                    <td>{{ $tournee->vehicule ? $tournee->vehicule->immatriculation.' ('.($tournee->vehicule->type->libelle ?? '').')' : '—' }}</td>
                                    <td>{{ $tournee->etapes_count }}</td>
                                    <td>{{ number_format($tournee->distance_totale_km, 1, ',', ' ') }} km</td>
                                    <td>{{ \App\Services\Routage\Horaire::formatDuree($tournee->duree_totale_minutes) }}</td>
                                    <td>{{ $tournee->cout_total > 0 ? number_format($tournee->cout_total, 0, ',', ' ').' F' : '—' }}</td>
                                    <td>
                                        @php $gain = $tournee->gainPourcent('duree'); @endphp
                                        <span class="badge badge-pill badge-light-{{ $gain > 0 ? 'success' : 'secondary' }}">{{ $gain > 0 ? '-' : '' }}{{ abs($gain) }} % de temps</span>
                                        @if($tournee->cout_reference > 0)
                                            <span class="badge badge-pill badge-light-{{ $tournee->gainPourcent('cout') > 0 ? 'success' : 'secondary' }}">{{ $tournee->gainPourcent('cout') > 0 ? '-' : '' }}{{ abs($tournee->gainPourcent('cout')) }} % de coût</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php $couleur = ['planifiee' => 'info', 'encours' => 'warning', 'terminee' => 'success', 'annulee' => 'danger'][$tournee->statut] ?? 'secondary'; @endphp
                                        <span class="badge badge-pill badge-light-{{ $couleur }}">{{ \App\Models\tournee\Tournee::STATUTS[$tournee->statut] }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown" style="margin-bottom: 0px !important;"><i data-feather="more-vertical"></i></button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{route('tournee.show', $tournee->id)}}"><i data-feather="eye" class="mr-50 text-dark"></i><span class="ml-1">Feuille de route</span></a>
                                                @if(in_array($tournee->statut, ['planifiee','encours']))
                                                <span class="dropdown-item" data-toggle="modal" onclick="remplir('{{route('tournee.destroy', $tournee->id)}}','Tournée #{{ $tournee->id }}',' annuler la tournée (les commandes repassent en attente) ','button_footer')" data-target="#danger">
                                                    <i data-feather='x' class="text-danger"></i><span class="ml-1" type="button">Annuler</span>
                                                </span>
                                                @endif
                                                <span class="dropdown-item" data-toggle="modal" onclick="remplir('{{route('tournee.destroy', $tournee->id)}}','Tournée #{{ $tournee->id }}',' supprimer définitivement la tournée ','button_delete')" data-target="#danger">
                                                    <i data-feather='trash-2' class="text-danger"></i><span class="ml-1" type="button">Supprimer</span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="11" class="text-center text-muted p-2">Aucune tournée planifiée. <a href="{{ route('tournee.create') }}">Planifier la première tournée</a>.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-1">
                    {{ $tournees->appends(request()->query())->links('pagination::bootstrap-4') }}
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
