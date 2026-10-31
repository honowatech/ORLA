@extends('admin/templates/template')

@section('title')
{{'Mon abonnement'}}
@endsection
@section('contenu')
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-12 col-12 mb-2">
                <h2 class="content-header-title mb-0">Mon abonnement</h2>
            </div>
        </div>
        <div class="content-body">
            <div class="row match-height">
                <div class="col-lg-6 col-12">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">Offre en cours</h4></div>
                        <div class="card-body">
                            @if($entreprise->enEssai())
                                <p>Essai gratuit, toutes fonctionnalités incluses, jusqu'au <b>{{$entreprise->essai_jusqu_au->format('d/m/Y')}}</b>.</p>
                            @elseif($entreprise->abonnement)
                                <p>Offre <b>{{$entreprise->abonnement->titre}}</b> ({{Sa_montant($entreprise->abonnement->montant)}} FCFA).</p>
                                @if($entreprise->abonnementValide())
                                    <p>Payée jusqu'au <b>{{$entreprise->date_fin->format('d/m/Y')}}</b>@if($entreprise->abonnement->periode_grace), puis {{$entreprise->abonnement->periode_grace}} jour(s) de grâce @endif.</p>
                                @else
                                    <p class="text-danger">Expirée depuis le {{$entreprise->finAcces()?->format('d/m/Y')}} : votre espace est en lecture seule.</p>
                                @endif
                            @else
                                <p class="text-danger">Aucun abonnement : votre espace est en lecture seule.</p>
                            @endif
                            <a href="{{route('Sc-transaction.create')}}" class="btn btn-info">
                                {{ $entreprise->abonnementValide() ? 'Prolonger ou changer d\'offre' : 'Choisir une offre et payer' }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">Consommation</h4></div>
                        <div class="card-body">
                            @foreach($utilisation as $ligne)
                                <div class="d-flex justify-content-between">
                                    <span>{{$ligne['libelle']}}</span>
                                    <b>{{$ligne['utilise']}} / {{$ligne['limite'] ?? 'illimité'}}</b>
                                </div>
                                @if($ligne['limite'])
                                    <div class="progress mb-1 {{ $ligne['utilise'] >= $ligne['limite'] ? 'progress-bar-danger' : 'progress-bar-info' }}" style="height: 6px">
                                        <div class="progress-bar" style="width: {{ min(100, round($ligne['utilise'] * 100 / $ligne['limite'])) }}%"></div>
                                    </div>
                                @else
                                    <hr class="my-50">
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4 class="card-title">Offres disponibles</h4></div>
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Offre</th><th>Prix</th><th>Livreurs</th><th>Véhicules</th><th>Agents</th><th>Commandes / mois</th></tr></thead>
                        <tbody>
                            @foreach($offres as $offre)
                            <tr class="{{ $offre->id == $entreprise->id_abonnement ? 'table-info' : '' }}">
                                <td><b>{{$offre->titre}}</b></td>
                                <td>{{Sa_montant($offre->montant)}} FCFA / {{$offre->accumulateur > 1 ? $offre->accumulateur.' ' : ''}}{{$offre->type_periode}}</td>
                                <td>{{$offre->max_coursiers ?? 'Illimité'}}</td>
                                <td>{{$offre->max_vehicules ?? 'Illimité'}}</td>
                                <td>{{$offre->max_agents ?? 'Illimité'}}</td>
                                <td>{{$offre->max_commandes_mois ?? 'Illimité'}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4 class="card-title">Historique des paiements</h4></div>
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Date</th><th>Offre</th><th>Montant</th><th>Période</th><th>Statut</th><th></th></tr></thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                            <tr>
                                <td>{{$transaction->created_at->format('d/m/Y H:i')}}</td>
                                <td>{{$transaction->abonnement->titre ?? '—'}}</td>
                                <td>{{Sa_montant($transaction->montant)}} FCFA</td>
                                <td>@if($transaction->statut == 'success'){{\Carbon\Carbon::parse($transaction->date_debut)->format('d/m/Y')}} → {{\Carbon\Carbon::parse($transaction->date_fin)->format('d/m/Y')}}@endif</td>
                                <td><span class="badge badge-light-{{$statuts[$transaction->statut][1] ?? 'secondary'}}">{{$statuts[$transaction->statut][0] ?? $transaction->statut}}</span></td>
                                <td><a href="{{route('Sc-transaction.show',$transaction->id)}}">Reçu</a></td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted">Aucun paiement pour le moment.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
