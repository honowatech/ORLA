@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')
@section('title')
{{'Tous les Livreurs'}}
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
                    <h2 class="content-header-title float-left mb-0">Liste des Livreurs</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item active">Liste des Livreurs
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
                    <a class="btn btn-info m-1" href="{{route('coursiers.create')}}">
                        <i data-feather="plus"></i>
                    </a>
                        <form action="{{ route('coursiers.index') }}" class="col-lg-3 mt-1" id="search" method="GET">
                            @csrf
                            <div class="form-group">
                                <div class="input-group input-group-merge">
                                    <div class="input-group-prepend ">
                                        <span class="input-group-text"><i data-feather='search'></i></span>
                                    </div>
                                    <input type="search" class="form-control" name="search" value="{{ session()->get('search')}}" placeholder="Rechercher un Livreur">
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
                                        <th>Noms Et Prénoms</th>
                                        <th>Avatar</th>
                                        <th>Téléphone</th>
                                        <th>Statut</th>
                                        <th>Authentification</th>
                                        @if(filter(['routeur','superviseur_ville'],auth()->user()) != 'true')
                                        <th>Ville</th>
                                        @endif
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	@php
                                	$count = 20*$coursiers->currentPage() -19 ;
                                	@endphp
                                	@foreach($coursiers as $coursier)
                                    @if(filter(['routeur','superviseur_ville'],auth()->user()) == 'true' && $coursier->coursier_utilisateur->id_ville != auth()->user()->id_ville)
                                        @continue
                                    @endif
                                    <tr class="text-center {{ $coursier->infos_perso->cni == null ? 'bg-light-danger' : '' }}" title="{{ $coursier->infos_perso->cni == null ? 'Coursier Non Authentifié' : 'Coursier Authentifié' }}">
	                                        <td>
	                                        	{{$count}}
	                                        	@php $count++ @endphp
	                                        </td>
	                                        <td>
	                                            <span class="font-weight-bolder">
	                                            	{{$coursier->noms .' '. $coursier->prenoms }}
	                                            </span>
	                                        </td>
	                                        <td>
	                                        	<div class="avatar bg-dark avatar-bg badge-glow">
	                                        		<span class="avatar-content">
		                                        		{{$coursier->noms[0]}}{{ $coursier->prenoms[0]}}
	                                        		</span>
	                                        	</div>
	                                        </td>
	                                        <td>
	                                            <span class="text-center font-weight-bolder @if($coursier->telephone == null)text-dark @endif">
		                                            @if($coursier->telephone == null)
		                                             	<div class="spinner-grow spinner-grow-sm" role="status">
                                        				</div>
		                                            @else 
		                                            	{{phone($coursier->telephone)}}
		                                            @endif
	                                         	</span>
	                                        </td>
                                            <td>
                                                <span class="badge badge-glow @if($coursier->statut == 0)badge-danger @else badge-success @endif">
                                            		@if($coursier->statut == 0)
		                                             	Désactivé
		                                            @else 
		                                            	Actif
		                                            @endif
                                                </span>
                                            </td>
                                            <td>
                                                    @if($coursier->infos_perso->cni == null)
                                                        <a class="text-info btn-info btn-sm" title="Cliquer Pour authentifier" href="{{route('informations_personnels.edit',$coursier->id.'-2')}}"> Authentifier </a>
                                                    @else 
                                                        <i data-feather="user-check" class="text-success"></i> 
                                                        <small class="text-success">Authentifié</small>
                                                    @endif
                                            </td>
                                            @if(filter(['routeur','superviseur_ville'],auth()->user()) != 'true')
                                            <td class="font-weight-bolder">
                                                @if($coursier->user != null && $coursier->user->ville != null)
                                                {{$coursier->user->ville->libelle}} 
                                                @endif
                                            </td>
                                            @endif
	                                        <td>
	                                            <div>
	                                                <button type="button" style="margin-bottom: 0px !important;" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
	                                                    <i data-feather="more-vertical"></i>
	                                                </button>
	                                                <div class="dropdown-menu">
	                                                    <a class="dropdown-item" href="{{route('coursiers.show',$coursier->id)}}">
	                                                        <i data-feather='eye' class="text-dark mr-50"></i>
	                                                        <span>Détails</span>
	                                                    </a>
                                                        <a class="dropdown-item" href="{{route('coursiers.edit',$coursier->id)}}">
                                                            <i data-feather="edit-2" class="text-info mr-50"></i>
                                                            <span>Editer</span>
                                                        </a>
                                                        <span class="dropdown-item" data-toggle="modal" onclick="remplir('{{route('coursiers.destroy',$coursier->id)}}',{{ Js::from(e($coursier->noms.' '.$coursier->prenoms)) }},@if($coursier->statut == 1) ' Désactiver ' @else ' Activer ' @endif ,'button_footer')" data-target="#danger">
                                                            @if($coursier->statut == 1)
                                                                <i data-feather='x' class="text-danger mr-50"></i>
                                                                <span type="button">
                                                                    Désactiver
                                                                </span>
                                                            @else
                                                                <i data-feather='check' class=" text-success mr-50"></i>
                                                                <span type="button">
                                                                    Activer
                                                                </span> 
                                                            @endif
                                                        </span>
                                                    </div>
	                                            </div>
	                                        </td>
	                                    </tr>
	                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                
                @if ($coursiers->hasPages())
                    <nav class="d-flex col-12 mt-1 justify-items-center justify-content-center">
                        <div class="row mx-auto justify-content-center flex-fill d-md-none">
                            <div class="mr-1 d-md-none">
                                <p class="small text-muted">
                                    {!! __('De') !!}
                                    <span class="fw-semibold">{{ $coursiers->firstItem() }}</span>
                                    {!! __('à') !!}
                                    <span class="fw-semibold">{{ $coursiers->lastItem() }}</span>
                                    {!! __('sur') !!}
                                    <span class="fw-semibold">{{ $coursiers->total() }}</span>
                                    {!! __('resultats') !!}
                                </p>
                            </div>
                            <ul class="pagination">
                                {{-- Previous Page Link --}}
                                @if ($coursiers->onFirstPage())
                                    <li class="page-item disabled" aria-disabled="true">
                                        <span class="page-link">Précédent</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" onclick="mettre({{ $coursiers->currentPage()-1 }})" rel="prev">Précédent</a>
                                    </li>
                                @endif

                                {{-- Next Page Link --}}
                                @if ($coursiers->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" onclick="mettre({{ $coursiers->currentPage()+1 }})" rel="next">Suivant</a>
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
                                    <span class="fw-semibold">{{ $coursiers->firstItem() }}</span>
                                    {!! __('à') !!}
                                    <span class="fw-semibold">{{ $coursiers->lastItem() }}</span>
                                    {!! __('sur') !!}
                                    <span class="fw-semibold">{{ $coursiers->total() }}</span>
                                    {!! __('resultats') !!}
                                </p>
                            </div>

                            <div>
                                <ul class="pagination">
                                    {{-- Previous Page Link --}}
                                    @if ($coursiers->onFirstPage())
                                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                                            <span class="page-link" aria-hidden="true">&lsaquo;</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" onclick="mettre({{ $coursiers->currentPage()-1 }})" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($coursiers->links()->elements as $element)
                                        {{-- "Three Dots" Separator --}}
                                        @if (is_string($element))
                                            <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                                        @endif

                                        {{-- Array Of Links --}}
                                        @if (is_array($element))
                                            @foreach ($element as $page => $url)
                                                @if ($page == $coursiers->currentPage())
                                                    <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                                                @else
                                                    <li class="page-item"><a class="page-link" onclick="mettre({{ $page }})">{{ $page }}</a></li>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($coursiers->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" onclick="mettre({{ $coursiers->currentPage()-1 }})" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
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
        function mettre(number_page){
           document.querySelector('#page').value = number_page;
           document.querySelector('#search').submit() 
        }
		document.querySelector('#CoursierListe')?.classList.add('active');
	</script>
@endsection