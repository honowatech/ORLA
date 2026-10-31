{{--
    Carte statistique colorée et sobre.
    Variables : couleur (primary|success|info|warning|danger|secondary|dark), icone (feather),
    valeur, libelle, note (optionnel), unite (optionnel), lien (optionnel), petite (bool), teinte (bool)
--}}
@php
    $couleur = $couleur ?? 'primary';
    $petite = $petite ?? false;
    $teinte = $teinte ?? true;
    $balise = isset($lien) && $lien ? 'a' : 'div';
@endphp
<{{ $balise }} @if($balise == 'a') href="{{ $lien }}" @endif class="card stat-card stat-{{ $couleur }} {{ $teinte ? 'stat-tint' : '' }} {{ $petite ? 'stat-card-sm' : '' }} mb-2">
    <div class="card-body">
        <span class="stat-icon"><i data-feather="{{ $icone }}"></i></span>
        <div class="stat-body">
            <p class="stat-value">{{ $valeur }} @if(!empty($unite))<small>{{ $unite }}</small>@endif</p>
            <p class="stat-label">{{ $libelle }}</p>
            @if(!empty($note))<small class="stat-note">{!! $note !!}</small>@endif
        </div>
    </div>
</{{ $balise }}>
