@extends('superadmin/layout/template')
@section('menu')
    @include('superadmin/menu/menu')
@endsection
@section('title')
    {{ 'Super Admin - Tableau de bord' }}
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="<?= asset('app-assets/css/core/menu/menu-types/vertical-menu.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset('app-assets/css/plugins/extensions/ext-component-toastr.css') ?>">
@endsection
@section('contenu')
@php
    $variation = $stats['revenu_prec'] > 0 ? round((($stats['revenu_mois'] - $stats['revenu_prec']) / $stats['revenu_prec']) * 100) : null;
    $statuts_transaction = [
        'success' => ['success', 'check-circle', 'Réussi'],
        'waiting' => ['warning', 'clock', 'En attente'],
        'failed' => ['danger', 'x-circle', 'Échoué'],
        'cancelled' => ['secondary', 'slash', 'Annulé'],
    ];
    $methodes = ['mobile' => 'Mobile Money', 'bank' => 'Banque', 'application' => 'Application'];
    $max_abo = max(1, $repartition_abonnements->max() ?? 0);
    $today = \Carbon\Carbon::now()->startOfDay();
@endphp
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Tableau de bord</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active">{{ Sa_Ladate(now()) }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <section id="dashboard-superadmin">
                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            @include('partials.stat_card', ['couleur' => 'primary', 'icone' => 'briefcase', 'valeur' => $stats['clients_actifs'], 'unite' => '/ '.$stats['clients'], 'libelle' => 'Clients actifs', 'note' => $stats['clients_abonnes'].' avec abonnement en cours', 'lien' => route('Sa-client.index')])
                        </div>
                        <div class="col-xl-3 col-md-6">
                            @include('partials.stat_card', ['couleur' => $stats['clients_expires'] + $stats['clients_sans_abonnement'] > 0 ? 'danger' : 'success', 'icone' => 'alert-circle', 'valeur' => $stats['clients_expires'] + $stats['clients_sans_abonnement'], 'libelle' => 'Abonnements à régulariser', 'note' => $stats['clients_expires'].' expiré(s) · '.$stats['clients_sans_abonnement'].' sans formule'])
                        </div>
                        <div class="col-xl-3 col-md-6">
                            @include('partials.stat_card', ['couleur' => 'success', 'icone' => 'dollar-sign', 'valeur' => Sa_montant($stats['revenu_mois']), 'unite' => 'XAF', 'libelle' => 'Encaissé ce mois', 'note' => ($variation !== null ? '<span class="stat-trend text-'.($variation >= 0 ? 'success' : 'danger').'">'.($variation >= 0 ? '+' : '').$variation.' %</span> vs '.Sa_montant($stats['revenu_prec']).' XAF' : $stats['paiements_mois'].' paiement(s) réussi(s)')])
                        </div>
                        <div class="col-xl-3 col-md-6">
                            @include('partials.stat_card', ['couleur' => $stats['paiements_attente'] > 0 ? 'warning' : 'info', 'icone' => 'credit-card', 'valeur' => $stats['paiements_attente'], 'libelle' => 'Paiements en attente', 'note' => $stats['paiements_echoues_mois'].' échoué(s) ce mois · '.Sa_montant($stats['revenu_total']).' XAF cumulés'])
                        </div>
                    </div>

                    <div class="row match-height">
                        <div class="col-lg-7">
                            <div class="card panel-card panel-warning">
                                <div class="card-header">
                                    <h4 class="card-title"><span class="panel-icon"><i data-feather="bell"></i></span> Échéances d'abonnement <span class="badge badge-light-warning sx-badge-soft">{{ $echeances->count() }}</span></h4>
                                    <a href="{{ route('Sa-client.index') }}" class="panel-link">Clients</a>
                                </div>
                                <div class="card-body">
                                    <ul class="sx-list">
                                        @forelse($echeances as $client)
                                            @php $jours = (int) $today->diffInDays(\Carbon\Carbon::parse($client->date_fin)->startOfDay(), false); $grace = $client->abonnement ? (int) $client->abonnement->periode_grace : 0; @endphp
                                            <li class="{{ $jours < 0 ? 'is-danger' : ($jours <= 7 ? 'is-warning' : 'is-info') }}">
                                                <span class="sx-list-icon"><i data-feather="{{ $jours < 0 ? 'x-octagon' : 'clock' }}"></i></span>
                                                <div class="sx-list-body">
                                                    <span class="sx-list-title"><a href="{{ route('Sa-client.show', $client->id) }}">{{ Sa_name($client->name) }}</a></span>
                                                    <small class="sx-list-sub">{{ $client->abonnement ? $client->abonnement->titre : 'Sans formule' }} · fin le {{ Sa_Ladate($client->date_fin) }}@if($jours < 0 && $grace > 0) · grâce de {{ $grace }} j @endif</small>
                                                </div>
                                                <span class="sx-list-right {{ $jours < 0 ? 'text-danger font-weight-bolder' : 'text-muted' }}">{{ $jours < 0 ? 'Expiré depuis '.abs($jours).' j' : ($jours == 0 ? 'Expire aujourd\'hui' : 'Dans '.$jours.' j') }}</span>
                                            </li>
                                        @empty
                                            <li class="is-success"><span class="sx-list-icon"><i data-feather="check-circle"></i></span><div class="sx-list-body"><span class="sx-list-title">Aucune échéance sous 30 jours</span><small class="sx-list-sub">Tous les abonnements actifs sont à jour.</small></div></li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="card panel-card panel-primary">
                                <div class="card-header">
                                    <h4 class="card-title"><span class="panel-icon"><i data-feather="layers"></i></span> Répartition des formules</h4>
                                    <a href="{{ route('Sa-abonnement.index') }}" class="panel-link">{{ $stats['abonnements'] }} formule(s)</a>
                                </div>
                                <div class="card-body">
                                    @forelse($repartition_abonnements as $titre => $nombre)
                                        <div class="sx-bar-row">
                                            <div class="sx-bar-meta">
                                                <span><span class="sx-dot bg-primary"></span>{{ $titre }}</span>
                                                <span class="font-weight-bolder">{{ $nombre }} <small class="text-muted">client(s)</small></span>
                                            </div>
                                            <div class="sx-bar"><span class="bg-primary" style="width: {{ round($nombre / $max_abo * 100) }}%"></span></div>
                                        </div>
                                    @empty
                                        <div class="sx-empty"><i data-feather="layers"></i> Aucun client abonné pour le moment.</div>
                                    @endforelse
                                    <hr class="my-1">
                                    <div class="d-flex justify-content-between font-small-3"><span class="text-muted">Paiements réussis ce mois</span><span class="font-weight-bolder">{{ $stats['paiements_mois'] }}</span></div>
                                    <div class="d-flex justify-content-between font-small-3"><span class="text-muted">Revenu cumulé</span><span class="font-weight-bolder">{{ Sa_montant($stats['revenu_total']) }} XAF</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card panel-card panel-success sx-table-card">
                                <div class="card-header">
                                    <h4 class="card-title"><span class="panel-icon"><i data-feather="credit-card"></i></span> Dernières transactions</h4>
                                    <a href="{{ route('Sa-transaction.index') }}" class="panel-link">Tout voir</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead><tr><th>Date</th><th>Client</th><th>Formule</th><th>Méthode</th><th class="text-right">Montant</th><th>Statut</th></tr></thead>
                                        <tbody>
                                        @forelse($dernieres_transactions as $t)
                                            @php $st = $statuts_transaction[$t->statut] ?? ['secondary', 'help-circle', $t->statut]; @endphp
                                            <tr>
                                                <td class="text-nowrap">{{ Sa_Ladate($t->created_at) }}<br><small class="text-muted">{{ Sa_Heure($t->created_at) }}</small></td>
                                                <td>@if($t->client)<a href="{{ route('Sa-client.show', $t->client->id) }}" class="font-weight-bolder">{{ Sa_name($t->client->name) }}</a>@else - @endif</td>
                                                <td>{{ $t->abonnement ? $t->abonnement->titre : '-' }} @if($t->nbre_abonnement > 1)<small class="text-muted">× {{ $t->nbre_abonnement }}</small>@endif</td>
                                                <td>{{ $methodes[$t->methode] ?? $t->methode }}</td>
                                                <td class="text-right font-weight-bolder">{{ Sa_montant($t->montant) }} XAF</td>
                                                <td><span class="badge badge-light-{{ $st[0] }} sx-badge-soft"><i data-feather="{{ $st[1] }}" style="width:12px;height:12px"></i> {{ $st[2] }}</span></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6"><div class="sx-empty"><i data-feather="inbox"></i> Aucune transaction enregistrée.</div></td></tr>
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
        document.getElementById('home').className += 'active'
    </script>
@endsection
