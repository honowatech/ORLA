{{-- En-tête commun des pages parc. Variables : $titre, $sous_menu (clé active) --}}
<div class="content-header row">
    <div class="content-header-left col-md-12 col-12 mb-1">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">{{ $titre }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a></li>
                        <li class="breadcrumb-item"><a href="{{route('parc.index')}}">Parc automobile</a></li>
                        <li class="breadcrumb-item active">{{ $titre }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 mb-2">
        <ul class="nav nav-pills flex-wrap" style="gap:.25rem">
            <li class="nav-item"><a class="nav-link {{ $sous_menu == 'tableau' ? 'active' : '' }}" href="{{route('parc.index')}}"><i data-feather="bar-chart-2"></i> Tableau de bord</a></li>
            <li class="nav-item"><a class="nav-link {{ $sous_menu == 'vehicules' ? 'active' : '' }}" href="{{route('vehicule.index')}}"><i data-feather="truck"></i> Véhicules</a></li>
            <li class="nav-item"><a class="nav-link {{ $sous_menu == 'alertes' ? 'active' : '' }}" href="{{route('parc.alertes')}}"><i data-feather="bell"></i> Alertes</a></li>
            <li class="nav-item"><a class="nav-link {{ $sous_menu == 'documents' ? 'active' : '' }}" href="{{route('vehicule_document.index')}}"><i data-feather="file-text"></i> Documents</a></li>
            <li class="nav-item"><a class="nav-link {{ $sous_menu == 'entretiens' ? 'active' : '' }}" href="{{route('vehicule_maintenance.index')}}"><i data-feather="tool"></i> Entretiens</a></li>
            <li class="nav-item"><a class="nav-link {{ $sous_menu == 'carburant' ? 'active' : '' }}" href="{{route('vehicule_carburant.index')}}"><i data-feather="droplet"></i> Carburant</a></li>
            <li class="nav-item"><a class="nav-link {{ $sous_menu == 'incidents' ? 'active' : '' }}" href="{{route('vehicule_incident.index')}}"><i data-feather="alert-triangle"></i> Incidents</a></li>
            <li class="nav-item"><a class="nav-link {{ $sous_menu == 'couts' ? 'active' : '' }}" href="{{route('parc.couts')}}"><i data-feather="pie-chart"></i> Coûts</a></li>
        </ul>
    </div>
</div>
