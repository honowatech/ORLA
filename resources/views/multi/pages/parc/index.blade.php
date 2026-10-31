@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')

@section('title')
{{'Parc automobile'}}
@endsection
@section('css')
<style>
    .parc-barre { height: 8px; border-radius: 4px; background: #f3f2f7; overflow: hidden; }
    .parc-barre > div { height: 100%; }
    .parc-liste li { padding: .5rem 0; border-bottom: 1px solid #ebe9f1; }
    .parc-liste li:last-child { border-bottom: 0; }
</style>
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
                <div class="row match-height">
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body d-flex align-items-center">
                                <div class="avatar bg-light-primary mr-2 p-50"><div class="avatar-content"><i data-feather="truck" class="font-medium-5"></i></div></div>
                                <div>
                                    <h3 class="font-weight-bolder mb-0">{{$stats['actifs']}} <small class="text-muted font-small-3">/ {{$stats['total']}}</small></h3>
                                    <p class="card-text mb-0">Véhicules actifs</p>
                                    <small class="text-muted">{{$stats['attribues']}} attribué(s) · {{$stats['disponibles']}} disponible(s)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body d-flex align-items-center">
                                <div class="avatar {{$stats['documents_expires'] > 0 ? 'bg-light-danger' : 'bg-light-success'}} mr-2 p-50"><div class="avatar-content"><i data-feather="file-text" class="font-medium-5"></i></div></div>
                                <div>
                                    <h3 class="font-weight-bolder mb-0">{{$stats['documents_expires']}}</h3>
                                    <p class="card-text mb-0">Document(s) expiré(s)</p>
                                    <small class="text-muted">{{$stats['documents_bientot']}} à renouveler sous 30 jours</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body d-flex align-items-center">
                                <div class="avatar {{$stats['maintenances_retard'] > 0 ? 'bg-light-warning' : 'bg-light-success'}} mr-2 p-50"><div class="avatar-content"><i data-feather="tool" class="font-medium-5"></i></div></div>
                                <div>
                                    <h3 class="font-weight-bolder mb-0">{{$stats['maintenances_retard']}}</h3>
                                    <p class="card-text mb-0">Entretien(s) en retard</p>
                                    <small class="text-muted">{{$stats['maintenances_planifiees']}} planifié(s) au total</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body d-flex align-items-center">
                                <div class="avatar {{$stats['incidents_ouverts'] > 0 ? 'bg-light-danger' : 'bg-light-success'}} mr-2 p-50"><div class="avatar-content"><i data-feather="alert-triangle" class="font-medium-5"></i></div></div>
                                <div>
                                    <h3 class="font-weight-bolder mb-0">{{$stats['incidents_ouverts']}}</h3>
                                    <p class="card-text mb-0">Incident(s) ouvert(s)</p>
                                    <small class="text-muted">{{formatMontant($stats['kilometrage_total'])}} km cumulés sur le parc</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row match-height">
                    {{-- Coûts du mois --}}
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-header pb-50">
                                <h4 class="card-title">Coûts du mois</h4>
                                <a href="{{route('parc.couts')}}" class="font-small-3">Détail</a>
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
                                <div class="mb-75">
                                    <div class="d-flex justify-content-between font-small-3">
                                        <span><i data-feather="{{$icone}}" class="text-{{$couleur}}" style="width:14px"></i> {{$libelle}}</span>
                                        <span class="font-weight-bolder">{{formatMontant($couts['mois'][$cle])}}</span>
                                    </div>
                                    <div class="parc-barre"><div class="bg-{{$couleur}}" style="width: {{round($couts['mois'][$cle] / $total_mois * 100)}}%"></div></div>
                                </div>
                                @endforeach
                                <small class="text-muted">{{formatMontant($stats['litres_mois'])}} litres de carburant ce mois.</small>
                            </div>
                        </div>
                    </div>

                    {{-- Répartition par état --}}
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-header pb-50"><h4 class="card-title">État du parc</h4></div>
                            <div class="card-body">
                                @foreach(\App\Models\Vehicule::ETATS as $cle => $libelle)
                                @php $couleur = str_replace('badge-', '', \App\Models\Vehicule::ETATS_BADGE[$cle]); @endphp
                                <div class="mb-75">
                                    <div class="d-flex justify-content-between font-small-3">
                                        <a href="{{route('vehicule.index',['etat'=>$cle])}}" class="text-body">{{$libelle}}</a>
                                        <span class="font-weight-bolder">{{$par_etat[$cle]}}</span>
                                    </div>
                                    <div class="parc-barre"><div class="bg-{{$couleur}}" style="width: {{round($par_etat[$cle] / $max_etat * 100)}}%"></div></div>
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
                        <div class="card">
                            <div class="card-header pb-50"><h4 class="card-title">Véhicules les plus coûteux (mois)</h4></div>
                            <div class="card-body">
                                <ul class="list-unstyled parc-liste mb-0">
                                    @forelse($top_couts as $ligne)
                                    <li class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <a href="{{route('vehicule.show',$ligne['vehicule']->id)}}" class="font-weight-bolder">{{$ligne['vehicule']->immatriculation}}</a>
                                            <small class="d-block text-muted">{{$ligne['vehicule']->marque}} {{$ligne['vehicule']->modele}}</small>
                                        </div>
                                        <span class="badge badge-light-primary">{{formatMontant($ligne['cout'])}} FCFA</span>
                                    </li>
                                    @empty
                                    <li class="text-muted">Aucune dépense enregistrée ce mois.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row match-height">
                    {{-- Alertes --}}
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header pb-50">
                                <h4 class="card-title"><i data-feather="bell" class="text-warning mr-50"></i> Alertes ({{$alertes->count()}})</h4>
                                <a href="{{route('parc.alertes')}}" class="font-small-3">Tout voir</a>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled parc-liste mb-0">
                                    @forelse($alertes->take(8) as $alerte)
                                    <li class="d-flex align-items-center">
                                        <span class="badge badge-light-{{$alerte['niveau']}} mr-1 p-50"><i data-feather="{{$alerte['icone']}}"></i></span>
                                        <div class="flex-grow-1">
                                            <a href="{{route('vehicule.show',[$alerte['vehicule']->id,'onglet'=>$alerte['onglet']])}}" class="font-weight-bolder text-body">{{$alerte['vehicule']->immatriculation}}</a>
                                            <span class="text-muted"> · {{$alerte['titre']}}</span>
                                            <small class="d-block text-muted">{{$alerte['message']}}</small>
                                        </div>
                                    </li>
                                    @empty
                                    <li class="text-success"><i data-feather="check-circle"></i> Aucune alerte : le parc est en règle.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Prochains entretiens --}}
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header pb-50">
                                <h4 class="card-title"><i data-feather="calendar" class="text-info mr-50"></i> Prochains entretiens</h4>
                                <a href="{{route('vehicule_maintenance.index',['statut'=>'a_venir'])}}" class="font-small-3">Planning</a>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled parc-liste mb-0">
                                    @forelse($prochains_entretiens as $m)
                                    <li class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <a href="{{route('vehicule.show',[$m->id_vehicule,'onglet'=>'entretiens'])}}" class="font-weight-bolder">{{$m->vehicule ? $m->vehicule->immatriculation : '-'}}</a>
                                            <span class="text-muted"> · {{$m->libelle}}</span>
                                            <small class="d-block text-muted">{{$m->type_libelle}} @if($m->prochain_kilometrage) · à {{formatMontant($m->prochain_kilometrage)}} km @endif</small>
                                        </div>
                                        <span class="badge {{$m->en_retard ? 'badge-light-danger' : 'badge-light-info'}}">{{$m->date_prevue ? Ladate($m->date_prevue) : 'sans date'}}</span>
                                    </li>
                                    @empty
                                    <li class="text-muted">Aucun entretien planifié.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header pb-50">
                                <h4 class="card-title"><i data-feather="alert-triangle" class="text-danger mr-50"></i> Derniers incidents</h4>
                                <div>
                                    <a href="{{route('parc.export')}}" class="btn btn-sm btn-outline-secondary"><i data-feather="download"></i> Export CSV du parc</a>
                                    <a href="{{route('vehicule_incident.index')}}" class="btn btn-sm btn-outline-danger">Registre</a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
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
                                            <td><span class="badge badge-glow {{$inc->statut_badge}}">{{$inc->statut_libelle}}</span></td>
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
