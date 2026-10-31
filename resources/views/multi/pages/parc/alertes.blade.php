@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')

@section('title')
{{'Alertes du parc'}}
@endsection
@section('contenu')
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        @include('multi.pages.parc._entete', ['titre' => 'Alertes du parc', 'sous_menu' => 'alertes'])
        <div class="content-body">
            <div class="card">
                <div class="card-header pb-50 flex-wrap">
                    <h4 class="card-title">{{$alertes->count()}} alerte(s)</h4>
                    <div class="btn-group btn-group-sm flex-wrap">
                        <a href="{{route('parc.alertes')}}" class="btn {{!$type ? 'btn-primary' : 'btn-outline-primary'}}">Toutes</a>
                        <a href="{{route('parc.alertes',['type'=>'document'])}}" class="btn {{$type=='document' ? 'btn-primary' : 'btn-outline-primary'}}">Documents</a>
                        <a href="{{route('parc.alertes',['type'=>'maintenance'])}}" class="btn {{$type=='maintenance' ? 'btn-primary' : 'btn-outline-primary'}}">Entretiens</a>
                        <a href="{{route('parc.alertes',['type'=>'incident'])}}" class="btn {{$type=='incident' ? 'btn-primary' : 'btn-outline-primary'}}">Incidents</a>
                        <a href="{{route('parc.alertes',['type'=>'etat'])}}" class="btn {{$type=='etat' ? 'btn-primary' : 'btn-outline-primary'}}">Véhicules</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Niveau</th><th>Véhicule</th><th>Coursier</th><th>Objet</th><th>Détail</th><th>Date</th><th></th></tr></thead>
                        <tbody>
                        @forelse($alertes as $alerte)
                            <tr>
                                <td><span class="badge badge-glow badge-{{$alerte['niveau']}}"><i data-feather="{{$alerte['icone']}}" style="width:14px"></i> {{['danger'=>'Urgent','warning'=>'À prévoir','info'=>'Info'][$alerte['niveau']]}}</span></td>
                                <td><a href="{{route('vehicule.show',$alerte['vehicule']->id)}}" class="font-weight-bolder">{{$alerte['vehicule']->immatriculation}}</a><br><small class="text-muted">{{$alerte['vehicule']->marque}} {{$alerte['vehicule']->modele}}</small></td>
                                <td>{{$alerte['vehicule']->coursier ? $alerte['vehicule']->coursier->noms.' '.$alerte['vehicule']->coursier->prenoms : '-'}}</td>
                                <td>{{$alerte['titre']}}</td>
                                <td>{{$alerte['message']}}</td>
                                <td class="text-nowrap">{{$alerte['date'] ? Ladate($alerte['date']) : '-'}}</td>
                                <td><a href="{{route('vehicule.show',[$alerte['vehicule']->id,'onglet'=>$alerte['onglet']])}}" class="btn btn-sm btn-flat-primary"><i data-feather="arrow-right-circle"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-success py-3"><i data-feather="check-circle"></i> Aucune alerte pour ce filtre.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script type="text/javascript">
    document.querySelector('#ParcAlertes').classList.add('active');
</script>
@endsection
