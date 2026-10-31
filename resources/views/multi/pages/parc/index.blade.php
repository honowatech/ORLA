@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')

@section('title')
{{'Parc automobile'}}
@endsection
@section('contenu')
@php
    $variation = $couts['precedent']['total'] > 0 ? round((($couts['mois']['total'] - $couts['precedent']['total']) / $couts['precedent']['total']) * 100) : null;
    $max_etat = max(1, max($par_etat));
@endphp
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        @include('multi.pages.parc._entete', ['titre' => 'Tableau de bord du parc', 'sous_menu' => 'tableau'])
        <div class="content-body">
            <section id="dashboard-parc">
                {{-- Statistiques principales --}}
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        @include('partials.stat_card', ['couleur' => 'primary', 'icone' => 'truck', 'valeur' => $stats['actifs'], 'unite' => '/ '.$stats['total'], 'libelle' => 'Véhicules actifs', 'note' => $stats['attribues'].' attribué(s) · '.$stats['disponibles'].' disponible(s)', 'lien' => route('vehicule.index')])
                    </div>
                    <div class="col-xl-3 col-md-6">
                        @include('partials.stat_card', ['couleur' => $stats['documents_expires'] > 0 ? 'danger' : 'success', 'icone' => 'file-text', 'valeur' => $stats['documents_expires'], 'libelle' => 'Document(s) expiré(s)', 'note' => $stats['documents_bientot'].' à renouveler sous 30 jours', 'lien' => route('vehicule_document.index', ['etat' => 'expire'])])
                    </div>
                    <div class="col-xl-3 col-md-6">
                        @include('partials.stat_card', ['couleur' => $stats['maintenances_retard'] > 0 ? 'warning' : 'info', 'icone' => 'tool', 'valeur' => $stats['maintenances_retard'], 'libelle' => 'Entretien(s) en retard', 'note' => $stats['maintenances_planifiees'].' planifié(s) au total', 'lien' => route('vehicule_maintenance.index', ['statut' => 'retard'])])
                    </div>
                    <div class="col-xl-3 col-md-6">
                        @include('partials.stat_card', ['couleur' => $stats['incidents_ouverts'] > 0 ? 'danger' : 'secondary', 'icone' => 'alert-triangle', 'valeur' => $stats['incidents_ouverts'], 'libelle' => 'Incident(s) ouvert(s)', 'note' => formatMontant($stats['kilometrage_total']).' km cumulés sur le parc', 'lien' => route('vehicule_incident.index', ['statut' => 'ouverts'])])
                    </div>
                </div>

                <div class="row match-height">
                    {{-- Coûts du mois --}}
                    <div class="col-lg-4 col-md-6">
                        <div class="card panel-card panel-warning">
                            <div class="card-header">
                                <h4 class="card-title"><span class="panel-icon"><i data-feather="dollar-sign"></i></span> Coûts du mois</h4>
                                <a href="{{route('parc.couts')}}" class="panel-link">Détail</a>
                            </div>
                            <div class="card-body">
                                <h2 class="font-weight-bolder mb-0">{{formatMontant($couts['mois']['total'])}} <small class="font-small-3">FCFA</small></h2>
                                <p class="text-muted mb-1">
                                    @if($variation !== null)
                                        <span class="{{$variation > 0 ? 'text-danger' : 'text-success'}}"><i data-feather="{{$variation > 0 ? 'trending-up' : 'trending-down'}}"></i> {{$variation > 0 ? '+' : ''}}{{$variation}} %</span> vs mois précédent ({{formatMontant($couts['precedent']['total'])}})
                                    @else
                                        Mois précédent : {{formatMontant($couts['precedent']['total'])}} FCFA
                                    @endif
                                </p>
                                @php $postes = [['Carburant','carburant','warning','droplet'],['Entretien','maintenance','info','tool'],['Incidents','incidents','danger','alert-triangle'],['Documents','documents','primary','file-text']]; $total_mois = max(1,$couts['mois']['total']); @endphp
                                @foreach($postes as [$libelle,$cle,$couleur,$icone])
                                <div class="sx-bar-row">
                                    <div class="sx-bar-meta">
                                        <span><span class="sx-dot bg-{{$couleur}}"></span>{{$libelle}}</span>
                                        <span class="font-weight-bolder">{{formatMontant($couts['mois'][$cle])}}</span>
                                    </div>
                                    <div class="sx-bar"><span class="bg-{{$couleur}}" style="width: {{round($couts['mois'][$cle] / $total_mois * 100)}}%"></span></div>
                                </div>
                                @endforeach
                                <small class="text-muted">{{formatMontant($stats['litres_mois'])}} litres de carburant ce mois.</small>
                            </div>
                        </div>
                    </div>

                    {{-- Répartition par état --}}
                    <div class="col-lg-4 col-md-6">
                        <div class="card panel-card panel-primary">
                            <div class="card-header"><h4 class="card-title"><span class="panel-icon"><i data-feather="pie-chart"></i></span> État du parc</h4><a href="{{route('vehicule.index')}}" class="panel-link">Véhicules</a></div>
                            <div class="card-body">
                                @foreach(\App\Models\Vehicule::ETATS as $cle => $libelle)
                                @php $couleur = str_replace('badge-', '', \App\Models\Vehicule::ETATS_BADGE[$cle]); @endphp
                                <div class="sx-bar-row">
                                    <div class="sx-bar-meta">
                                        <a href="{{route('vehicule.index',['etat'=>$cle])}}" class="text-body"><span class="sx-dot bg-{{$couleur}}"></span>{{$libelle}}</a>
                                        <span class="font-weight-bolder">{{$par_etat[$cle]}}</span>
                                    </div>
                                    <div class="sx-bar"><span class="bg-{{$couleur}}" style="width: {{round($par_etat[$cle] / $max_etat * 100)}}%"></span></div>
                                </div>
                                @endforeach
                                <hr>
                                <h6 class="text-muted">Par type de véhicule</h6>
                                @forelse($par_type as $type => $nombre)
                                    <div class="d-flex justify-content-between font-small-3"><span>{{$type}}</span><span class="font-weight-bolder">{{$nombre}}</span></div>
                                @empty
                                    <small class="text-muted">Aucun véhicule actif.</small>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Top coûts --}}
                    <div class="col-lg-4 col-md-12">
                        <div class="card panel-card panel-danger">
                            <div class="card-header"><h4 class="card-title"><span class="panel-icon"><i data-feather="trending-up"></i></span> Véhicules les plus coûteux</h4><span class="text-muted font-small-3">ce mois</span></div>
                            <div class="card-body">
                                @php $max_cout = max(1, $top_couts->max('cout') ?? 0); @endphp
                                @forelse($top_couts as $ligne)
                                <div class="sx-bar-row">
                                    <div class="sx-bar-meta">
                                        <span><a href="{{route('vehicule.show',$ligne['vehicule']->id)}}" class="font-weight-bolder">{{$ligne['vehicule']->immatriculation}}</a> <small class="text-muted">{{$ligne['vehicule']->marque}} {{$ligne['vehicule']->modele}}</small></span>
                                        <span class="font-weight-bolder">{{formatMontant($ligne['cout'])}} <small class="text-muted">FCFA</small></span>
                                    </div>
                                    <div class="sx-bar"><span class="bg-danger" style="width: {{round($ligne['cout'] / $max_cout * 100)}}%"></span></div>
                                </div>
                                @empty
                                <div class="sx-empty"><i data-feather="dollar-sign"></i> Aucune dépense enregistrée ce mois.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row match-height">
                    {{-- Alertes --}}
                    <div class="col-lg-6">
                        <div class="card panel-card panel-warning">
                            <div class="card-header">
                                <h4 class="card-title"><span class="panel-icon"><i data-feather="bell"></i></span> Alertes <span class="badge badge-light-warning sx-badge-soft">{{$alertes->count()}}</span></h4>
                                <a href="{{route('parc.alertes')}}" class="panel-link">Tout voir</a>
                            </div>
                            <div class="card-body">
                                <ul class="sx-list">
                                    @forelse($alertes->take(8) as $alerte)
                                    <li class="is-{{$alerte['niveau']}}">
                                        <span class="sx-list-icon"><i data-feather="{{$alerte['icone']}}"></i></span>
                                        <div class="sx-list-body">
                                            <span class="sx-list-title"><a href="{{route('vehicule.show',[$alerte['vehicule']->id,'onglet'=>$alerte['onglet']])}}">{{$alerte['vehicule']->immatriculation}}</a> · {{$alerte['titre']}}</span>
                                            <small class="sx-list-sub">{{$alerte['message']}}</small>
                                        </div>
                                        <a href="{{route('vehicule.show',[$alerte['vehicule']->id,'onglet'=>$alerte['onglet']])}}" class="sx-list-right text-muted"><i data-feather="chevron-right"></i></a>
                                    </li>
                                    @empty
                                    <li class="is-success"><span class="sx-list-icon"><i data-feather="check-circle"></i></span><div class="sx-list-body"><span class="sx-list-title">Aucune alerte</span><small class="sx-list-sub">Le parc est en règle.</small></div></li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Prochains entretiens --}}
                    <div class="col-lg-6">
                        <div class="card panel-card panel-info">
                            <div class="card-header">
                                <h4 class="card-title"><span class="panel-icon"><i data-feather="calendar"></i></span> Prochains entretiens</h4>
                                <a href="{{route('vehicule_maintenance.index',['statut'=>'a_venir'])}}" class="panel-link">Planning</a>
                            </div>
                            <div class="card-body">
                                <ul class="sx-list">
                                    @forelse($prochains_entretiens as $m)
                                    <li class="{{$m->en_retard ? 'is-danger' : ($m->statut == 'en_cours' ? 'is-warning' : 'is-info')}}">
                                        <span class="sx-list-icon"><i data-feather="tool"></i></span>
                                        <div class="sx-list-body">
                                            <span class="sx-list-title"><a href="{{route('vehicule.show',[$m->id_vehicule,'onglet'=>'entretiens'])}}">{{$m->vehicule ? $m->vehicule->immatriculation : '-'}}</a> · {{$m->libelle}}</span>
                                            <small class="sx-list-sub">{{$m->type_libelle}} @if($m->prochain_kilometrage) · à {{formatMontant($m->prochain_kilometrage)}} km @endif @if($m->statut == 'en_cours') · en cours @endif</small>
                                        </div>
                                        <span class="sx-list-right {{$m->en_retard ? 'text-danger font-weight-bolder' : 'text-muted'}}">{{$m->date_prevue ? Ladate($m->date_prevue) : 'sans date'}}</span>
                                    </li>
                                    @empty
                                    <li class="is-success"><span class="sx-list-icon"><i data-feather="check-circle"></i></span><div class="sx-list-body"><span class="sx-list-title">Aucun entretien planifié</span></div></li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card panel-card panel-danger sx-table-card">
                            <div class="card-header">
                                <h4 class="card-title"><span class="panel-icon"><i data-feather="alert-triangle"></i></span> Derniers incidents</h4>
                                <div>
                                    <a href="{{route('parc.export')}}" class="btn btn-sm btn-outline-secondary"><i data-feather="download"></i> Export CSV du parc</a>
                                    <a href="{{route('vehicule_incident.index')}}" class="btn btn-sm btn-outline-danger">Registre</a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead><tr><th>Date</th><th>Véhicule</th><th>Type</th><th>Description</th><th>Coursier</th><th>Coût</th><th>Statut</th></tr></thead>
                                    <tbody>
                                    @forelse($derniers_incidents as $inc)
                                        <tr>
                                            <td class="text-nowrap">{{Ladate($inc->date)}}</td>
                                            <td><a href="{{route('vehicule.show',[$inc->id_vehicule,'onglet'=>'incidents'])}}" class="font-weight-bolder">{{$inc->vehicule ? $inc->vehicule->immatriculation : '-'}}</a></td>
                                            <td>{{$inc->type_libelle}}</td>
                                            <td>{{cutText($inc->description, 80)}}</td>
                                            <td>{{$inc->coursier ? $inc->coursier->noms.' '.$inc->coursier->prenoms : '-'}}</td>
                                            <td>{{$inc->cout ? formatMontant($inc->cout).' FCFA' : '-'}}</td>
                                            <td><span class="badge sx-badge-soft {{str_replace('badge-','badge-light-',$inc->statut_badge)}}">{{$inc->statut_libelle}}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted py-2">Aucun incident déclaré.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script type="text/javascript">
    document.querySelector('#ParcTableau').classList.add('active');
</script>
@endsection
