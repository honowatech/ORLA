@extends('client/templates/template')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/dashboard-ecommerce.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/menu/menu-types/horizontal-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/app-ecommerce.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/form-wizard.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/extensions/ext-component-toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/form-number-input.css')}}">
@endsection
@section('title')
    {{'Acceuil'}}
@endsection
@section('contenu')

    <!-- BEGIN: Content-->
    <div class="app-content content ecommerce-application">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-body">
                <div class="bs-stepper checkout-tab-steps">
                    <h2 class="text-center">Livraisons En attente</h2>
                    <div class="bs-stepper-content">
                        <!-- Checkout Place order starts -->
                            <div id="place-order" class="list-view col-12 col-md-8 mx-auto p-0">
                                <!-- Checkout Place Order Left starts -->
                                <div class="checkout-items">
                                    @if($commandes->count() == 0)
                                        <div class="card p-2">
                                            <div class="item-img">
                                                <h2 class="text-center">AUCUNE COMMANDE !</h2>
                                            </div>
                                        </div>
                                    @endif
                                    @foreach($commandes as $commande)
                                            <div class="card ecommerce-card">
                                                <div class="item-img">
                                                    <a href="{{route('Clientcommandes.show',$commande->id)}}" class="h-100 w-100 d-flex">
                                                        <i data-feather='shopping-bag' class="text-dark font-weight-bold m-auto" style="width: 4rem !important; height: 7rem !important;"></i>
                                                    </a>
                                                </div>
                                                <div class="card-body text-center">
                                                    <div class="item-name">
                                                        @if(isset($commande->coursier))
                                                            <h6 class="mb-0">
                                                                {{$commande->coursier->noms}} {{$commande->coursier->Prenoms}}
                                                            <span class="item-company">( Coursier )</span>
                                                            </h6>
                                                            <button class="text-center font-weight-bolder cursor-pointer btn btn-sm btn-dark mx-auto" style="width: fit-content;" onclick="copy({{ Js::from(e(phone2(explode('*/*',$commande->adresse_colis)[0]))) }})">
                                                                {{phone(explode('*/*',$commande->adresse_colis)[0])}}<i data-feather='copy' class="ml-50"></i>
                                                            </button>
                                                        @else
                                                            <span class="item-company">Cette Commande n'est pas encore attribuée</span>
                                                        @endif
                                                        <div class=" mx-auto">
                                                            Frais :  <span class="font-weight-bolder">{{formatMontant($commande->montant_livraison)}} XAF</span>
                                                        </div>
                                                    </div>
                                                    <span class="text-dark font-weight-bolder mb-1">
                                                        <span title="{{$commande->quartier_colis->libelle}}">{{$commande->quartier_colis->libelle}}</span>
                                                        -
                                                        <span title="{{$commande->quartier_livraison->libelle}}">{{$commande->quartier_livraison->libelle}}</span>
                                                    </span>
                                                    <span class="font-weight-bolder">
                                                        <i data-feather='at-sign' class="mr-50"></i>
                                                        @php
                                                            $nom_destinataire = isset(explode('*/*',$commande->adresse_livraison)[3]) ? explode('*/*',$commande->adresse_livraison)[3] : 'Pas de Nom';
                                                        @endphp
                                                        {{name($nom_destinataire)}}
                                                    </span>
                                                    <button class="text-center font-weight-bolder cursor-pointer btn btn-sm btn-dark mx-auto" style="width: fit-content;" onclick="copy({{ Js::from(e(phone2(explode('*/*',$commande->adresse_colis)[0]))) }})">
                                                        {{phone(explode('*/*',$commande->adresse_colis)[0])}}<i data-feather='copy' class="ml-50"></i>
                                                    </button>
                                                    <div class=" mx-auto">
                                                        <span class="quantity-title text-underline font-weight-bolder">Description :</span>
                                                        <br>
                                                        <span class="text-left input-group quantity-counter-wrapper" title="{!! nl2br(e($commande->description)) !!}">
                                                            {!! nl2br(e($commande->description)) !!}
                                                        </span>
                                                    </div>
                                                    <span class="delivery-date">
                                                        Prévu pour le 
                                                        <span class="font-weight-bold text-dark">
                                                            {{Ladate($commande->date_livraison)}}
                                                        </span> à 
                                                        <span class="font-weight-bold text-dark">
                                                            {{Heure($commande->date_livraison) == '00h00min' ? 'Tout moment' : Heure($commande->date_livraison)}}
                                                        </span>
                                                        @if(Heure($commande->date_livraison) != '00h00min' )
                                                            @php
                                                                $new_date = strtotime(date_format(now(),'Y-m-d h:i'));;
                                                            @endphp
                                                        @else
                                                            @php
                                                                $new_date = strtotime(date_format(now(),'Y-m-d').' 00:00');
                                                            @endphp
                                                        @endif
                                                            @if($commande->statut != 'livre' && strtotime($commande->date_livraison) >= $new_date)
                                                            <span title="Le date n'est pas encore arrivée">
                                                                <i data-feather='clock' class="text-success font-weight-bold"></i>
                                                            </span>
                                                            @elseif($commande->statut != 'livre' && strtotime($commande->date_livraison) < $new_date)
                                                            <span title="Délai dépassé">
                                                                <i data-feather='clock' class="text-danger font-weight-bold"></i> 
                                                            </span>
                                                            @endif
                                                    </span>
                                                    <small class="text-muted">Enregistrée par {{in_array($commande->enregistreur->type_utilisateur->libelle,['Agent','Super Admin']) ? 'Speedex' : 'Client'}}</small>
                                                </div>
                                                <div class="item-options text-center">
                                                    @if($commande->disponibility != 1)
                                                    <div class="badge badge-pill badge-glow badge-danger">Client Indisponible</div>
                                                    @else
                                                    <div class="badge badge-pill badge-glow badge-success">Client disponible</div>
                                                    @endif
                                                    <div class="item-wrapper">
                                                        <div class="item-cost mb-1">
                                                            <small class="pb-1 text-muted">Montant à récupérer</small>
                                                            <h5 class="font-weight-bolder">{{formatMontant($commande->montant_recuperer)}} XAF</h5>
                                                            <p class="card-text shipping">
                                                                @if($commande->statut == 'attribue')
                                                                    <div class="progress progress-bar-info">
                                                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100" style="width: {{$commande->statut == 'attribue' ? '35%' : ''}}"></div>
                                                                    </div>
                                                                    <span class="badge badge-pill badge-light-info m-1">
                                                                        {{$commande->statut == 'attribue' ? 'Attribué' : ''}}
                                                                    </span>
                                                                @elseif($commande->statut == 'attente')
                                                                    <div class="progress progress-bar-info">
                                                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100" style="width: {{$commande->statut == 'attente' ? '0%' : ''}}"></div>
                                                                    </div>
                                                                    <span class="badge badge-pill badge-light-info m-1">
                                                                        {{$commande->statut == 'attente' ? 'En attente' : ''}}
                                                                    </span>
                                                                @else
                                                                    <div class="progress progress-bar-dark">
                                                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100" style="width: {{$commande->statut == 'encours' ? '75%' : ''}}"></div>
                                                                    </div>
                                                                    <span class="badge badge-pill badge-light-dark m-1">
                                                                        {{$commande->statut == 'encours' ? 'En cours' : ''}}
                                                                    </span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <button type="button" data-toggle="modal" data-target="#danger" class="btn btn-danger btn-gradient-danger btn-sm mt-1 text-truncate" style="margin:0.5rem 0rem !important;" onclick="put_statut('annulee'); remplir('{{route('Clientcommandes.destroy',$commande->id)}}','', 'Changer le statut de la commande ','button_remove')">
                                                        <i data-feather="x" class="mr-50"></i>
                                                        Annuler la livraison
                                                    </button>
                                                    <hr class="w-100">
                                                    @if ($commande->disponibility == 1)
                                                        <a type="button" data-toggle="modal" data-target="#danger" class="btn round btn-danger btn-gradient-danger btn-sm text-truncate" style="margin:0.5rem 0rem !important;" onclick="put_statut('annulee'); remplir('{{route('Clientusers.destroy',$commande->id)}}','', 'Rendre cette Commande Indisponible ','button_remove')">
                                                            <i data-feather="phone-off" class="mr-50"></i>
                                                            <span>Se rendre Indisponible</span>
                                                        </a> 
                                                    @else
                                                        <a type="button" data-toggle="modal" data-target="#danger" class="btn round btn-success btn-gradient-success btn-sm text-truncate" style="margin:0.5rem 0rem !important;" onclick="put_statut('annulee'); remplir('{{route('Clientusers.destroy',$commande->id)}}','', 'Rendre cette Commande Disponible ','button_remove')">
                                                            <i data-feather="phone" class="mr-50"></i>
                                                            <span>Rendre Disponible</span>
                                                        </a>
                                                    @endif
                                                    <span id="tospin_{{$commande->id}}"></span>
                                                </div>
                                            </div>
                                    @endforeach
                                </div>
                                <!-- Checkout Place Order Left ends -->
                            </div>
                        </div>
                        <!-- Checkout Place order Ends -->
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- END: Content-->
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
    <script src="{{asset('app-assets/vendors/js/ui/jquery.sticky.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/forms/wizard/bs-stepper.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/forms/spinner/jquery.bootstrap-touchspin.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
    <script src="{{asset('app-assets/js/scripts/pages/app-ecommerce-checkout.js')}}"></script>
	<script type="text/javascript">
        cliquer = 0;
        function  put_statut(id){
            var status = document.querySelectorAll('.statut');
            for (i = 0; i < status.length; i++) {
                status[i].value = id;
            }
        }
		document.querySelector('#Acceuil').classList.add('active');
	</script>
@endsection