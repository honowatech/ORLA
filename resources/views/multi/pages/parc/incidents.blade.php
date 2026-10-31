@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')

@section('title')
{{'Registre des incidents'}}
@endsection
@section('contenu')
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        @include('multi.pages.parc._entete', ['titre' => 'Incidents et sinistres', 'sous_menu' => 'incidents'])
        <div class="content-body">
            <div class="row match-height">
                <div class="col-md-4"><div class="card"><div class="card-body d-flex align-items-center"><div class="avatar bg-light-danger mr-2 p-50"><div class="avatar-content"><i data-feather="alert-triangle" class="font-medium-5"></i></div></div><div><h3 class="font-weight-bolder mb-0">{{$compteurs['ouverts']}}</h3><p class="card-text mb-0">Incidents ouverts</p></div></div></div></div>
                <div class="col-md-4"><div class="card"><div class="card-body d-flex align-items-center"><div class="avatar bg-light-warning mr-2 p-50"><div class="avatar-content"><i data-feather="slash" class="font-medium-5"></i></div></div><div><h3 class="font-weight-bolder mb-0">{{$compteurs['immobilises']}}</h3><p class="card-text mb-0">Véhicules immobilisés</p></div></div></div></div>
                <div class="col-md-4"><div class="card"><div class="card-body d-flex align-items-center"><div class="avatar bg-light-primary mr-2 p-50"><div class="avatar-content"><i data-feather="dollar-sign" class="font-medium-5"></i></div></div><div><h3 class="font-weight-bolder mb-0">{{formatMontant($compteurs['cout_total'])}}</h3><p class="card-text mb-0">FCFA de coûts cumulés</p></div></div></div></div>
            </div>
            <div class="card">
                <div class="card-header flex-wrap pb-50">
                    <form method="GET" action="{{route('vehicule_incident.index')}}" class="form-inline" style="gap:.5rem">
                        <select name="statut" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">Tous les statuts</option>
                            <option value="ouverts" {{$statut=='ouverts'?'selected':''}}>Non résolus</option>
                            @foreach(\App\Models\Parc\Vehicule_incident::STATUTS as $cle => $libelle)
                                <option value="{{$cle}}" {{$statut==$cle?'selected':''}}>{{$libelle}}</option>
                            @endforeach
                        </select>
                        <select name="type" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">Tous les types</option>
                            @foreach(\App\Models\Parc\Vehicule_incident::TYPES as $cle => $libelle)
                                <option value="{{$cle}}" {{request('type')==$cle?'selected':''}}>{{$libelle}}</option>
                            @endforeach
                        </select>
                        <div class="input-group input-group-merge input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text"><i data-feather='search'></i></span></div>
                            <input type="search" class="form-control" name="recherche" value="{{request('recherche')}}" placeholder="Immatriculation, coursier, lieu…">
                        </div>
                    </form>
                    <button type="button" class="btn btn-sm btn-primary" data-modal="#modal_incident" data-action="{{route('vehicule_incident.store')}}" data-method="POST" data-titre="Nouvel incident" data-json='{{ json_encode(['date' => date('Y-m-d\TH:i'), 'statut' => 'ouvert']) }}' onclick="parcOuvrir(this)"><i data-feather="plus"></i> Déclarer un incident</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Date</th><th>Véhicule</th><th>Type</th><th>Description</th><th>Lieu</th><th>Coursier</th><th>Coût</th><th>Statut</th><th class="text-center">Actions</th></tr></thead>
                        <tbody>
                        @forelse($incidents as $inc)
                            <tr>
                                <td class="text-nowrap">{{Ladate($inc->date)}}<br><small class="text-muted">{{Heure($inc->date)}}</small></td>
                                <td><a href="{{route('vehicule.show',[$inc->id_vehicule,'onglet'=>'incidents'])}}" class="font-weight-bolder">{{$inc->vehicule ? $inc->vehicule->immatriculation : '-'}}</a></td>
                                <td>{{$inc->type_libelle}}@if($inc->immobilise)<br><span class="badge badge-light-danger">Immobilisé</span>@endif</td>
                                <td style="max-width:260px">{{cutText($inc->description, 120)}}</td>
                                <td>{{$inc->lieu ?: '-'}}</td>
                                <td>{{$inc->coursier ? $inc->coursier->noms.' '.$inc->coursier->prenoms : '-'}}</td>
                                <td>{{$inc->cout ? formatMontant($inc->cout).' FCFA' : '-'}}</td>
                                <td><span class="badge badge-glow {{$inc->statut_badge}}">{{$inc->statut_libelle}}</span></td>
                                <td class="text-center text-nowrap">
                                    <button type="button" class="btn btn-sm btn-icon btn-flat-info" title="Modifier" data-modal="#modal_incident" data-action="{{route('vehicule_incident.update',$inc->id)}}" data-method="PUT" data-titre="Modifier l'incident" data-json='{{ json_encode(array_merge($inc->only(['id_coursier','type','lieu','description','cout','statut','date_resolution','immobilise']), ['id_vehicule' => $inc->id_vehicule, 'date' => \Carbon\Carbon::parse($inc->date)->format('Y-m-d\TH:i')])) }}' onclick="parcOuvrir(this)"><i data-feather="edit-2"></i></button>
                                    <button type="button" class="btn btn-sm btn-icon btn-flat-danger" title="Supprimer" onclick="remplir('{{route('vehicule_incident.destroy',$inc->id)}}','{{$inc->type_libelle}} du {{Ladate($inc->date)}}',' supprimer cet incident ','button_delete_parc')"><i data-feather="trash-2"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-2">Aucun incident pour ce filtre.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @include('multi.pages.parc.partials.pagination', ['paginator' => $incidents])
            </div>
        </div>
    </div>
</div>
@php $vehicule = null; @endphp
@include('multi.pages.parc.partials.modal_incident')
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
    document.querySelector('#ParcIncidents').classList.add('active');
</script>
@endsection
