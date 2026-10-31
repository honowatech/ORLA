@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')

@section('title')
{{'Journal carburant'}}
@endsection
@section('contenu')
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        @include('multi.pages.parc._entete', ['titre' => 'Journal de carburant', 'sous_menu' => 'carburant'])
        <div class="content-body">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    @include('partials.stat_card', ['couleur' => 'warning', 'icone' => 'dollar-sign', 'valeur' => formatMontant($totaux['montant']), 'libelle' => 'Montant sur la période', 'note' => 'FCFA'])
                </div>
                <div class="col-md-3 col-sm-6">
                    @include('partials.stat_card', ['couleur' => 'info', 'icone' => 'droplet', 'valeur' => formatMontant($totaux['litres']), 'libelle' => 'Litres'])
                </div>
                <div class="col-md-3 col-sm-6">
                    @include('partials.stat_card', ['couleur' => 'primary', 'icone' => 'tag', 'valeur' => $totaux['prix_litre'] ?: '-', 'libelle' => 'Prix moyen / litre'])
                </div>
                <div class="col-md-3 col-sm-6">
                    @include('partials.stat_card', ['couleur' => 'secondary', 'icone' => 'list', 'valeur' => $totaux['pleins'], 'libelle' => 'Pleins'])
                </div>
            </div>
            <div class="card panel-card panel-secondary sx-table-card">
                <div class="card-header flex-wrap">
                    <form method="GET" action="{{route('vehicule_carburant.index')}}" class="form-inline" style="gap:.5rem">
                        <label>Du</label><input type="date" name="debut" class="form-control form-control-sm" value="{{$debut}}">
                        <label>au</label><input type="date" name="fin" class="form-control form-control-sm" value="{{$fin}}">
                        <select name="id_vehicule" class="form-control form-control-sm">
                            <option value="">Tous les véhicules</option>
                            @foreach($vehicules as $v)
                                <option value="{{$v->id}}" {{request('id_vehicule')==$v->id?'selected':''}}>{{$v->immatriculation}} - {{$v->marque}} {{$v->modele}}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-sm btn-primary" type="submit"><i data-feather="filter"></i> Filtrer</button>
                    </form>
                    <button type="button" class="btn btn-sm btn-primary" data-modal="#modal_carburant" data-action="{{route('vehicule_carburant.store')}}" data-method="POST" data-titre="Nouveau plein" data-json='{{ json_encode(['date' => date('Y-m-d'), 'plein_complet' => 1]) }}' onclick="parcOuvrir(this)"><i data-feather="plus"></i> Saisir un plein</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Date</th><th>Véhicule</th><th>Coursier</th><th>Litres</th><th>Montant</th><th>Prix / L</th><th>Km</th><th>Station</th><th>Plein</th><th class="text-center">Actions</th></tr></thead>
                        <tbody>
                        @forelse($pleins as $plein)
                            <tr>
                                <td class="text-nowrap">{{Ladate($plein->date)}}</td>
                                <td><a href="{{route('vehicule.show',[$plein->id_vehicule,'onglet'=>'carburant'])}}" class="font-weight-bolder">{{$plein->vehicule ? $plein->vehicule->immatriculation : '-'}}</a></td>
                                <td>{{$plein->coursier ? $plein->coursier->noms.' '.$plein->coursier->prenoms : '-'}}</td>
                                <td class="font-weight-bolder">{{$plein->litres}} L</td>
                                <td>{{formatMontant($plein->montant)}} FCFA</td>
                                <td>{{$plein->prix_litre ?: '-'}}</td>
                                <td>{{$plein->kilometrage ? formatMontant($plein->kilometrage) : '-'}}</td>
                                <td>{{$plein->station ?: '-'}}</td>
                                <td>@if($plein->plein_complet)<span class="badge badge-light-success">Complet</span>@else<span class="badge badge-light-secondary">Partiel</span>@endif</td>
                                <td class="text-center text-nowrap">
                                    <button type="button" class="btn btn-sm btn-icon btn-flat-info" title="Modifier" data-modal="#modal_carburant" data-action="{{route('vehicule_carburant.update',$plein->id)}}" data-method="PUT" data-titre="Modifier le plein" data-json='{{ json_encode(array_merge($plein->only(['id_coursier','date','litres','montant','kilometrage','plein_complet','station','description']), ['id_vehicule' => $plein->id_vehicule])) }}' onclick="parcOuvrir(this)"><i data-feather="edit-2"></i></button>
                                    <button type="button" class="btn btn-sm btn-icon btn-flat-danger" title="Supprimer" onclick="remplir('{{route('vehicule_carburant.destroy',$plein->id)}}','{{$plein->litres}} L du {{Ladate($plein->date)}}',' supprimer ce plein ','button_delete_parc')"><i data-feather="trash-2"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-muted py-2">Aucun plein sur la période.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @include('multi.pages.parc.partials.pagination', ['paginator' => $pleins])
            </div>
        </div>
    </div>
</div>
@php $vehicule = null; @endphp
@include('multi.pages.parc.partials.modal_carburant')
<div id="button_delete_parc" hidden>
    <form method="POST" class="destroy_form">
        @csrf
        @method('DELETE')
        <input type="hidden" name="retour" value="{{url()->full()}}">
        <button type="submit" class="btn btn-gradient-success btn-success">Confirmer</button>
    </form>
    <button type="button" class="btn btn-gradient-danger btn-danger" data-dismiss="modal">Annuler</button>
</div>
@endsection
@section('javascript')
@include('multi.pages.parc.partials.scripts')
<script type="text/javascript">
    document.querySelector('#ParcCarburant').classList.add('active');
</script>
@endsection
