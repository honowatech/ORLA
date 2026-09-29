@extends('superadmin/layout/template')
@section('menu')
    @include('superadmin/menu/menu')
@endsection
@section('title')
{{'Détails sur '}} {{Sa_name($client->name)}}
@endsection
@section('css')
    <style type="text/css">
        .user_feather{
            height: 90px;
            width: 90px;
        }
    </style>
@endsection
@section('contenu')

    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
            <div class="content-wrapper">
                <div class="content-header row">
    	           <div class="content-header-left col-md-12 col-12 mb-2">
                        <div class="row breadcrumbs-top">
                            <div class="col-12">
                                <h2 class="content-header-title float-left mb-0">Détails de {{Sa_name($client->name)}}</h2>
                                <div class="breadcrumb-wrapper">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{route('SuperAdmin.home')}}">Acceuil</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="{{route('Sa-client.index')}}">
                                                Liste des Clients 
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item active">Détails de {{Sa_name($client->name)}}
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <section class="app-user-view">
                    <!-- User Card & Plan Starts -->
                    <div class="row">
                        <!-- User Card starts-->
                        <div class="col-md-12">
                            <div class="card user-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-bg-6 col-lg-6 d-flex flex-column justify-content-between border-container-lg">
                                            <div class="user-avatar-section">
                                                <div class="d-flex justify-content-start mb-1">
                                                    <div class="m-auto">
                                                        <i data-feather="user" class="user_feather mr-1"></i>
                                                    </div>
                                                    <div class="d-flex flex-column ml-1 m-auto">
                                                        <div class="user-info mb-1">
                                                            <h4 class="mb-0">{{Sa_name($client->name)}}</h4>
                                                            <span class="card-text" title="{{$client->adresse}}">
                                                                {{$client->adresse}}
                                                            </span>
                                                        </div>
                                                        <div class="row" style="gap: 1rem;">
                                                            <a href="{{route('Sa-client.edit',$client->id)}}" class=" btn btn-secondary btn-gradient-secondary btn-sm ml-1">
                                                                <i data-feather="edit-2" class="mr-50"></i>
                                                                Modifier
                                                            </a>
                                                            <button class="btn ml-1 btn-sm @if($client->statut == 1)  btn-gradient-danger btn-danger  @else btn-gradient-success btn-success @endif "  data-toggle="modal" onclick="remplir('{{route('Sa-client.destroy',$client->id)}}',{{ Js::from(e(Sa_name($client->name))) }},@if($client->statut == 1) 'Désactiver' @else 'Activer' @endif ,'button_footer')" data-target="#danger">
                                                                @if($client->statut == 1)
                                                                    <i data-feather="x" class="mr-50"></i>
                                                                    <span>
                                                                        Désactiver
                                                                    </span>
                                                                @else
                                                                    <i data-feather="check" class="mr-50"></i>
                                                                    <span>
                                                                        Activer
                                                                    </span>
                                                                @endif
                                                            </button>
                                                            {{-- <button class="btn ml-1  btn-sm btn-outline-danger" data-toggle="modal" onclick="remplir('{{route('Sa-client.destroy',$client->id)}}',{{ Js::from(e(Sa_name($client->name))) }}, 'supprimer' ,'button_delete_footer')" data-target="#danger">
                                                                    <i data-feather="trash" class="mr-50"></i>
                                                                    Supprimer
                                                            </button> --}} 
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-bg-6 col-lg-6">
                                            <div class="user-info-wrapper">
                                                <div class="d-flex flex-wrap d-sm-block d-sm-flex  d-none">
                                                    <div class="user-info-title">
                                                        <i data-feather="user" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Noms</span>
                                                    </div>
                                                    <p class="card-text mb-0">
                                                        <b>
                                                            {{Sa_name($client->name)}}
                                                        </b>
                                                    </p>
                                                </div>
                                                <div class="d-flex flex-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="credit-card" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">CNI</span>
                                                    </div>
                                                    <p class="card-text mb-0">
                                                        <b>
                                                            {{$client->cni}}
                                                        </b>
                                                    </p>
                                                </div>
                                                <div class="d-flex flex-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="phone" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Contact</span>
                                                    </div>
                                                    <p class="card-text mb-0">
                                                        <span>
                                                            {{Sa_phone2($client->telephone)}}
                                                        </span>
                                                    </p>
                                                </div>
                                                @if($client->telephone_secondaire != null)
                                                <div class="d-flex flex-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="phone" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Contact 2</span>
                                                    </div>
                                                    <p class="card-text mb-0">
                                                        <span>
                                                            {{Sa_phone2($client->telephone_secondaire)}}
                                                        </span>
                                                    </p>
                                                </div>
                                                @endif
                                                <div class="d-flex flex-wrap my-50">
                                                    <div class="user-info-title">
                                                         @if($client->statut == 0)
                                                            <i data-feather="x-circle" class="mr-1"></i>
                                                        @else 
                                                            <i data-feather="check-circle" class="mr-1"></i> 
                                                        @endif
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Status</span>
                                                    </div>
                                                    <span class="badge badge-pill badge-glow @if($client->statut == 0)badge-danger @else badge-success @endif">
	                                            		@if($client->statut == 0)
			                                             	Désactivé
			                                            @else 
			                                            	Actif
			                                            @endif
                                                	</span> 
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- zone Timeline & Permissions Starts -->
                    <div class="row">

                        <!-- zone Permissions Starts -->
                        <div class="col-md-12">
                            <!-- zone Permissions -->
                            <div class="card">
                                <div class="card-header mx-1 row" style="gap: 2rem;">
                                    <h4 class=" card-title">Tableau des Transactions</h4>
                                </div>
                                <div class="card-body">
                                <form action="{{ route('Sa-client.show',$client->id) }}" class="col-lg-3 mt-1" id="search" method="GET">
                                    @csrf
                                    <input type="hidden" name="page" id="page" value="">
                                </form> 
                                    <div id="table">
                                    </div>
                                </div>
                             </div>
                            <!-- fin modale de sélection des viles -->
                        </div>
                    </div>
                        <!-- zone Permissions Ends -->
                </div>
                    <!-- zone Timeline & Permissions Ends -->
            </section>
        </div>
    </div>
</div>
    <!-- END: Content-->
<div class="modal fade" id="description" tabindex="-1" aria-labelledby="descriptionTitle" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="descriptionTitle"> Description </h5>
                <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-10 mx-auto py-2" id="info_description">
                        Infos de chaque paiement ici !
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center justify-content-md-apt-1">
                <button data-dismiss="modal" class="btn px-2 btn-success btn-gradient-success mx-auto">
                    <i data-feather="check"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<div id="button_footer" hidden>
    <button type="button" class="mx-auto btn btn-gradient-danger btn-danger" data-dismiss="modal">
            <i data-feather="x"></i>
    </button>
    <form method="POST" class="destroy_form m-0 p-0 mx-auto">
        @csrf
        @method('DELETE')
            <button type="submit" class="btn btn-gradient-success btn-success">
                <i data-feather="check"></i>
            </button>
    </form>
</div>
@endsection
@section('javascript')
	<script type="text/javascript">
            document.querySelector('#client-index').classList.add('active');
	</script>
@endsection