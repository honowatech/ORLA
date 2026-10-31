@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')

@section('title')
{{'Planning des entretiens'}}
@endsection
@section('contenu')
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        @include('multi.pages.parc._entete', ['titre' => 'Entretiens et réparations', 'sous_menu' => 'entretiens'])
        <div class="content-body">
            <div class="row">
                <div class="col-md-4">
                    @include('partials.stat_card', ['couleur' => $compteurs['retard'] > 0 ? 'danger' : 'success', 'icone' => 'alert-circle', 'valeur' => $compteurs['retard'], 'libelle' => 'En retard'])
                </div>
                <div class="col-md-4">
                    @include('partials.stat_card', ['couleur' => 'info', 'icone' => 'calendar', 'valeur' => $compteurs['a_venir'], 'libelle' => 'Planifiés / en cours'])
                </div>
                <div class="col-md-4">
                    @include('partials.stat_card', ['couleur' => 'warning', 'icone' => 'dollar-sign', 'valeur' => formatMontant($compteurs['cout_mois']), 'libelle' => 'Dépensés ce mois', 'note' => 'FCFA, entretiens réalisés'])
                </div>
            </div>
            <div class="card panel-card panel-secondary sx-table-card">
                <div class="card-header flex-wrap">
                    <form method="GET" action="{{route('vehicule_maintenance.index')}}" class="form-inline" style="gap:.5rem">
                        <select name="statut" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">Tous les statuts</option>
                            <option value="retard" {{$statut=='retard'?'selected':''}}>En retard</option>
                            <option value="a_venir" {{$statut=='a_venir'?'selected':''}}>Planifiés / en cours</option>
                            @foreach(\App\Models\Parc\Vehicule_maintenance::STATUTS as $cle => $libelle)
                                <option value="{{$cle}}" {{$statut==$cle?'selected':''}}>{{$libelle}}</option>
                            @endforeach
                        </select>
                        <select name="type" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">Tous les types</option>
                            @foreach(\App\Models\Parc\Vehicule_maintenance::TYPES as $cle => $libelle)
                                <option value="{{$cle}}" {{request('type')==$cle?'selected':''}}>{{$libelle}}</option>
                            @endforeach
                        </select>
                        <div class="input-group input-group-merge input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text"><i data-feather='search'></i></span></div>
                            <input type="search" class="form-control" name="recherche" value="{{request('recherche')}}" placeholder="Immatriculation, libellé, garage…">
                        </div>
                    </form>
                    <button type="button" class="btn btn-sm btn-primary" data-modal="#modal_maintenance" data-action="{{route('vehicule_maintenance.store')}}" data-method="POST" data-titre="Nouvel entretien" onclick="parcOuvrir(this)"><i data-feather="plus"></i> Planifier un entretien</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Véhicule</th><th>Entretien</th><th>Prévu</th><th>Réalisé</th><th>Km</th><th>Coût</th><th>Prestataire</th><th>Statut</th><th class="text-center">Actions</th></tr></thead>
                        <tbody>
                        @forelse($maintenances as $m)
                            <tr class="{{$m->en_retard ? 'sx-row-danger' : ''}}">
                                <td><a href="{{route('vehicule.show',[$m->id_vehicule,'onglet'=>'entretiens'])}}" class="font-weight-bolder">{{$m->vehicule ? $m->vehicule->immatriculation : '-'}}</a><br><small class="text-muted">{{$m->vehicule ? $m->vehicule->marque.' '.$m->vehicule->modele : ''}} @if($m->vehicule) · {{formatMontant($m->vehicule->kilometrage)}} km @endif</small></td>
                                <td><span class="font-weight-bolder">{{$m->libelle}}</span><br><small class="text-muted">{{$m->type_libelle}}</small></td>
                                <td class="text-nowrap">{{$m->date_prevue ? Ladate($m->date_prevue) : '-'}}@if($m->prochain_kilometrage && !$m->date_realisation)<br><small class="text-muted">ou {{formatMontant($m->prochain_kilometrage)}} km</small>@endif</td>
                                <td class="text-nowrap">{{$m->date_realisation ? Ladate($m->date_realisation) : '-'}}</td>
                                <td>{{$m->kilometrage ? formatMontant($m->kilometrage) : '-'}}</td>
                                <td>{{$m->cout ? formatMontant($m->cout).' FCFA' : '-'}}</td>
                                <td>{{$m->prestataire ?: '-'}}</td>
                                <td><span class="badge sx-badge-soft {{str_replace('badge-','badge-light-',$m->statut_badge)}}">{{$m->statut_libelle}}</span>@if($m->en_retard)<br><small class="text-danger">en retard</small>@endif</td>
                                <td class="text-center text-nowrap">
                                    @if(in_array($m->statut,['planifie','en_cours']))
                                    <button type="button" class="btn btn-sm btn-icon btn-flat-success" title="Marquer réalisé" data-modal="#modal_maintenance" data-action="{{route('vehicule_maintenance.update',$m->id)}}" data-method="PUT" data-titre="Clôturer l'entretien" data-json='{{ json_encode(array_merge($m->only(['type','libelle','description','date_prevue','cout','prestataire','prochaine_date','prochain_kilometrage']), ['id_vehicule' => $m->id_vehicule, 'statut' => 'realise', 'date_realisation' => date('Y-m-d'), 'kilometrage' => $m->kilometrage ?: ($m->vehicule ? $m->vehicule->kilometrage : null), 'planifier_suivant' => 1])) }}' onclick="parcOuvrir(this)"><i data-feather="check-circle"></i></button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-icon btn-flat-info" title="Modifier" data-modal="#modal_maintenance" data-action="{{route('vehicule_maintenance.update',$m->id)}}" data-method="PUT" data-titre="Modifier l'entretien" data-json='{{ json_encode(array_merge($m->only(['type','libelle','description','statut','date_prevue','date_realisation','kilometrage','cout','prestataire','prochaine_date','prochain_kilometrage']), ['id_vehicule' => $m->id_vehicule])) }}' onclick="parcOuvrir(this)"><i data-feather="edit-2"></i></button>
                                    <button type="button" class="btn btn-sm btn-icon btn-flat-danger" title="Supprimer" onclick="remplir('{{route('vehicule_maintenance.destroy',$m->id)}}','{{$m->libelle}}',' supprimer cet entretien ','button_delete_parc')"><i data-feather="trash-2"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-2">Aucun entretien pour ce filtre.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @include('multi.pages.parc.partials.pagination', ['paginator' => $maintenances])
            </div>
        </div>
    </div>
</div>
@php $vehicule = null; @endphp
@include('multi.pages.parc.partials.modal_maintenance')
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
    document.querySelector('#ParcEntretiens').classList.add('active');
</script>
@endsection
