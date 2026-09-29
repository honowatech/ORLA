@extends('admin/templates/template')

@section('title')
{{'Liste des Quartiers '}}
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
                        <h2 class="content-header-title float-left mb-0">Liste des Quartiers</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item active">Liste des Quartiers
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
            </div>
    <div class="card pl-1 pr-1">
            <div class="content-body">
            <div class="content-body">
                <div class="row justify-content-between bg-light-secondary" >
                    <a class="btn btn-primary col-lg-2 m-1" href="{{route('quartier.create')}}">Nouveau Quartier</a>
                        <form action="{{ route('quartier.index') }}" class="col-lg-3 mt-1" id="search" method="POST">
                            @csrf
                            @method('GET')
                            <div class="form-group">
                                <div class="input-group input-group-merge">
                                    <div class="input-group-prepend ">
                                        <span class="input-group-text"><i data-feather='search'></i></span>
                                    </div>
                                    <input type="search" class="form-control" name="recherche" value="{{ session()->get('search')}}" placeholder="Rechercher un coursier">
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
                                        <th>ville</th>
                                        <th>Date création</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	@php
                                	$count = 20*$quartiers->currentPage() -19 ;
                                	@endphp
                                	@foreach($quartiers as $quartier)
	                                    <tr class="text-center">
	                                        <td>
	                                        	{{$count}}
	                                        	@php $count++ @endphp
	                                        </td>
	                                        <td>
	                                            <span class="font-weight-bold">{{$quartier->libelle}}</span>
                                            </td>
	                                        
                                            <td>
                                                {{$quartier->ville->libelle}} 
                                            </td>
	                                        <td>Le
                                                <span class="badge badge-pill badge-light-info">{{explode(' ',$quartier->updated_at)[0]}}</span> à
                                                <span class="badge badge-pill badge-light-info">{{explode(' ',$quartier->updated_at)[1]}}</span></td>
	                                        <td>
	                                            <div>
	                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown" style="margin-bottom: 0px !important;">
	                                                    <i data-feather="more-vertical"></i>
	                                                </button>
	                                                <div class="dropdown-menu">
	                                                    <a class="dropdown-item " href="{{route('quartier.edit',$quartier->id)}}">
	                                                        <i data-feather="edit-2" class="mr-50"></i>
	                                                        <span class="ml-1">Editer</span>
	                                                    </a>
                                                        @if($quartier->boutiques->count()>0||$quartier->point_relais->count()>0||$quartier->commandes_pointdepart->count()>0||$quartier->commandes_pointarrivee->count()>0)
                                                        @else
                                                        <span class="text-danger dropdown-item" data-toggle="modal" onclick="remplir('{{route('quartier.destroy',$quartier->id)}}','{{$quartier->libelle}}','supprimer','button_footer')" data-target="#danger">
                                                            <i data-feather='trash-2'></i>
                                                            <span class="ml-1 text-danger" type="button">
                                                                Supprimer
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
                    <div class="border-top mt-1">
                        <div class="d-flex col-md-10 mx-auto" style=" padding:1rem; overflow: auto; border-radius: 4px;">
                            <ul class="pagination"  style="margin-bottom:  0rem">
                            {{-- Previous Page Link --}}
                                @if ($quartiers->onFirstPage())
                                    <li class="page-item" style="opacity: 0.6; cursor: no-drop;">
                                        <span disabled class="page-link">Précédent</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" onclick="mettre({{ $quartiers->currentPage()-1 }})" rel="prev" aria-label="@lang('pagination.previous')">Précédent</a>
                                    </li>
                                @endif
                                @for($i=1;$i<=$quartiers->lastPage();$i++)
                                    <li class="page-item @if($i==$quartiers->currentPage()) active @endif" >
                                        <a class="page-link" onclick="@if($i!=$quartiers->currentPage()) mettre({{$i}}) @endif" rel="prev" aria-label="@lang('pagination.previous')">{{$i}}</a>
                                    </li>
                                @endfor
                                {{-- Next Page Link --}}
                                @if ($quartiers->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" onclick="mettre({{ $quartiers->currentPage()+1 }})" rel="next" aria-label="@lang('pagination.next')">Suivant</a>
                                    </li>
                                @else
                                    <li class="page-item" style="opacity: 0.6; cursor: no-drop;">
                                        <span disabled class="page-link">Suivant</span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
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
        <button type="submit" class="btn btn-gradient-success btn-success round btn-lg">Confirmer</button>
    </form>
<button type="button" class="btn btn-gradient-danger btn-danger round btn-lg" data-dismiss="modal">Annuler</button>
</div>

@endsection
@section('javascript')
	<script type="text/javascript">
        function mettre(number_page){
           document.querySelector('#page').value = number_page;
           document.querySelector('#search').submit() 
        }
		document.querySelector('#quartierListe').classList.add('active');
	</script>
@endsection