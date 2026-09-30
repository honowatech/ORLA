@extends('admin/templates/template')

@section('title')
{{'Liste des Utilisateurs '}}
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
                        <h2 class="content-header-title float-left mb-0">Liste des Utilisateurs</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item active">Liste des Utilisateurs
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
                    <a class="btn btn-dark col-lg-2 m-1" href="{{route('users.create')}}">Nouvel Utilisateur</a>
                        <form action="{{ route('users.index') }}" class="col-lg-3 mt-1" id="search" method="POST">
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
                                        <th>Noms Et Prénoms</th>
                                        <th>E-mail</th>
                                        <th>Avatar</th>
                                        <th>Téléphone</th>
                                        <th>Rôle</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	@php
                                	$count = 20*$users->currentPage() -19 ;
                                	@endphp
                                	@foreach($users as $user)
	                                    <tr class="text-center">
	                                        <td>
	                                        	{{$count}}
	                                        	@php $count++ @endphp
	                                        </td>
	                                        <td>
	                                            <span class="font-weight-bold">
	                                            	{{$user->noms}}
	                                            </span>
	                                        </td>
	                                        <td>
	                                            <span class="font-weight-bold">
	                                            	{{$user->email}}
	                                            </span>
	                                        </td>
	                                        <td>
	                                        	<div class="avatar bg-light-info avatar-bg">
	                                        		<span class="avatar-content">
		                                        		{{explode(' ',$user->noms)[0][0]}}{{isset(explode(' ',$user->noms)[1][0]) ? explode(' ',$user->noms)[1][0] : explode(' ',$user->noms)[0][1] }}
	                                        		</span>
	                                        	</div>
	                                        </td>
	                                        <td>
	                                            <span class="text-center @if($user->telephone == null)text-dark @endif">
		                                            @if($user->telephone == null)
		                                             	<div class="spinner-grow spinner-grow-sm" role="status">
                                        				</div>
		                                            @else 
		                                            	+237 {{$user->telephone}}
		                                            @endif
	                                         	</span>
	                                        </td>
                                            <td>
                                                @php
                                                    $table = ['secondary','primary','info','success',];
                                                @endphp
                                                @for($i = 0 ; $i < count($table) ; $i ++ )
                                                    @if($i == $user->type_utilisateur->id-1 )
                                                        <span class="badge badge-pill badge-light-{{ $table[ $i ] }}">
                                                    @endif
                                                @endfor
                                                {{ $user->type_utilisateur->libelle }}
                                                 </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-pill @if($user->statut == 0)badge-light-danger @else badge-light-success @endif">
                                            		@if($user->statut == 0)
		                                             	Désactivé
		                                            @else 
		                                            	Actif
		                                            @endif
                                                </span>
                                            </td>
	                                        <td>
	                                            <div>
	                                                <button type="button" style="margin-bottom: 0px !important;" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
	                                                    <i data-feather="more-vertical"></i>
	                                                </button>
	                                                <div class="dropdown-menu">
	                                                    <a class="dropdown-item" href="{{route('users.show',$user->email)}}">
	                                                        <i data-feather='eye'></i>
	                                                        <span class="ml-1">Détails</span>
	                                                    </a>
                                                        <a class="dropdown-item" href="{{route('users.edit',$user->email)}}">
                                                            <i data-feather="edit-2" class="mr-50"></i>
                                                            <span class="ml-1">Editer</span>
                                                        </a>
                                                        <span class="text-danger dropdown-item" data-toggle="modal" onclick="remplir('{{route('password.destroy',$user->id)}}',{{ Js::from(e($user->noms)) }}, ' Réinitialiser le mot de passe de ' ,'button_footer')" data-target="#danger">
                                                                <i data-feather='refresh-ccw'></i>
                                                                <span class="ml-1 text-danger" type="button">
                                                                    Réinitialiser
                                                                </span> 
                                                        </span>
                                                        <span class=" @if($user->statut == 1) text-danger @else text-success @endif dropdown-item" data-toggle="modal" onclick="remplir('{{route('users.destroy',$user->id)}}',{{ Js::from(e($user->noms)) }},@if($user->statut == 1) 'Désactiver' @else 'Activer' @endif ,'button_footer')" data-target="#danger">
                                                            @if($user->statut == 1)
                                                                <i data-feather='user-x'></i>
                                                                <span class="ml-1 text-danger" type="button">
                                                                    Désactiver
                                                                </span>
                                                            @else
                                                                <i data-feather='user-check'></i>
                                                                <span class="ml-1 text-success" type="button">
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
                    <div class="border-top mt-1">
                        <div class="d-flex col-md-10 mx-auto" style=" padding:1rem; overflow: auto; border-radius: 4px;">
                            <ul class="pagination"  style="margin-bottom:  0rem">
                            {{-- Previous Page Link --}}
                                @if ($users->onFirstPage())
                                    <li class="page-item" style="opacity: 0.6; cursor: no-drop;">
                                        <span disabled class="page-link">Précédent</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" onclick="mettre({{ $users->currentPage()-1 }})" rel="prev" aria-label="@lang('pagination.previous')">Précédent</a>
                                    </li>
                                @endif
                                @for($i=1;$i<=$users->lastPage();$i++)
                                    <li class="page-item @if($i==$users->currentPage()) active @endif" >
                                        <a class="page-link" onclick="@if($i!=$users->currentPage()) mettre({{$i}}) @endif" rel="prev" aria-label="@lang('pagination.previous')">{{$i}}</a>
                                    </li>
                                @endfor
                                {{-- Next Page Link --}}
                                @if ($users->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" onclick="mettre({{ $users->currentPage()+1 }})" rel="next" aria-label="@lang('pagination.next')">Suivant</a>
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
		document.querySelector('#UserListe')?.classList.add('active');
	</script>
@endsection