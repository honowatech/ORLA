@extends('coursier/templates/template')
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
    {{'Coursier Acceuil'}}
@endsection
@section('contenu')

    <!-- BEGIN: Content-->
    <div class="app-content content ecommerce-application">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-body">
                <div class="bs-stepper checkout-tab-steps">
                    <h2 class="text-center">Livraisons de la journée</h2>
                    <div class="bs-stepper-content">
                        <!-- Checkout Place order starts -->
                        <div id="step-cart" class="content">
                        @if($activities->count() > 0)
                            <div id="place-order" class="list-view product-checkout">
                        @else
                            <div id="place-order" class="list-view col-12 col-md-8 mx-auto p-0">
                        @endif
                                <!-- Checkout Place Order Left starts -->
                                <div class="checkout-items">
                                    @if($commandes->count() == 0 && $commandes2->count() == 0)
                                        <div class="card p-2">
                                            <div class="item-img">
                                                <h2 class="text-center">AUCUNE COMMANDE !</h2>
                                            </div>
                                        </div>
                                    @endif
                                    @foreach($commandes as $commande)
                                        @if(in_array($commande->statut,['attribue','encours']))
                                            <div class="card ecommerce-card">
                                                <div class="item-img">
                                                    <a href="{{route('Coursiercommandes.show',$commande->id)}}" class="h-100 w-100 d-flex">
                                                        @if($commande->statut == 'attribue')
                                                            <div class="spinner-grow text-info m-auto" role="status" style="width: 3rem !important; height: 3rem !important;">
                                                                <span class="sr-only"></span>
                                                           </div>
                                                        @else
                                                            <div class="spinner-grow text-dark m-auto" role="status" style="width: 3rem !important; height: 3rem !important;">
                                                                <span class="sr-only"></span>
                                                           </div>
                                                        @endif 
                                                    </a>
                                                </div>
                                                <div class="card-body text-center">
                                                    <div class="item-name">
                                                        <h6 class="mb-0">
                                                            {{$commande->client->noms}} {{$commande->client->Prenoms}}
                                                        </h6>
                                                        <span class="item-company">( {{$commande->client->type__client->libelle}} )</span>
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
                                                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100" style="width: {{$commande->statut == 'attribue' ? '35%' : '75%'}}"></div>
                                                                    </div>
                                                                    <span class="badge badge-pill badge-light-info m-1">
                                                                        {{$commande->statut == 'attribue' ? 'Attribué' : ''}}
                                                                    </span>
                                                                @else
                                                                    <div class="progress progress-bar-dark">
                                                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100" style="width: {{$commande->statut == 'attribue' ? '25%' : '75%'}}"></div>
                                                                    </div>
                                                                    <span class="badge badge-pill badge-light-dark m-1">
                                                                        {{$commande->statut == 'encours' ? 'En cours' : ''}}
                                                                    </span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                    @if($commande->statut == 'attribue' && $commande->disponibility == 1)
                                                    <button type="button" data-toggle="modal" data-target="#danger" class="btn btn-secondary btn-gradient-secondary btn-sm mt-1 text-truncate" style="margin:0.5rem 0rem !important;" onclick="put_statut('encours'); remplir('{{route('Coursiercommandes.destroy',$commande->id)}}','', 'Changer le statut de la commande ','button_remove')">
                                                        <i data-feather="zap" class="mr-50"></i>
                                                        Débuter la livraison
                                                    </button>
                                                    @endif
                                                    @if($commande->statut == 'encours' && $commande->disponibility == 1)
                                                    <button type="button" data-toggle="modal" data-target="#danger" class="mx-auto btn btn-success btn-gradient-success btn-sm mt-1 text-truncate" onclick="put_statut('livre'); remplir('{{route('Coursiercommandes.destroy',$commande->id)}}','', 'Changer le statut de la commande ','button_remove')" style="margin:0.5rem 0rem !important;">
                                                        <i data-feather="check" class="mr-50"></i>
                                                        Terminer la livraison
                                                    </button>
                                                    @endif
                                                    <button type="button" data-toggle="modal" data-target="#danger" class="btn btn-danger btn-gradient-danger btn-sm mt-1 text-truncate" style="margin:0.5rem 0rem !important;" onclick="put_statut('annulee'); remplir('{{route('Coursiercommandes.destroy',$commande->id)}}','', 'Changer le statut de la commande ','button_remove')">
                                                        <i data-feather="x" class="mr-50"></i>
                                                        Annuler la livraison
                                                    </button>
                                                    <hr class="w-100">
                                                    @if ($commande->disponibility == 1)
                                                        <a type="button" data-toggle="modal" data-target="#danger" class="btn round btn-danger btn-gradient-danger btn-sm text-truncate" style="margin:0.5rem 0rem !important;" onclick="put_statut('annulee'); remplir('{{route('Coursierusers.destroy',$commande->id)}}','', 'Rendre cette Commande Indisponible ','button_remove')">
                                                            <i data-feather="phone-off" class="mr-50"></i>
                                                            <span>Rendre Indisponible</span>
                                                        </a> 
                                                    @else
                                                        <a type="button" data-toggle="modal" data-target="#danger" class="btn round btn-success btn-gradient-success btn-sm text-truncate" style="margin:0.5rem 0rem !important;" onclick="put_statut('annulee'); remplir('{{route('Coursierusers.destroy',$commande->id)}}','', 'Rendre cette Commande Disponible ','button_remove')">
                                                            <i data-feather="phone" class="mr-50"></i>
                                                            <span>Rendre Disponible</span>
                                                        </a>
                                                    @endif
                                                    <span id="tospin_{{$commande->id}}"></span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                    @foreach($commandes2 as $commande)
                                        @if(!in_array($commande->statut,['attribue','encours']))
                                            <div class="card ecommerce-card">
                                                <div class="item-img">
                                                    @if($commande->statut == 'livre')
                                                        <a href="{{route('Coursiercommandes.show',$commande->id)}}" class="h-100 w-100 d-flex">
                                                            <div class="rounded-pill bg-success text-success m-auto" role="status" style="width: 5rem !important; height: 5rem !important;">
                                                           </div> 
                                                        </a>
                                                    @else
                                                        <a href="{{route('Coursiercommandes.show',$commande->id)}}" class="h-100 w-100 d-flex">
                                                            <div class="rounded-pill bg-danger text-danger m-auto" role="status" style="width: 5rem !important; height: 5rem !important;">
                                                           </div> 
                                                        </a>
                                                    @endif
                                                </div>
                                                <div class="card-body text-center">
                                                    <div class="item-name">
                                                        <h6 class="mb-0">
                                                            {{$commande->client->noms}} {{$commande->client->Prenoms}}
                                                        </h6>
                                                        <span class="item-company">( {{$commande->client->type__client->libelle}} )</span>
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
                                                        <span class="text-left input-group quantity-counter-wrapper" title="{{str_replace('<br>','',$commande->description)}}">
                                                            {!! nl2br(e($commande->description)) !!}
                                                        </span>
                                                    </div>
                                                    <span class="delivery-date">
                                                        {{$commande->statut == 'annulee' ? 'Annulé' : ''}}
                                                        {{$commande->statut == 'livre' ? 'Livré' : ''}}
                                                        {{$commande->statut == 'echoue' ? 'Echoué' : ''}}
                                                        le {{Ladate($commande->date_livre)}} à {{Heure($commande->date_livre) == '00h00min' ? 'Tout moment' : Heure($commande->date_livre)}}
                                                        @if(Heure($commande->date_livre) != '00h00min' )
                                                            @php
                                                                $new_date = strtotime($commande->date_livre);
                                                            @endphp
                                                        @else
                                                            @php
                                                                $new_date = strtotime(explode(' ', $commande->date_livre)[0].' 00:00');
                                                            @endphp
                                                        @endif
                                                            @if($commande->statut == 'livre' && strtotime($commande->date_livraison) >= $new_date)
                                                            <span title="A été livré à temps">
                                                                <i data-feather='clock' class="text-success"></i>
                                                            </span>
                                                            @elseif($commande->statut == 'livre' && strtotime($commande->date_livraison) < $new_date)
                                                            <span title="A été livré en retard">
                                                                <i data-feather='clock' class="text-danger"></i> 
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
                                                                @if($commande->statut == 'livre')
                                                                <div class="progress progress-bar-success">
                                                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100" style="width: 100%;"></div>
                                                                </div>
                                                                <span class="badge badge-pill badge-light-success m-1">
                                                                    {{$commande->statut == 'livre' ? 'Livré' : ''}}
                                                                </span>
                                                                @else
                                                                <div class="progress progress-bar-danger">
                                                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="40" aria-valuemin="40" aria-valuemax="100" style="width: 100%;"></div>
                                                                </div>
                                                                <span class="badge badge-pill badge-light-danger m-1">
                                                                    {{$commande->statut == 'annulee' ? 'Annulée' : ''}}
                                                                    {{$commande->statut == 'echoue' ? 'Echouée' : ''}}
                                                                </span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <!-- Checkout Place Order Left ends -->

                                <!-- Checkout Place Order Right starts -->
                                @if($activities->count() > 0)
                                    <div class="checkout-options">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title">Activité journalière</h4>
                                            </div>
                                            <div class="card-body">
                                                <ul class="timeline">
                                                    @foreach($activities as $activity)
                                                        <li class="timeline-item">
                                                            <span class="timeline-point timeline-point-indicator timeline-point-{{$activity->color}}"></span>
                                                            <div class="timeline-event">
                                                                <div class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                                                                    <h6>
                                                                        {{-- <img class="mr-1" src="{{asset('app-assets/images/avatars/user1.png')}}" alt="invoice" height="23"> --}}
                                                                        <span class="pr-25">
                                                                            <i data-feather='shopping-cart' alt="invoice" height="23"></i>
                                                                        </span>
                                                                        {{$activity->title}}
                                                                    </h6>
                                                                    <span class="timeline-event-time temps" data-date="{{strtotime($activity->jour.' '.$activity->heure)}}"></span>
                                                                </div>
                                                                <p>{!!$activity->message!!}.</p>
                                                                <div class="media align-items-center">
                                                                    <a class="media-body" href="{{$activity->lien}}">{{$activity->texte_lien}}</a>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <!-- Checkout Place Order Right ends -->
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
        @if($activities->count() > 0)
            function tempsEcoule(date) {
                  const dateSoumise = new Date((date * 1000));
                  const dateActuelle = new Date();
                  const intervalle = dateActuelle - dateSoumise;
                  const annees = Math.floor(intervalle / (365 * 24 * 60 * 60 * 1000));
                  const mois = Math.floor((intervalle % (365 * 24 * 60 * 60 * 1000)) / (30 * 24 * 60 * 60 * 1000));
                  const jours = Math.floor((intervalle % (30 * 24 * 60 * 60 * 1000)) / (24 * 60 * 60 * 1000));
                  const heures = Math.floor((intervalle % (24 * 60 * 60 * 1000)) / (60 * 60 * 1000));
                  const minutes = Math.floor((intervalle % (60 * 60 * 1000)) / (60 * 1000));
                  const secondes = Math.floor((intervalle % (60 * 1000)) / 1000);
                  // console.log(dateActuelle)
                  // console.log(dateSoumise)
                  if (secondes != 0 && minutes == 0 && heures == 0 && jours == 0 && mois == 0 && annees == 0) {
                    return `Maintenant`;
                  } else if (minutes != 0 && heures == 0 && jours == 0 && mois == 0 && annees == 0) {
                    return `il y a ${minutes} Minutes`;
                  } else if (heures != 0 && jours == 0 && mois == 0 && annees == 0) {
                    return `il y a ${heures} Heures`;
                  } else if (jours != 0 && mois == 0 && annees == 0) {
                    return `il y a ${jours} Jours`;
                  } else if (mois != 0 && annees == 0) {
                    return `il y a ${mois} Mois`;
                  } else {
                    return `il y a ${annees} Années`;
                  }
            }
                var options = document.querySelectorAll('.temps');
                options.forEach((option) => {
                    temps = tempsEcoule(option.dataset.date);
                    if (temps != option.innerHTML) {
                        option.innerHTML = temps;
                    }
                });
            setInterval(function(){
                var options = document.querySelectorAll('.temps');
                options.forEach((option) => {
                    temps = tempsEcoule(option.dataset.date);
                    if (temps != option.innerHTML) {
                        option.innerHTML = temps;
                    }
                });
            },10000)
        @endif
	</script>
@endsection