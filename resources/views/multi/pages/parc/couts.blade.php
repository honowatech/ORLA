@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')

@section('title')
{{'Coûts du parc'}}
@endsection
@section('contenu')
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        @include('multi.pages.parc._entete', ['titre' => 'Analyse des coûts', 'sous_menu' => 'couts'])
        <div class="content-body">
            <div class="card">
                <div class="card-header flex-wrap pb-50">
                    <form method="GET" action="{{route('parc.couts')}}" class="form-inline">
                        <label class="mr-50">Du</label>
                        <input type="date" name="debut" class="form-control form-control-sm mr-1" value="{{$debut}}">
                        <label class="mr-50">au</label>
                        <input type="date" name="fin" class="form-control form-control-sm mr-1" value="{{$fin}}">
                        <button class="btn btn-sm btn-primary mr-50" type="submit"><i data-feather="filter"></i> Filtrer</button>
                        <a class="btn btn-sm btn-outline-secondary" href="{{route('parc.export',['debut'=>$debut,'fin'=>$fin])}}"><i data-feather="download"></i> Export CSV</a>
                    </form>
                    <div class="text-right">
                        <h3 class="font-weight-bolder mb-0">{{formatMontant($totaux['total'])}} <small class="font-small-3">FCFA</small></h3>
                        <small class="text-muted">Total période · {{formatMontant($totaux['litres'])}} L · {{formatMontant($totaux['distance'])}} km parcourus (relevés)</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Véhicule</th><th>Coursier</th>
                                <th class="text-right">Carburant</th><th class="text-right">Litres</th>
                                <th class="text-right">Entretien</th><th class="text-right">Incidents</th><th class="text-right">Documents</th>
                                <th class="text-right">Total</th><th class="text-right">Km période</th><th class="text-right">Coût / km</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($lignes as $l)
                            <tr>
                                <td><a href="{{route('vehicule.show',$l['vehicule']->id)}}" class="font-weight-bolder">{{$l['vehicule']->immatriculation}}</a><br><small class="text-muted">{{$l['vehicule']->marque}} {{$l['vehicule']->modele}}</small></td>
                                <td>{{$l['vehicule']->coursier ? $l['vehicule']->coursier->noms.' '.$l['vehicule']->coursier->prenoms : '-'}}</td>
                                <td class="text-right">{{formatMontant($l['carburant'])}}</td>
                                <td class="text-right">{{formatMontant($l['litres'])}}</td>
                                <td class="text-right">{{formatMontant($l['maintenance'])}}</td>
                                <td class="text-right">{{formatMontant($l['incidents'])}}</td>
                                <td class="text-right">{{formatMontant($l['documents'])}}</td>
                                <td class="text-right font-weight-bolder">{{formatMontant($l['total'])}}</td>
                                <td class="text-right">{{$l['distance'] !== null ? formatMontant($l['distance']) : '-'}}</td>
                                <td class="text-right">{{$l['cout_km'] !== null ? $l['cout_km'] : '-'}}</td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-muted py-2">Aucun véhicule.</td></tr>
                        @endforelse
                        </tbody>
                        <tfoot class="font-weight-bolder">
                            <tr>
                                <td colspan="2">Totaux</td>
                                <td class="text-right">{{formatMontant($totaux['carburant'])}}</td>
                                <td class="text-right">{{formatMontant($totaux['litres'])}}</td>
                                <td class="text-right">{{formatMontant($totaux['maintenance'])}}</td>
                                <td class="text-right">{{formatMontant($totaux['incidents'])}}</td>
                                <td class="text-right">{{formatMontant($totaux['documents'])}}</td>
                                <td class="text-right">{{formatMontant($totaux['total'])}}</td>
                                <td class="text-right">{{formatMontant($totaux['distance'])}}</td>
                                <td class="text-right">{{$totaux['distance'] > 0 ? round($totaux['total'] / $totaux['distance'], 2) : '-'}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script type="text/javascript">
    document.querySelector('#ParcCouts').classList.add('active');
</script>
@endsection
