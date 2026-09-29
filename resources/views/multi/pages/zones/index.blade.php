@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')

@section('title')
{{'Toute les Zones '}}
@endsection
@section('contenu')
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
            <div class="content-header row">
    	 <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Liste des zones</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item active">Liste des zones
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
            </div>
    <div class="card pl-1 pr-1">
            <div class="content-body"> 
                <div class="row justify-content-between bg-light-secondary" >
                    <a class="btn btn-primary m-1" href="{{route('zone.create')}}">
                        <i data-feather="plus"></i>
                    </a>
                        <form action="{{ route('zone.index') }}" class="col-lg-3 mt-1" id="search" method="GET">
                            @csrf
                            <div class="form-group">
                                <div class="input-group input-group-merge">
                                    <div class="input-group-prepend ">
                                        <span class="input-group-text"><i data-feather='search'></i></span>
                                    </div>
                                    <input type="search" class="form-control" name="search" value="{{ session()->get('search')}}" placeholder="Rechercher une zone">
                                </div>
                            </div>
                            <input type="hidden" name="page" id="page" value="">
                        </form>
                    </div>
                </div>
                <!-- Table without card start -->
                <div class="row" id="table-without-card">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr class="text-center">
                                        <th>N°</th>
                                        <th>libelle</th>
                                        @if(filter(['routeur','superviseur_ville'],auth()->user()) != 'true')
                                        <th>Ville</th>
                                        @endif
                                        <th>Date création</th>
                                        <th>statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	@php
                                	$count = 20*$zones->currentPage() -19 ;
                                	@endphp
                                	@foreach($zones as $zone)
                                    @if(filter(['routeur','superviseur_ville'],auth()->user()) == 'true' &&  $zone->id_ville != auth()->user()->id_ville)
                                        @continue
                                    @endif
	                                    <tr class="text-center">
	                                        <td>
	                                        	{{$count}}
	                                        	@php $count++ @endphp
	                                        </td>
	                                        <td>
	                                            <span class="font-weight-bold">{{$zone->libelle}}</span>
                                            </td>
                                            @if(filter(['routeur','superviseur_ville'],auth()->user()) != 'true')
                                            <td>
                                                {{$zone->ville->libelle}} 
                                            </td>
                                            @endif
	                                        <td>
                                                <span class="badge badge-glow badge-light-dark">Le {{Ladate($zone->updated_at)}} à {{Heure($zone->updated_at)}}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-glow @if($zone->statut == 0)badge-danger @else badge-success @endif">
                                                    @if($zone->statut == 0)
                                                        Désactivé
                                                    @else 
                                                        Actif
                                                    @endif
                                                </span>
                                            </td>
	                                        <td>
	                                            <div>
	                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown" style="margin-bottom: 0px !important;">
	                                                    <i data-feather="more-vertical"></i>
	                                                </button>
	                                                <div class="dropdown-menu">
                                                        <a class="dropdown-item " href="{{route('zone.show',$zone)}}">
                                                            <i data-feather="eye" class="mr-50 text-dark"></i>
                                                            <span class="ml-1">Détails</span>
                                                        </a> 
                                                        <a class="dropdown-item " href="{{route('zone.edit',$zone)}}">
                                                            <i data-feather="edit-2" class="mr-50 text-info"></i>
                                                            <span class="ml-1">Editer</span>
                                                        </a>
                                                        @if($zone->statut == 0)
                                                            <span class="dropdown-item" data-toggle="modal" onclick="remplir('{{route('zone.destroy',$zone->id)}}',{{ Js::from(e($zone->libelle)) }},' Activer ','button_footer')" data-target="#danger">
                                                                <i data-feather='check' class="text-success"></i>
                                                                <span class="ml-1" type="button">
                                                                    Activer
                                                                </span>
                                                            </span>
                                                        @else
                                                            <span class="dropdown-item" data-toggle="modal" onclick="remplir('{{route('zone.destroy',$zone->id)}}',{{ Js::from(e($zone->libelle)) }},' Désactiver ','button_footer')" data-target="#danger">
                                                                <i data-feather='x' class="text-danger"></i>
                                                                <span class="ml-1" type="button">
                                                                    désactiver
                                                                </span>
                                                            </span>
                                                        @endif
                                                    </div>
	                                            </div>
	                                        </td>
	                                    </tr>
	                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @if ($zones->hasPages())
                    <nav class="d-flex col-12 mt-1 justify-items-center justify-content-center">
                        <div class="row mx-auto justify-content-center flex-fill d-md-none">
                            <div class="mr-1 d-md-none">
                                <p class="small text-muted">
                                    {!! __('De') !!}
                                    <span class="fw-semibold">{{ $zones->firstItem() }}</span>
                                    {!! __('à') !!}
                                    <span class="fw-semibold">{{ $zones->lastItem() }}</span>
                                    {!! __('sur') !!}
                                    <span class="fw-semibold">{{ $zones->total() }}</span>
                                    {!! __('resultats') !!}
                                </p>
                            </div>
                            <ul class="pagination">
                                {{-- Previous Page Link --}}
                                @if ($zones->onFirstPage())
                                    <li class="page-item disabled" aria-disabled="true">
                                        <span class="page-link">Précédent</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" onclick="mettre({{ $zones->currentPage()-1 }})" rel="prev">Précédent</a>
                                    </li>
                                @endif

                                {{-- Next Page Link --}}
                                @if ($zones->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" onclick="mettre({{ $zones->currentPage()+1 }})" rel="next">Suivant</a>
                                    </li>
                                @else
                                    <li class="page-item disabled" aria-disabled="true">
                                        <span class="page-link">Suivant</span>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <div class="d-none flex-md-fill d-md-flex align-items-md-center justify-content-md-center">
                            <div class="mr-1">
                                <p class="small text-muted">
                                    {!! __('De') !!}
                                    <span class="fw-semibold">{{ $zones->firstItem() }}</span>
                                    {!! __('à') !!}
                                    <span class="fw-semibold">{{ $zones->lastItem() }}</span>
                                    {!! __('sur') !!}
                                    <span class="fw-semibold">{{ $zones->total() }}</span>
                                    {!! __('resultats') !!}
                                </p>
                            </div>

                            <div>
                                <ul class="pagination">
                                    {{-- Previous Page Link --}}
                                    @if ($zones->onFirstPage())
                                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                                            <span class="page-link" aria-hidden="true">&lsaquo;</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" onclick="mettre({{ $zones->currentPage()-1 }})" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($zones->links()->elements as $element)
                                        {{-- "Three Dots" Separator --}}
                                        @if (is_string($element))
                                            <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                                        @endif

                                        {{-- Array Of Links --}}
                                        @if (is_array($element))
                                            @foreach ($element as $page => $url)
                                                @if ($page == $zones->currentPage())
                                                    <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                                                @else
                                                    <li class="page-item"><a class="page-link" onclick="mettre({{ $page }})">{{ $page }}</a></li>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($zones->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" onclick="mettre({{ $zones->currentPage()-1 }})" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                                        </li>
                                    @else
                                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                                            <span class="page-link" aria-hidden="true">&rsaquo;</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </nav>
                @endif
                </div>
</div>
    
</div>                         
</div>
</div>
</div>
</div>
<div id="button_footer" hidden>
    <form method="POST" class="destroy_form">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-gradient-success btn-success ">Confirmer</button>
    </form>
<button type="button" class="btn btn-gradient-danger btn-danger " data-dismiss="modal">Annuler</button>
</div>

@endsection
@section('javascript')
	<script type="text/javascript">
		document.querySelector('#zoneListe').classList.add('active');
	</script>
@endsection