<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Suivi de votre livraison - {{ $entreprise->name }}</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.css') }}">
    <style>
        :root { --marque: {{ $entreprise->couleur_principale ?: '#00cfe8' }}; }
        body { background: #f4f6f9; }
        .marque { color: var(--marque); }
        .etape { display: flex; align-items: center; gap: .75rem; padding: .5rem 0; color: #b9b9c3; }
        .etape .point { width: 14px; height: 14px; border-radius: 50%; background: #e0e0e6; flex: none; }
        .etape.faite { color: #5e5873; }
        .etape.faite .point { background: var(--marque); }
        .logo { max-height: 56px; max-width: 180px; }
    </style>
</head>
<body>
    <div class="container py-4" style="max-width: 560px">
        <div class="text-center mb-3">
            @if($entreprise->logo)
                <img class="logo mb-1" src="{{ route('entreprise.logo', $entreprise->slug) }}" alt="{{ $entreprise->name }}">
            @endif
            <h3 class="marque mb-0">{{ $entreprise->name }}</h3>
        </div>
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-25">Commande n° {{ $commande->id }}</p>
                <h4 class="mb-2">
                    <span class="badge badge-{{ $etat[1] }}">{{ $etat[0] }}</span>
                </h4>

                @if($etat[2] > 0)
                    @foreach(collect($etapes)->where(2, '>', 0) as $etape)
                        <div class="etape {{ $etape[2] <= $etat[2] ? 'faite' : '' }}">
                            <span class="point"></span><span>{{ $etape[0] }}</span>
                        </div>
                    @endforeach
                @endif

                <hr>
                @if($commande->quartier_livraison)
                    <p class="mb-25">Livraison vers : <b>{{ $commande->quartier_livraison->libelle }}</b></p>
                @endif
                @if($commande->statut == 'livre' && $commande->date_livre)
                    <p class="mb-0">Livrée le <b>{{ \Carbon\Carbon::parse($commande->date_livre)->format('d/m/Y à H:i') }}</b></p>
                @elseif($commande->date_livraison)
                    <p class="mb-0">Livraison prévue le <b>{{ \Carbon\Carbon::parse($commande->date_livraison)->format('d/m/Y') }}</b></p>
                @endif
            </div>
        </div>
        @if($entreprise->telephone)
            <p class="text-center text-muted mt-2">Une question ? Contactez {{ $entreprise->name }} au {{ Sa_phone($entreprise->telephone) }}</p>
        @endif
    </div>
</body>
</html>
