@extends('admin/templates/template')

@section('title')
{{'Démarrage'}}
@endsection
@section('contenu')
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-12 col-12 mb-2">
                <h2 class="content-header-title mb-0">Bienvenue, {{$entreprise->name}}</h2>
                <p class="mt-1 mb-0">
                    @if($entreprise->enEssai())
                        Votre essai gratuit est actif jusqu'au <b>{{$entreprise->essai_jusqu_au->format('d/m/Y')}}</b>, avec toutes les fonctionnalités.
                        <a href="{{route('Sc-transaction.create')}}">Choisir un abonnement</a>
                    @elseif($entreprise->abonnementValide())
                        Abonnement actif jusqu'au <b>{{$entreprise->date_fin->format('d/m/Y')}}</b>.
                    @else
                        Votre accès est en lecture seule. <a href="{{route('Sc-transaction.create')}}">Renouveler l'abonnement</a>
                    @endif
                </p>
            </div>
        </div>
        <div class="content-body">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Mettre en place votre espace</h4>
                    <span class="badge badge-light-info">{{$faites}} / {{count($etapes)}} étapes</span>
                </div>
                <div class="card-body">
                    <div class="progress progress-bar-info mb-2" style="height: 8px">
                        <div class="progress-bar" role="progressbar" style="width: {{ round($faites * 100 / count($etapes)) }}%"></div>
                    </div>
                    <ul class="list-group">
                        @foreach($etapes as $etape)
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap" style="gap: .5rem">
                            <div>
                                <i data-feather="{{ $etape['nombre'] > 0 ? 'check-circle' : 'circle' }}" class="{{ $etape['nombre'] > 0 ? 'text-success' : 'text-muted' }} mr-50"></i>
                                <b>{{$etape['titre']}}</b>
                                @if($etape['nombre'] > 0)<span class="badge badge-light-success ml-50">{{$etape['nombre']}}</span>@endif
                                <div class="text-muted small ml-2">{{$etape['texte']}}</div>
                            </div>
                            <a href="{{route($etape['route'])}}" class="btn btn-sm {{ $etape['nombre'] > 0 ? 'btn-outline-secondary' : 'btn-info' }}">
                                {{ $etape['nombre'] > 0 ? 'Gérer' : 'Commencer' }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    <div class="text-right mt-2">
                        <a href="{{route('home.admin')}}" class="btn btn-outline-info">Aller au tableau de bord</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
