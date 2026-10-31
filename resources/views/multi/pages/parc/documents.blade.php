@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')

@section('title')
{{'Échéancier des documents'}}
@endsection
@section('contenu')
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        @include('multi.pages.parc._entete', ['titre' => 'Documents réglementaires', 'sous_menu' => 'documents'])
        <div class="content-body">
            <div class="row">
                <div class="col-md-4">
                    @include('partials.stat_card', ['couleur' => 'danger', 'icone' => 'x-octagon', 'valeur' => $compteurs['expire'], 'libelle' => 'Expirés'])
                </div>
                <div class="col-md-4">
                    @include('partials.stat_card', ['couleur' => 'warning', 'icone' => 'clock', 'valeur' => $compteurs['bientot'], 'libelle' => 'À renouveler sous 30 jours'])
                </div>
                <div class="col-md-4">
                    @include('partials.stat_card', ['couleur' => 'primary', 'icone' => 'file-text', 'valeur' => $compteurs['total'], 'libelle' => 'Documents enregistrés'])
                </div>
            </div>
            <div class="card panel-card panel-secondary sx-table-card">
                <div class="card-header flex-wrap">
                    <form method="GET" action="{{route('vehicule_document.index')}}" class="form-inline" style="gap:.5rem">
                        <select name="etat" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">Tous les états</option>
                            <option value="expire" {{$etat=='expire'?'selected':''}}>Expirés</option>
                            <option value="bientot" {{$etat=='bientot'?'selected':''}}>À renouveler (30 j)</option>
                            <option value="valide" {{$etat=='valide'?'selected':''}}>Valides</option>
                        </select>
                        <select name="type" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">Tous les types</option>
                            @foreach(\App\Models\Parc\Vehicule_document::TYPES as $cle => $libelle)
                                <option value="{{$cle}}" {{request('type')==$cle?'selected':''}}>{{$libelle}}</option>
                            @endforeach
                        </select>
                        <div class="input-group input-group-merge input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text"><i data-feather='search'></i></span></div>
                            <input type="search" class="form-control" name="recherche" value="{{request('recherche')}}" placeholder="Immatriculation, numéro…">
                        </div>
                    </form>
                    <button type="button" class="btn btn-sm btn-primary" data-modal="#modal_document" data-action="{{route('vehicule_document.store')}}" data-method="POST" data-titre="Nouveau document" onclick="parcOuvrir(this)"><i data-feather="plus"></i> Ajouter un document</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Véhicule</th><th>Type</th><th>Libellé / N°</th><th>Fournisseur</th><th>Expiration</th><th>Montant</th><th>État</th><th class="text-center">Actions</th></tr></thead>
                        <tbody>
                        @forelse($documents as $doc)
                            <tr>
                                <td><a href="{{route('vehicule.show',[$doc->id_vehicule,'onglet'=>'documents'])}}" class="font-weight-bolder">{{$doc->vehicule ? $doc->vehicule->immatriculation : '-'}}</a>@if($doc->vehicule && $doc->vehicule->coursier)<br><small class="text-muted">{{$doc->vehicule->coursier->noms}} {{$doc->vehicule->coursier->prenoms}}</small>@endif</td>
                                <td>{{$doc->type_libelle}}</td>
                                <td>{{$doc->libelle}}@if($doc->numero)<br><small class="text-muted">{{$doc->numero}}</small>@endif</td>
                                <td>{{$doc->fournisseur ?: '-'}}</td>
                                <td class="text-nowrap">{{$doc->date_expiration ? Ladate($doc->date_expiration) : 'sans échéance'}}</td>
                                <td>{{$doc->montant ? formatMontant($doc->montant).' FCFA' : '-'}}</td>
                                <td><span class="badge sx-badge-soft {{str_replace('badge-','badge-light-',$doc->etat_badge)}}">{{$doc->etat_libelle}}</span>@if($doc->jours_restants !== null)<br><small class="text-muted">{{$doc->jours_restants < 0 ? 'depuis '.abs($doc->jours_restants).' j' : 'dans '.$doc->jours_restants.' j'}}</small>@endif</td>
                                <td class="text-center text-nowrap">
                                    @if($doc->fichier)<a href="{{route('vehicule_document.telecharger',$doc->id)}}" class="btn btn-sm btn-icon btn-flat-dark" title="Télécharger"><i data-feather="download"></i></a>@endif
                                    <a href="{{route('vehicule.show',[$doc->id_vehicule,'onglet'=>'documents'])}}" class="btn btn-sm btn-icon btn-flat-primary" title="Fiche véhicule"><i data-feather="arrow-right-circle"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-2">Aucun document pour ce filtre.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @include('multi.pages.parc.partials.pagination', ['paginator' => $documents])
            </div>
        </div>
    </div>
</div>
@php $vehicule = null; @endphp
@include('multi.pages.parc.partials.modal_document')
@endsection
@section('javascript')
@include('multi.pages.parc.partials.scripts')
<script type="text/javascript">
    document.querySelector('#ParcDocuments').classList.add('active');
</script>
@endsection
