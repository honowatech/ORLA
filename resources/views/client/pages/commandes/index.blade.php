@extends('client/templates/template')
@section('title')
    {{'Toutes mes commande'}}
@endsection
@section('css')
<style type="text/css">
        td{
            padding-left: 2px !important;
            padding-right: 2px !important;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
</style>
@endsection
@section('contenu')
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Liste des commandes</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('home')}}l">Acceuil</a>
                                    </li>
                                    <li class="breadcrumb-item active"> Liste des commandes
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <div class="card pl-1 pr-1">

            <div class="content-body"> 
                <div class="row justify-content-between bg-light-secondary pt-50 pt-lg-0" >
                    <div class=" col-xl-2 col-lg-3 col-md-6 col-8 m-auto">
                        <a class="btn btn-dark col-12" style="margin-bottom:0px !important;" href="{{route('Clientcommandes.create')}}"><small>Nouvelle</small></a>
                    </div>
                        <form action="{{ route('Clientcommandes.index') }}" class="col-lg-9 col-xl-10 mt-1 row pr-0" id="search" method="GET">
                            @csrf
                            <div class="col-lg-4 col-sm-6 col-12 pr-0">
                                <div class="form-group">
                                    <label for="statut">Statut</label>
                                    <div class="form-group">
                                        <select class=" @error('statut') is-invalid @enderror hide-search form-control select2-hidden-accessible" id="statut" onchange="document.querySelector('#search').submit()" name="statut" />
                                            <option value="all">Tout afficher</option>
                                                @foreach($statuts as $statut)
                                                    <option value="{{$statut}}" {{$statut == session()->get('statut') ? 'selected' : ''}}>
                                                        {{$statut == 'attente' ? 'En attente' : ''}}
                                                        {{$statut == 'attribue' ? 'Attribuée' : ''}}
                                                        {{$statut == 'encours' ? 'En cours' : ''}}
                                                        {{$statut == 'livre' ? 'Livrée' : ''}}
                                                        {{$statut == 'annulee' ? 'Annulée' : ''}}
                                                        {{$statut == 'echoue' ? 'Echouée' : ''}}
                                                    </option>
                                                @endforeach
                                        </select>
                                    @error('mode_de_paiement') 
                                        <small class="alert alert-danger"> {{$message}} </small>
                                    @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-12 pr-0">
                                <div class="form-group">
                                    <div class="text-center mx-auto">
                                        <label>Le coursier récupère l'argent ? </label><span class="text-danger cursor-pointer" title="Obligatoire"> * </span>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group row col-md-10 col-lg-12 mx-auto justify-content-around">
                                            <div class="form-check custom-control custom-control-info custom-radio mt-1 mr-1">
                                                <input onchange="document.querySelector('#search').submit()" class="mode_paiement custom-control-input" type="radio" id="tout" name="mode_de_paiement" value="" {{ !session()->has('mode_de_paiement') || session()->get('mode_de_paiement')==null ? 'checked' : ''}}>
                                                <label class="custom-control-label" for="tout">
                                                    Oui et Non
                                                </label>
                                            </div>
                                        @foreach($modes_de_paiement as $mode_de_paiement)
                                            <div class="form-check custom-control custom-control-info custom-radio mt-1">
                                                <input onchange="document.querySelector('#search').submit()" class="mode_paiement custom-control-input" type="radio" name="mode_de_paiement" id="{{$mode_de_paiement}}" value="{{$mode_de_paiement}}" {{$mode_de_paiement == session()->get('mode_de_paiement') ? 'checked' : ''}}>
                                                <label class="custom-control-label" id="for{{$mode_de_paiement}}" for="{{$mode_de_paiement}}">
                                                {{$mode_de_paiement == 'speedex' ? 'Non' : ''}}
                                                {{$mode_de_paiement == 'coursier' ? 'Oui' : ''}}
                                                </label>
                                            </div>
                                        @endforeach
                                        </div>
                                    </div>
                                    @error('mode_de_paiement') 
                                        <small class="alert alert-danger"> {{$message}} </small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-12 pr-0 pr-lg-2">
                                <div class="form-group">
                                    <label for="mode_de_paiement">Rechercher</label>
                                    <div class="form-group">
                                        <div class="input-group input-group-merge">
                                            <div class="input-group-prepend ">
                                                <span class="input-group-text"><i data-feather='search'></i></span>
                                            </div>
                                            <input type="search" autofocus class="form-control" name="search" value="{{ session()->get('search')}}" placeholder="Rechercher un coursier">
                                        </div>
                                    </div>
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
                                        <th>Type Commande</th>
                                        <th>Téléphone</th>
                                        <th>adresse livraison</th>
                                        <th>Date de livraison</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	@php
                                	    $count = 20*$commandes->currentPage() -19 ;
                                	@endphp
                                	@foreach($commandes as $commande)
	                                    <tr class="text-center">
	                                        <td>
	                                        	{{$count}}
	                                        	@php $count++ @endphp
	                                        </td>
                                            <td>
                                                <span class="badge @if($commande->type_commande == 'simple') badge-secondary @else badge-dark @endif">
                                                    {{$commande->type_commande}}
                                                </span>
                                            </td>
	                                        <td>
                                                @php
                                                    $commande->id_client == null ? $telephone = $commande->telephone : $telephone = $commande->client->telephone;
                                                @endphp
                                                <span class="font-weight-bolder text-center @if($telephone == null)text-dark @endif">
                                                    @if($telephone == null)
                                                        <div class="spinner-grow spinner-grow-sm" role="status">
                                                        </div>
                                                    @else 
                                                        {{phone($telephone)}}
                                                    @endif
                                                </span>
	                                        </td>
	                                        <td>
	                                        		<span class="font-weight-bold" title="{{explode('*/*',$commande->adresse_livraison)[1]}}">
                                                        {{cutText(explode('*/*',$commande->adresse_livraison)[1],18)}} 
	                                        		</span>
	                                        </td>
                                            <td class="text-left">
                                                <span class="badge badge-glow badge-light-dark"> Le 
                                                    {{Ladate($commande->date_livraison)}}
                                                </span> à

                                                <span class="badge badge-glow badge-light-dark">
                                                    @if(isset(explode(' ',$commande->date_livraison)[1]) && explode(' ',$commande->date_livraison)[1] == '00:00:00')
                                                    Tout moment
                                                    @else
                                                        {{Heure($commande->date_livraison)}}
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-glow badge-{{explode('/',$contenu[$commande->statut])[0]}}">
                                                    <i class="mr-25" data-feather={{explode('/',$contenu[$commande->statut])[1]}}></i>
                                                        {{explode('/',$contenu[$commande->statut])[2]}}
                                                </span>
                                            </td>
	                                        <td>
	                                            <div>
	                                                <button type="button" style="margin-bottom: 0px !important;" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
	                                                    <i data-feather="more-vertical"></i>
	                                                </button>
	                                                <div class="dropdown-menu" id="accordion-hover">
	                                                    <a class="dropdown-item" href="{{route('Clientcommandes.show',$commande->id)}}">
	                                                        <i data-feather='eye'></i>
	                                                        <span class="ml-1">Détails</span>
	                                                    </a>
                                                    @if(!in_array($commande->statut , ['livre','annulee','echoue']))
                                                    @if($commande->statut != 'encours')
                                                        <a class="dropdown-item" href="{{route('Clientcommandes.edit',$commande->id)}}">
                                                            <i data-feather="edit-2" class="mr-50"></i>
                                                            <span class="ml-1">Modifier</span>
                                                        </a>
                                                    @endif
                                                        <div class="card m-0"  onmouseleave="document.querySelector('#collapse300{{$commande->id}}').classList.remove('show')">
                                                        @if($commande->statut != 'livre')
                                                            <section id="accordion-hover">
                                                                <div>
                                                                    <div>
                                                                        <div class="card collapse-icon m-0">
                                                                            <div class="card-body p-0">
                                                                                <div class="accordion" id="accordionExample3" data-toggle-hover="true">
                                                                                    <div class="collapse-default">
                                                                                        <div class="card m-0">
                                                                                            <span class="dropdown-item" id="modal_danger" data-toggle="modal" data-target="#change_statut" data-target="#collapse300{{$commande->id}}" aria-expanded="true" aria-controls="collapse300{{$commande->id}}" onclick="remplir_modale('contenu{{$commande->id}}')">
                                                                                                <i data-feather="rotate-ccw" class="mr-50"></i>
                                                                                                <span class=" collapse-hover-title" > Changer le statut </span>
                                                                                            </span>
                                                                                            @php
                                                                                                $commande->statut == 'attente' ? $depart = 1 : $statuts;
                                                                                                $commande->statut == 'attribue' ? $depart = 2 : $statuts;
                                                                                                $commande->statut == 'encours' ? $depart = 3 : $statuts;
                                                                                            @endphp
                                                                                            <div id="collapse300{{$commande->id}}" class="collapse" aria-labelledby="heading300{{$commande->id}}" data-parent="#accordionExample3">
                                                                                            <div class="card-body" id="contenu{{$commande->id}}">

                                                                                                <div class="" id="statut">
                                                                                                    @for($i=$depart;$i<count($statuts);$i++)
                                                                                                        @if($statuts[$i] == 'annulee')
                                                                                                        <span id="erreur{{$commande->id}}" class="dropdown-item cursor-pointer" class="cursor pointer"  data-toggle="modal" data-target="{{$statuts[$i] == 'attribue' ? '#choose_coursier' :'#danger'}}" onclick="put_statut('{{$statuts[$i]}}');
                                                                                                        @if($statuts[$i] !== 'attribue') 
                                                                                                         remplir('{{route('Clientcommandes.destroy',$commande->id)}}','', 'Changer le statut de la commande à {{$statuts[$i] == 'attente' ? 'En attente' : ''}}{{$statuts[$i] == 'attribue' ? 'Attribuée' : ''}}{{$statuts[$i] == 'encours' ? 'En cours' : ''}}{{$statuts[$i] == 'livre' ? 'Livrée' : ''}}{{$statuts[$i] == 'annulee' ? 'Annulée' : ''}}{{$statuts[$i] == 'echoue' ? 'Echouée' : ''}}','button_remove')"
                                                                                                        @endif>

                                                                                                            <i data-feather='chevron-right'></i>
                                                                                                            <span>
                                                                                                                {{$statuts[$i] == 'attente' ? ' En attente' : ''}}
                                                                                                                {{$statuts[$i] == 'attribue' ? 'Attribuée' : ''}}
                                                                                                                {{$statuts[$i] == 'encours' ? 'En cours' : ''}}
                                                                                                                {{$statuts[$i] == 'livre' ? 'Livrée' : ''}}
                                                                                                                {{$statuts[$i] == 'annulee' ? 'Annulée' : ''}}
                                                                                                                {{$statuts[$i] == 'echoue' ? 'Echouée' : ''}}
                                                                                                            </span>
                                                                                                        </span>
                                                                                                        @endif
                                                                                                    @endfor
                                                                                                </div>
                                                                                            </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </section>
                                                        @endif
                                                        </div>
                                                    @endif
                                                    </div>
	                                            </div>
	                                        </td>
	                                    </tr>
	                                @endforeach
                                </tbody>
                            </table>
                            @if($count==1)
                                <div class="w-100 text-center p-1">
                                           <i class="text-danger" data-feather='alert-triangle' style="width: 30px; height: 30px; margin-bottom: 0.5rem;"></i> <h5> Aucune information ...</h5>
                                </div>
                            @endif
<!-- debut modale de changement de statut -->
                            <div class="modal fade modal-danger text-left" id="change_statut" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-xs" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div></div>
                                            <h3 class="modal-title text-dark" id="myModalLabel120">Changer le satut</h3>
                                            <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close">
                                                <span class="text-dark" aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body" id="conteneur">
                                        </div>
                                        <div class="modal-footer" style="justify-content: space-around;">
                                        </div>
                                    </div>
                                </div>
                            </div>
<!-- fin de modale de changement de statut -->
                        </div>
                    </div>
                    <div class="border-top mt-1">
                        <div class="d-flex col-md-10 mx-auto" style=" padding:1rem; overflow: auto; border-radius: 4px;">
                            <ul class="pagination"  style="margin-bottom:  0rem">
                            {{-- Previous Page Link --}}
                                @if ($commandes->onFirstPage())
                                    <li class="page-item" style="opacity: 0.6; cursor: no-drop;">
                                        <span disabled class="page-link">Précédent</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" onclick="mettre({{ $commandes->currentPage()-1 }})" rel="prev" aria-label="@lang('pagination.previous')">Précédent</a>
                                    </li>
                                @endif
                                @for($i=1;$i<=$commandes->lastPage();$i++)
                                    <li class="page-item @if($i==$commandes->currentPage()) active @endif" >
                                        <a class="page-link" onclick="@if($i!=$commandes->currentPage()) mettre({{$i}}) @endif" rel="prev" aria-label="@lang('pagination.previous')">{{$i}}</a>
                                    </li>
                                @endfor
                                {{-- Next Page Link --}}
                                @if ($commandes->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" onclick="mettre({{ $commandes->currentPage()+1 }})" rel="next" aria-label="@lang('pagination.next')">Suivant</a>
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
    <!-- END: Content-->

<div class="modal fade modal-danger text-left" id="sfsdfs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div></div>
                <h3 class="modal-title text-dark" id="myModalLabel120">Confirmation! </h3>
                <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer" id="confirm_footer0" style="justify-content: space-around;">
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
<div id="button_remove" hidden>
    <button type="button" class="mx-auto btn btn-gradient-danger btn-danger" data-dismiss="modal"><i data-feather='x' style="width: 20px; height: 20px;"></i></button>
    <form method="POST" class="destroy_form m-0 p-0 mx-auto">
        @csrf
        @method('DELETE')
        <input type="hidden" class="statut" name="statut">
        <button type="submit" class="btn btn-gradient-success btn-success"><i data-feather='check' style="width: 20px; height: 20px;"></i></button>
    </form>
</div>
@endsection
@section('javascript')
<script src="{{asset('app-assets/js/scripts/components/components-collapse.js')}}"></script>
	<script type="text/javascript">
        cliquer = 0;
        function  put_statut(id){
            var status = document.querySelectorAll('.statut');
            for (i = 0; i < status.length; i++) {
                status[i].value = id;
            }
        }
        function mettre(number_page){
           document.querySelector('#page').value = number_page;
           document.querySelector('#search').submit() 
        }
		document.querySelector('#Commandes')?.classList.add('active');
        function remplir_modale(id_contenu){
            document.querySelector('#conteneur').innerHTML = document.querySelector('#'+id_contenu).innerHTML;
        }
        @error('id_coursier')
            document.querySelector('#erreur{{session()->get('id_commande')}}').click();
        @enderror
	</script>
@endsection