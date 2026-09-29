@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')

@section('title')
{{'Liste des Types de véhicule '}}
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
                        <h2 class="content-header-title float-left mb-0">Liste des Types de véhicule</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item active">Liste des Types de véhicule
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
                    <a class="btn btn-primary m-1" href="{{route('type_vehicule.create')}}"><i data-feather="plus"></i></a>
                        <form action="{{ route('type_vehicule.index') }}" class="col-lg-3 mt-1" id="search" method="GET">
                            @csrf
                            <div class="form-group">
                                <div class="input-group input-group-merge">
                                    <div class="input-group-prepend ">
                                        <span class="input-group-text"><i data-feather='search'></i></span>
                                    </div>
                                    <input type="search" class="form-control" name="recherche" value="{{ session()->get('search')}}" placeholder="Rechercher un Type">
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
                                        <th>Libellé</th>
                                        <th>description</th>
                                        <th>Date création</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	@php
                                	$count = 10*$types_vehicule->currentPage() -9 ;
                                	@endphp
                                	@foreach($types_vehicule as $type_vehicule)
	                                    <tr class="text-center">
	                                        <td>
	                                        	{{$count}}
	                                        	@php $count++ @endphp
	                                        </td>
	                                        <td>
	                                            <span class="font-weight-bold">{{$type_vehicule->libelle}}</span></td>
	                                        <td>
                                                @if($type_vehicule->description == null )
                                                    <i data-feather="alert-triangle" class="text-danger"></i>
                                                @else
                                                    {{$type_vehicule->description}}
                                                @endif
	                                        </td>
                                            <td class="text-truncate">
                                                <span class="badge badge-glow badge-light-dark">Le {{Ladate($type_vehicule->updated_at)}} à {{Heure($type_vehicule->updated_at)}}</span>
                                            </td>
                                            <td>
                                                @if($type_vehicule->statut == 0)
                                                <span class="badge badge-glow badge-danger">Désactivé</span>
                                                @else
                                                <span class="badge badge-glow badge-success">Actif</span>
                                                @endif
                                            </td>
                                            <td>
	                                            <div>
	                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown" style="margin-bottom: 0px !important;">
	                                                    <i data-feather="more-vertical"></i>
	                                                </button>
	                                                <div class="dropdown-menu">
	                                                    <a class="dropdown-item " href="{{route('type_vehicule.edit',$type_vehicule)}}">
	                                                        <i data-feather="edit-2" class="mr-50 text-info"></i>
	                                                        <span class="ml-1">Editer</span>
	                                                    </a>
                                                        @if($type_vehicule->statut == 0)
                                                            <span class="dropdown-item" data-toggle="modal" onclick="remplir('{{route('type_vehicule.destroy',$type_vehicule->id)}}','{{$type_vehicule->libelle}}',' Activer le Type de véhicule ','button_footer')" data-target="#danger">
                                                                <i data-feather='check' class="text-success"></i>
                                                                <span class="ml-1" type="button">
                                                                    Activer
                                                                </span>
                                                            </span>
                                                        @else
                                                            <span class="dropdown-item" data-toggle="modal" onclick="remplir('{{route('type_vehicule.destroy',$type_vehicule->id)}}','{{$type_vehicule->libelle}}',' Désactiver le Type de véhicule ','button_footer')" data-target="#danger">
                                                                <i data-feather='x' class="text-danger"></i>
                                                                <span class="ml-1" type="button">
                                                                    désactiver
                                                                </span>
                                                            </span>
                                                        @endif
                                                        <span class="dropdown-item" data-toggle="modal" onclick="remplir('{{route('type_vehicule.destroy',$type_vehicule->id)}}','{{$type_vehicule->libelle}}',' supprimer ','button_delete')" data-target="#danger">
                                                            <i data-feather='trash-2' class="text-danger"></i>
                                                            <span class="ml-1" type="button">
                                                                Supprimer
                                                            </span>
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
                @if ($types_vehicule->hasPages())
                    <nav class="d-flex col-12 mt-1 justify-items-center justify-content-center">
                        <div class="row mx-auto justify-content-center flex-fill d-md-none">
                            <div class="mr-1 d-md-none">
                                <p class="small text-muted">
                                    {!! __('De') !!}
                                    <span class="fw-semibold">{{ $types_vehicule->firstItem() }}</span>
                                    {!! __('à') !!}
                                    <span class="fw-semibold">{{ $types_vehicule->lastItem() }}</span>
                                    {!! __('sur') !!}
                                    <span class="fw-semibold">{{ $types_vehicule->total() }}</span>
                                    {!! __('resultats') !!}
                                </p>
                            </div>
                            <ul class="pagination">
                                {{-- Previous Page Link --}}
                                @if ($types_vehicule->onFirstPage())
                                    <li class="page-item disabled" aria-disabled="true">
                                        <span class="page-link">Précédent</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" onclick="mettre({{ $types_vehicule->currentPage()-1 }})" rel="prev">Précédent</a>
                                    </li>
                                @endif

                                {{-- Next Page Link --}}
                                @if ($types_vehicule->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" onclick="mettre({{ $types_vehicule->currentPage()+1 }})" rel="next">Suivant</a>
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
                                    <span class="fw-semibold">{{ $types_vehicule->firstItem() }}</span>
                                    {!! __('à') !!}
                                    <span class="fw-semibold">{{ $types_vehicule->lastItem() }}</span>
                                    {!! __('sur') !!}
                                    <span class="fw-semibold">{{ $types_vehicule->total() }}</span>
                                    {!! __('resultats') !!}
                                </p>
                            </div>

                            <div>
                                <ul class="pagination">
                                    {{-- Previous Page Link --}}
                                    @if ($types_vehicule->onFirstPage())
                                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                                            <span class="page-link" aria-hidden="true">&lsaquo;</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" onclick="mettre({{ $types_vehicule->currentPage()-1 }})" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($types_vehicule->links()->elements as $element)
                                        {{-- "Three Dots" Separator --}}
                                        @if (is_string($element))
                                            <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                                        @endif

                                        {{-- Array Of Links --}}
                                        @if (is_array($element))
                                            @foreach ($element as $page => $url)
                                                @if ($page == $types_vehicule->currentPage())
                                                    <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                                                @else
                                                    <li class="page-item"><a class="page-link" onclick="mettre({{ $page }})">{{ $page }}</a></li>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($types_vehicule->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" onclick="mettre({{ $types_vehicule->currentPage()-1 }})" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
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
<div id="button_delete" hidden>
    <form method="POST" class="destroy_form">
        @csrf
        @method('DELETE')
        <input type="hidden" name="type" value="delete">
        <button type="submit" class="btn btn-gradient-success btn-success ">Confirmer</button>
    </form>
<button type="button" class="btn btn-gradient-danger btn-danger " data-dismiss="modal">Annuler</button>
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
		document.querySelector('#Type_vehiculeListe').classList.add('active');
	</script>
@endsection