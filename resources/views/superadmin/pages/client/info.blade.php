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
                                                <div class="d-flex flex-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="calendar" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Date de fin</span>
                                                    </div>
                                                        @if($client->date_fin == null)
                                                            <span class="badge badge-pill badge-glow @if($client->date_fin < now() || $client->date_fin == null ) badge-danger @else badge-secondary @endif">
                                                                Pas de date
                                                            </span> 
                                                        @else 
                                                            <span data-toggle="tooltip" data-placement="right" title="" data-original-title=" @if($client->date_fin < now() ) En retard @else a jour @endif " class="badge badge-pill badge-glow @if($client->date_fin < now() || $client->date_fin == null ) badge-danger @else badge-secondary @endif">
                                                                {{Sa_Ladate($client->date_fin)}} à 
                                                                {{Sa_Heure($client->date_fin)}}
                                                            </span> 
                                                        @endif
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

            <section id="multiple-column-form">
                <h3 class="text-center mb-2"> 
                    Abonnements disponibles
                </h3>
                <div class="row">
                    @foreach($abonnements as $abonnement)
                        <div class="col-lg-4 col-sm-6 col-12 @if($abonnements->count() < 2) mx-auto @endif">
                            <div class="card">
                                <div class="card-header">
                                    <h2 class="mx-auto">
                                        <b>{{$abonnement->titre}}</b>
                                        @if($abonnement->id == $client->id_abonnement)
                                            <smal class="text-muted" style="font-size: 10px;">
                                               (Préféré)
                                            </smal>
                                        @endif
                                    </h2>
                                </div>
                                <div class="card-body">
                                    <div>
                                        <div class="row justify-content-between mx-1 mb-2">
                                            <div>
                                                    <b> Périodicité :</b>
                                            </div>
                                            <div>
                                                <span class="text-truncate">
                                                    {{$abonnement->accumulateur == 1 ? '1 '.str_replace('(s)','',$types_periode[$abonnement->type_periode]) : $abonnement->accumulateur.' '.str_replace('(s)','s',$types_periode[$abonnement->type_periode])}}
                                                    <span class="d-none d-md-inline-block">
                                                        {{$abonnement->periode_grace == 0 || $abonnement->periode_grace == null ? 'Sans période de grace' : ''}}
                                                        {{$abonnement->periode_grace == 1 ? '+ 01 '.'Jour (Grace)' : ''}}
                                                        {{$abonnement->periode_grace > 1 && $abonnement->periode_grace < 10 ? '+ 0'.$abonnement->periode_grace.' Jours (Grace)' : ''}}
                                                        {{$abonnement->periode_grace > 10 ? '+ '.$abonnement->periode_grace.' Jours (Grace)' : ''}}
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row justify-content-between mx-1 my-2">
                                            <div>
                                                    <b> Montant :</b>
                                            </div>
                                            <div>
                                                <span class="text-truncate">
                                                    {{Sa_montant($abonnement->montant)}} XAF
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button class="btn btn-success btn-gradient-success" href="#" data-target="#exampleModalCenter" data-toggle="modal" onclick="attribuate({{$abonnement->id}},{{$abonnement}})">
                                                    <i data-feather='shopping-cart' class="mr-50"></i> Attribuer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="modal fade show" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalCenterTitle" style="display: none; padding-right: 17px;" aria-modal="false" role="dialog">
                    <form id="formulaire" action="{{route('Sa-transaction.store')}}" method="POST" onsubmit="ajax(); return false;">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" >Modale d'enregistrement</h5>
                                    <button type="button" class="btn-danger close m-0" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="text-center">  
                                        <h4 id="abonnement_titre"></h4>    
                                        @csrf
                                        <input type="hidden" name="id_abonnement" id="id_abonnement">
                                        <input type="hidden" name="id_client" value="{{$client->id}}">
                                        <input type="hidden" name="methode" value="application">
                                        <div class="mx-auto mt-2">
                                            <div class="form-group">
                                                <label for="date_debut">Date de début</label>
                                                <span class="text-info cursor-pointer" title="Optionnel">Optionnel</span>
                                                <div class="form-group">
                                                    <input type="text" id="date_debut" class="text-center form-control flatpickr-date-time flatpickr-input @error('date_debut') is-invalid @enderror " name="date_debut" placeholder="Entrer une date" value="" />
                                                    @error('date_debut')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer"style="justify-content: space-around;">
                                    <button type="button" class="btn btn-danger " data-dismiss="modal">
                                        <i data-feather="x"></i>
                                    </button>
                                    <button type="submit" onclick="ajax();" class="btn btn-success" data-dismiss="modal">
                                        <i data-feather="check"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div>
                        </div>
                    </form>
                </div>
                            <div class="modal fade" id="recap" tabindex="-1" aria-labelledby="descriptionTitle" aria-modal="true" role="dialog">
                                <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document" id="modifiated">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="descriptionTitle">Boite de Confirmation</h5>
                                            <button type="button" class="bg-danger close m-0" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true" onclick="open_modale('exampleModalCenter')" class="text-white">×</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-11 mx-auto py-1" id="recapitulatif">
                                                    Le Récap s'affichera ici !!!
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer justify-content-center justify-content-md-apt-1">
                                            <div class="d-none d-lg-block text-center col-12 mx-auto">
                                                <button class="btn col-8 col-sm-7 col-lg-5  col-xl-3 col-md-8 btn-danger mt-1 mt-lg-2 mr-lg-4" data-dismiss="modal" aria-label="Close" onclick="open_modale('exampleModalCenter')">
                                                    <i class="mr-1" data-feather='x'></i> Annuler
                                                </button>
                                                    <button type="button" onclick="valider('formulaire')" data-toggle="modal" data-target="#setting" class="btn col-8 col-sm-7 col-lg-5 col-xl-3 col-md-8 btn-success mt-1 mt-lg-2">
                                                    <i class="mr-1" data-feather='check'></i> Confirmer
                                                </button>
                                            </div>
                                            <div class="d-block d-lg-none text-center col-12 mx-auto">
                                                <button type="button" onclick="valider('formulaire')" data-toggle="modal" data-target="#setting" class="btn col-8 col-sm-7 col-lg-5  col-xl-3 col-md-8 btn-success mt-1 mt-lg-2 mr-lg-2mx-auto">
                                                    <i class="mr-1" data-feather='check'></i> Confirmer
                                                </button>
                                                <button class="btn col-8 col-sm-7 col-lg-5 col-xl-3 col-md-8 btn-danger mt-1 mt-lg-2mx-auto" data-dismiss="modal" aria-label="Close" onclick="open_modale('exampleModalCenter')">
                                                    <i class="mr-1" data-feather='x'></i> Anuller
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
            </section>
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
        function ajax(){
            recap = recapituler('modal','recap');
            if(!recap){
                recap_ajax('recapitulatif',datas,'{{route('Sa-client.recap_abonate')}}','POST')
            } 
        }
        function open_modale(id){
            $('#'+id).modal()
        }
        function attribuate(id,abonnement){
            var id_abonnement = document.querySelector('#id_abonnement'),
                abonnement_titre = $('#abonnement_titre'),
                date_debut = document.querySelector('#date_debut');
            date_debut.value = '';
            abonnement_titre.html("");
            abonnement_titre.append('Abonnement '+abonnement.titre);
            id_abonnement.value = id;
        }
	</script>
@endsection