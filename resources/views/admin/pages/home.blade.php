@extends('admin/templates/template')
@section('css')
@endsection
@section('title')
    {{'Speedex Home'}}
@endsection
@section('contenu')
@php
    $statuts = [
        'attente' => ['primary', 'clock', 'En attente'],
        'attribue' => ['info', 'user-check', 'Attribuée'],
        'encours' => ['dark', 'loader', 'En cours'],
        'livre' => ['success', 'check', 'Livrée'],
        'annulee' => ['danger', 'alert-triangle', 'Annulée'],
        'echoue' => ['warning', 'slash', 'Échouée'],
        'supprime' => ['secondary', 'trash-2', 'Supprimée'],
    ];
    $livrees_mois = $datas['commandes_reussies_du_mois']->count();
    $livrees_prec = $datas['commandes_reussies_mois_precedent']->count();
    $ca_mois = $datas['commandes_reussies_du_mois']->sum('montant_livraison');
    $ca_prec = $datas['commandes_reussies_mois_precedent']->sum('montant_livraison');
    $variation_ca = $ca_prec > 0 ? round((($ca_mois - $ca_prec) / $ca_prec) * 100) : null;
    $variation_livrees = $livrees_prec > 0 ? round((($livrees_mois - $livrees_prec) / $livrees_prec) * 100) : null;
    $taux_reussite = $datas['commandes_mois'] > 0 ? round(($livrees_mois / $datas['commandes_mois']) * 100) : null;
    $en_cours_total = $datas['commandes_attente'] + $datas['commandes_attribuees'] + $datas['commandes_encours'];
    $max_pipeline = max(1, $datas['commandes_attente'], $datas['commandes_attribuees'], $datas['commandes_encours']);
@endphp
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Tableau de bord</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active">{{ Ladate(now()) }} · mis à jour <span class="temps"></span></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <section id="dashboard-admin">

                    {{-- Indicateurs principaux --}}
                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            @include('partials.stat_card', ['couleur' => 'primary', 'icone' => 'zap', 'valeur' => $datas['coursiers']->count(), 'libelle' => 'Coursiers actifs', 'note' => $datas['commandes_encours'].' livraison(s) en cours', 'lien' => route('coursiers.index')])
                        </div>
                        <div class="col-xl-3 col-md-6">
                            @include('partials.stat_card', ['couleur' => 'info', 'icone' => 'users', 'valeur' => $datas['clients_entreprise']->count() + $datas['clients_simple']->count(), 'libelle' => 'Clients', 'note' => $datas['clients_entreprise']->count().' entreprise(s) · '.$datas['clients_simple']->count().' particulier(s)', 'lien' => route('clients.index')])
                        </div>
                        <div class="col-xl-3 col-md-6">
                            @include('partials.stat_card', ['couleur' => 'success', 'icone' => 'check-circle', 'valeur' => $livrees_mois, 'libelle' => 'Livrées ce mois', 'note' => ($variation_livrees !== null ? '<span class="stat-trend text-'.($variation_livrees >= 0 ? 'success' : 'danger').'">'.($variation_livrees >= 0 ? '+' : '').$variation_livrees.' %</span> vs mois précédent' : $datas['commandes_reussies']->count().' livrées au total'), 'lien' => route('commandes.index')])
                        </div>
                        <div class="col-xl-3 col-md-6">
                            @include('partials.stat_card', ['couleur' => 'warning', 'icone' => 'dollar-sign', 'valeur' => formatMontant($ca_mois), 'unite' => 'XAF', 'libelle' => "Chiffre d'affaires du mois", 'note' => ($variation_ca !== null ? '<span class="stat-trend text-'.($variation_ca >= 0 ? 'success' : 'danger').'">'.($variation_ca >= 0 ? '+' : '').$variation_ca.' %</span> vs '.formatMontant($ca_prec).' XAF' : 'Frais de livraison encaissés')])
                        </div>
                    </div>

                    <div class="row match-height">
                        {{-- Flux des commandes --}}
                        <div class="col-lg-4 col-md-6">
                            <div class="card panel-card panel-primary">
                                <div class="card-header">
                                    <h4 class="card-title"><span class="panel-icon"><i data-feather="shopping-cart"></i></span> Commandes à traiter</h4>
                                    <a href="{{route('commandes.index')}}" class="panel-link">Voir tout</a>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-end justify-content-between mb-1">
                                        <div>
                                            <h2 class="font-weight-bolder mb-0">{{ $en_cours_total }}</h2>
                                            <small class="text-muted">commande(s) non livrée(s)</small>
                                        </div>
                                        <span class="badge badge-light-{{ $datas['commandes_du_jour'] > 0 ? 'warning' : 'success' }} sx-badge-soft">{{ $datas['commandes_du_jour'] }} prévue(s) aujourd'hui</span>
                                    </div>
                                    @foreach([['attente', $datas['commandes_attente']], ['attribue', $datas['commandes_attribuees']], ['encours', $datas['commandes_encours']]] as [$cle, $nombre])
                                    <div class="sx-bar-row">
                                        <div class="sx-bar-meta">
                                            <span><span class="sx-dot bg-{{ $statuts[$cle][0] }}"></span>{{ $statuts[$cle][2] }}</span>
                                            <span class="font-weight-bolder">{{ $nombre }}</span>
                                        </div>
                                        <div class="sx-bar"><span class="bg-{{ $statuts[$cle][0] }}" style="width: {{ round($nombre / $max_pipeline * 100) }}%"></span></div>
                                    </div>
                                    @endforeach
                                    <hr class="my-1">
                                    <div class="d-flex justify-content-between font-small-3">
                                        <span class="text-muted">Taux de réussite du mois</span>
                                        <span class="font-weight-bolder {{ $taux_reussite !== null && $taux_reussite < 80 ? 'text-warning' : 'text-success' }}">{{ $taux_reussite !== null ? $taux_reussite.' %' : '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between font-small-3">
                                        <span class="text-muted">Annulées / échouées ce mois</span>
                                        <span class="font-weight-bolder {{ $datas['commandes_echouees_mois'] > 0 ? 'text-danger' : '' }}">{{ $datas['commandes_echouees_mois'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Top coursiers --}}
                        <div class="col-lg-4 col-md-6">
                            <div class="card panel-card panel-success">
                                <div class="card-header">
                                    <h4 class="card-title"><span class="panel-icon"><i data-feather="award"></i></span> Coursiers du mois</h4>
                                    <a href="{{route('coursiers.index')}}" class="panel-link">Équipe</a>
                                </div>
                                <div class="card-body">
                                    @php $max_top = max(1, $datas['top_coursiers']->max('total') ?? 0); @endphp
                                    @forelse($datas['top_coursiers'] as $ligne)
                                    <div class="sx-bar-row">
                                        <div class="sx-bar-meta">
                                            <span class="text-truncate">
                                                @if($ligne->coursier)
                                                    <a href="{{route('coursiers.show',$ligne->coursier->id)}}" class="text-body">{{ $ligne->coursier->noms }} {{ $ligne->coursier->prenoms }}</a>
                                                @else
                                                    Coursier supprimé
                                                @endif
                                            </span>
                                            <span class="font-weight-bolder">{{ $ligne->total }} <small class="text-muted">livr.</small></span>
                                        </div>
                                        <div class="sx-bar"><span class="bg-success" style="width: {{ round($ligne->total / $max_top * 100) }}%"></span></div>
                                    </div>
                                    @empty
                                    <div class="sx-empty"><i data-feather="award"></i> Aucune livraison effectuée ce mois-ci.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- Parc automobile --}}
                        @if(isset($parc))
                        <div class="col-lg-4 col-md-12">
                            <div class="card panel-card panel-info">
                                <div class="card-header">
                                    <h4 class="card-title"><span class="panel-icon"><i data-feather="truck"></i></span> Parc automobile</h4>
                                    <a href="{{route('parc.index')}}" class="panel-link">Tableau de bord</a>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center mb-1">
                                        <div class="col-4"><h3 class="font-weight-bolder mb-0">{{$parc['actifs']}}</h3><small class="text-muted">Actifs</small></div>
                                        <div class="col-4"><h3 class="font-weight-bolder mb-0 text-success">{{$parc['disponibles']}}</h3><small class="text-muted">Disponibles</small></div>
                                        <div class="col-4"><h3 class="font-weight-bolder mb-0 {{$parc['indisponibles'] > 0 ? 'text-danger' : ''}}">{{$parc['indisponibles']}}</h3><small class="text-muted">Indisponibles</small></div>
                                    </div>
                                    @if($parc['alertes']->count() > 0)
                                        <ul class="sx-list">
                                            @foreach($parc['alertes']->take(4) as $alerte)
                                            <li class="is-{{$alerte['niveau']}}">
                                                <span class="sx-list-icon"><i data-feather="{{$alerte['icone']}}"></i></span>
                                                <div class="sx-list-body">
                                                    <span class="sx-list-title"><a href="{{route('vehicule.show',[$alerte['vehicule']->id,'onglet'=>$alerte['onglet']])}}">{{$alerte['vehicule']->immatriculation}}</a> · {{$alerte['titre']}}</span>
                                                    <small class="sx-list-sub">{{$alerte['message']}}</small>
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                        @if($parc['alertes']->count() > 4)
                                            <a href="{{route('parc.alertes')}}" class="d-block mt-75 font-small-3 font-weight-bolder">Voir les {{$parc['alertes']->count()}} alertes</a>
                                        @endif
                                    @else
                                        <div class="sx-empty text-success"><i data-feather="check-circle"></i> Aucune alerte sur le parc.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Dernières commandes --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card panel-card panel-secondary sx-table-card">
                                <div class="card-header">
                                    <h4 class="card-title"><span class="panel-icon"><i data-feather="activity"></i></span> Dernières commandes</h4>
                                    <a href="{{route('commandes.create')}}" class="btn btn-sm btn-primary"><i data-feather="plus"></i> Nouvelle commande</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr><th>Client</th><th>Livraison</th><th>Coursier</th><th class="text-right">Frais</th><th>Date prévue</th><th>Statut</th><th></th></tr>
                                        </thead>
                                        <tbody>
                                        @forelse($datas['dernieres_commandes'] as $commande)
                                            @php $st = $statuts[$commande->statut] ?? ['secondary', 'help-circle', $commande->statut]; @endphp
                                            <tr>
                                                <td>
                                                    <span class="font-weight-bolder text-uppercase">{{ $commande->id_client == null ? cutText((string) $commande->nom_client, 22) : cutText($commande->client->noms.' '.$commande->client->Prenoms, 22) }}</span>
                                                    <br><small class="text-muted">{{ $commande->type_commande == 'entreprise' ? 'Entreprise' : 'Particulier' }}</small>
                                                </td>
                                                <td>{{ $commande->quartier_livraison ? $commande->quartier_livraison->libelle : cutText((string) $commande->adresse_livraison, 25) }}</td>
                                                <td>{{ $commande->coursier ? $commande->coursier->noms.' '.$commande->coursier->prenoms : '-' }}</td>
                                                <td class="text-right">{{ formatMontant($commande->montant_livraison) }} XAF</td>
                                                <td class="text-nowrap">{{ $commande->date_livraison ? Ladate($commande->date_livraison) : '-' }}</td>
                                                <td><span class="badge badge-light-{{ $st[0] }} sx-badge-soft"><i data-feather="{{ $st[1] }}" style="width:12px;height:12px"></i> {{ $st[2] }}</span></td>
                                                <td class="text-right"><a href="{{route('commandes.show',$commande->id)}}" class="btn btn-sm btn-icon btn-flat-primary"><i data-feather="arrow-right-circle"></i></a></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="7"><div class="sx-empty"><i data-feather="inbox"></i> Aucune commande enregistrée pour le moment.</div></td></tr>
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
    <!-- END: Content-->
@endsection
@section('javascript')
	<script type="text/javascript">
        document.querySelector('#Acceuil').classList.add('active');
        function tempsEcoule(date) {
            const dateSoumise = new Date((date * 1000));
            const dateActuelle = new Date();
            const intervalle = dateActuelle - dateSoumise;
            const annees = Math.floor(intervalle / (365 * 24 * 60 * 60 * 1000));
            const mois = Math.floor((intervalle % (365 * 24 * 60 * 60 * 1000)) / (30 * 24 * 60 * 60 * 1000));
            const jours = Math.floor((intervalle % (30 * 24 * 60 * 60 * 1000)) / (24 * 60 * 60 * 1000));
            const heures = Math.floor((intervalle % (24 * 60 * 60 * 1000)) / (60 * 60 * 1000));
            const minutes = Math.floor((intervalle % (60 * 60 * 1000)) / (60 * 1000));
            const secondes = Math.floor((intervalle % (60 * 1000)) / 1000);
            if (secondes != 0 && minutes == 0 && heures == 0 && jours == 0 && mois == 0 && annees == 0) {
                return `à l'instant`;
            } else if (minutes != 0 && heures == 0 && jours == 0 && mois == 0 && annees == 0) {
                return `il y a ${minutes} minutes`;
            } else if (heures != 0 && jours == 0 && mois == 0 && annees == 0) {
                return `il y a ${heures} heures`;
            } else if (jours != 0 && mois == 0 && annees == 0) {
                return `il y a ${jours} jours`;
            } else if (mois != 0 && annees == 0) {
                return `il y a ${mois} mois`;
            } else {
                return `il y a ${annees} années`;
            }
        }
        setInterval(function(){
            var options = document.querySelectorAll('.temps');
            options.forEach((option) => {
                temps = tempsEcoule('{{strtotime(now())}}');
                if (temps != option.innerHTML) {
                    option.innerHTML = temps;
                }
            });
        },1000)
	</script>
@endsection
