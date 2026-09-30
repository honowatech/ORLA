@extends('coursier/templates/template')


@section('title')
{{'Détails sur La commande '}}
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
                                <h2 class="content-header-title float-left mb-0">Infos sur une commande</h2>
                                <div class="breadcrumb-wrapper">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                                        </li>
                                        <li class="breadcrumb-item active">Infos sur une commande
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
                    <!-- infos direct de la commande -->
                    <div class=" px-2 pt-2 col-12">
                        <div class="row">
                            <div class="col-md-8 card mx-auto">
                                <div class="card-header">
                                    <h4 class="card-title">Détails sur la commande <small class="text-muted ml-md-1">( {{ucfirst($commande->type_commande)}} )</small></h4>
                                </div>
                                <div class="card user-card">
                                    <div class="card-body px-0">
                                        <div class="row">
                                            @if($commande->disponibility != 1)
                                                <div class="badge badge-pill badge-glow badge-danger mx-auto">Client Indisponible</div>
                                            @else
                                                <div class="badge badge-pill badge-glow badge-success mx-auto">Client disponible</div>
                                            @endif
                                        </div>
                                        <div class="row">
                                            <div class="col-6 col-md-7 mx-auto"><i data-feather='chevron-right' class="mr-1"></i> statut</div>
                                            <div class="col-6 col-md-4 mx-auto">
                                                <div>
                                                    <span class="badge w-100 px-1 badge-glow badge-{{explode('/',$contenu[$commande->statut])[0]}}">
                                                        <i class="mr-25" data-feather='{{explode('/',$contenu[$commande->statut])[1]}}'></i>
                                                            {{explode('/',$contenu[$commande->statut])[2]}}
                                                    </span>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-6 col-md-7 mx-auto"><i data-feather='chevron-right' class="mr-1"></i>
                                                Date prévue pour la Livraison
                                            </div>
                                            <div class="col-6 col-md-4 mx-auto">
                                                <div class="w-100 px-1 badge badge-glow badge-primary">
                                                    <span> Le 
                                                        {{Ladate($commande->date_livraison)}}
                                                    </span> à
                                                    <span>
                                                        @if(isset(explode(' ',$commande->date_livraison)[1]) && explode(' ',$commande->date_livraison)[1] == '00:00:00')
                                                        Tout moment
                                                        @else
                                                            {{Heure($commande->date_livraison)}}
                                                        @endif
                                                    </span>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <hr>
                                        @if(in_array($commande->statut,['livre','annulee','supprime','echoue']))
                                        <div class="row">
                                            <div class="col-6 col-md-7 mx-auto"><i data-feather='chevron-right' class="mr-1"></i>
                                            @if($commande->statut == 'livre')
                                                Date de Livraison
                                            @elseif($commande->statut == 'annulee')
                                                Date d'Annulation
                                            @elseif($commande->statut == 'supprime')
                                                Date de Suppression
                                            @elseif($commande->statut == 'echoue')
                                                Date d' Echec
                                            @endif
                                            </div>
                                            <div class="col-6 col-md-4 mx-auto">
                                                <div class="w-100 px-1 badge badge-glow badge-dark">
                                                    <span> Le 
                                                        {{Ladate($commande->date_livre)}}
                                                    </span> à

                                                    <span>
                                                        @if(isset(explode(' ',$commande->date_livre)[1]) && explode(' ',$commande->date_livre)[1] == '00:00:00')
                                                        Tout moment
                                                        @else
                                                            {{Heure($commande->date_livre)}}
                                                        @endif
                                                    </span>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <hr>
                                        @endif
                                        <div class="row">
                                            <div class="col-6 col-md-7 mx-auto"><i data-feather='chevron-right' class="mr-1"></i> Montant à récupérer </div>
                                            <div class="col-6 col-md-4 m-auto">
                                                <div>
                                                    <h6 class="w-100 px-1 text-center m-0">
                                                        {{$commande->montant_recuperer == null ? 0 : $commande->montant_recuperer}} FCFA
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    @if($commande->id_coursier != null)
                                        <div class="row">
                                            <div class="col-6 col-md-7 mx-auto"><i data-feather='chevron-right' class="mr-1"></i> Coursier <span class="d-md-inline-block d-none "> en charge </span></div>
                                            <div class="col-6 col-md-4 mx-auto">
                                                <div>
                                                    <div class="text-uppercase text-center mx-auto">
                                                        <a class="text-dark font-weight-bold text-hover-info" target="_blank" href="{{route('coursiers.show',$commande->coursier)}}">
                                                            {{ucfirst($commande->coursier->noms.' '.$commande->coursier->prenoms)}}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    @endif
                                    @if(!in_array($commande->statut ,['livre','annulee','supprime','echoue']))
                                        <div class="col-12 mt-2 row">
                                            <button class="mb-1 btn ml-1 btn-outline-dark mx-auto " id="modal_danger" data-toggle="modal" data-target="#change_statut"  title="Cliquer pour changer le statut">
                                                <i data-feather="rotate-ccw" class="mr-50"></i>
                                                Changer le statut
                                            </button>
                                            @if ($commande->disponibility == 1)
                                                <button type="button" data-toggle="modal" data-target="#danger" class="mx-auto mb-1 btn btn-danger" onclick="put_statut('annulee'); remplir('{{route('Coursierusers.destroy',$commande->id)}}','', 'Rendre cette Commande Indisponible ','button_remove')">
                                                    <i data-feather="phone-off" class="mr-50"></i>
                                                    Rendre Indisponible
                                                </button> 
                                            @else
                                                <button type="button" data-toggle="modal" data-target="#danger" class="mx-auto mb-1 btn btn-success" onclick="put_statut('annulee'); remplir('{{route('Coursierusers.destroy',$commande->id)}}','', 'Rendre cette Commande Disponible ','button_remove')">
                                                    <i data-feather="phone" class="mr-50"></i>
                                                    Rendre Disponible
                                                </button>
                                        @endif
                                        </div>
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- infos direct de la command -->
                </section>
                <!-- Description list alignment -->
                <section id="description-list-alignment">
                    <div class="row">
                        <div class="col-md-12 text-center mt-1 mb-2">
                            <div class="group-area">
                                <h4>Plus d'informations sur la commande</h4>
                                <p>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row  match-height">
                        <!-- Description lists horizontal -->
                        <div class="col-sm-12 col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Informations du client <small class="text-muted ml-md-1">{{$commande->id_client == null ? 'Simple' : ucfirst($commande->client->type__client->libelle)}}</small></h4>
                                </div>
                                <div class="card-body pt-md-2">
                                    <dl class="row">
                                        <dt class="col-sm-5 text-truncate">
                                            <i data-feather="user" class="mr-1"></i>
                                            <span class="card-text user-info-title font-weight-bold mb-0">{{$commande->id_client == null ? 'Noms et Prénoms' : 'Nom Entreprise'}}</span>
                                        </dt>
                                        <dd class="col-sm-7 mt-1 mt-sm-0">{{$commande->id_client == null ? $commande->nom_client : $commande->client->noms.' '.$commande->client->Prenoms}}</dd>
                                    </dl>
                                    <dl class="row">
                                        @php
                                            $commande->id_client == null ? $telephone = $commande->telephone : $telephone = $commande->client->telephone;
                                        @endphp
                                        <dt class="col-sm-5 text-truncate">
                                            <i data-feather="phone" class="mr-1"></i>
                                            <span class="card-text user-info-title font-weight-bold mb-0">Contacts du client</span>
                                        </dt>
                                        <dd class="col-sm-7 mt-1 mt-sm-0">
                                            @if($telephone == null)
                                                <div class="spinner-grow spinner-grow-sm" role="status">
                                                </div>
                                            @else 
                                                <button class="btn btn-sm btn-dark" style="margin-bottom: 0rem !important" onclick="copy({{ Js::from(e(phone2($telephone))) }})">
                                                    {{phone($telephone)}} <i data-feather='copy' class="ml-1"></i>
                                                </button>
                                            @endif
                                        </dd>
                                    </dl>
                                    @if($commande->id_client != null)
                                    <dl class="row">
                                        <dt class="col-sm-5 text-truncate">
                                            <i data-feather="check" class="mr-1"></i>
                                            <span class="card-text user-info-title font-weight-bold mb-0">Status du client</span>
                                        </dt>
                                        <dd class="col-sm-7 mt-1 mt-sm-0">
                                            <span class="badge badge-glow px-1 @if($commande->client->statut == 0)badge-danger @else badge-success @endif">
                                                @if($commande->client->statut == 0)
                                                    Désactivé
                                                @else 
                                                    Actif
                                                @endif
                                            </span>
                                        </dd>
                                    </dl>
                                    <dl class="row mb-0">
                                        <a href="{{route('Coursierclients.show',$commande->client)}}" target="_blank" type="submit" class="badge badge-primary py-1 mx-auto col-8 col-sm-9 col-lg-6  col-xl-5 col-md-8  mt-1 mt-md-5 ">
                                            <i class="mr-1" data-feather='eye'></i> Voir le client<i class="ml-1" data-feather='link'></i>
                                        </a>
                                    </dl>
                                    @else
                                    <dl class="row">
                                        <dt class="col-sm-5 text-truncate">
                                            <i data-feather="check" class="mr-1"></i>
                                            <span class="card-text user-info-title font-weight-bold mb-0">Status du client</span>
                                        </dt>
                                        <dd class="col-sm-7 mt-1 mt-sm-0">
                                            <span class="badge badge-glow px-1 badge-success">
                                                Actif
                                            </span>
                                        </dd>
                                    </dl>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!--/ Description lists horizontal-->

                        <!-- Description lists vertical-->
                        <div class="col-sm-12 col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title mb-1">Détails sur Livraison<small class="text-muted ml-md-1">Adresse</small></h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="col-12">
                                            <div class="card m-0 p-0">
                                                <div class="card-header pt-0">
                                                    <h4 class="card-title">A la collecte<small class="text-muted"></small></h4>
                                                </div>
                                                <div class="card-body pb-0">
                                                    <dl class="row">
                                                        @php
                                                             $telephone = explode('*/*',$commande->adresse_colis)[0] ;
                                                             $description = isset(explode('*/*',$commande->adresse_colis)[2]) ? explode('*/*',$commande->adresse_colis)[2] : 'Pas de détails';
                                                        @endphp
                                                        <dt class="col-sm-6 text-truncate">
                                                            <i data-feather="phone" class="mr-1 "></i>
                                                            <span class="card-text user-info-title font-weight-bold mb-0">Contacts à la collecte</span>
                                                        </dt>
                                                        <dd class="col-sm-6 mt-1 mt-sm-0">
                                                            @if($telephone == null)
                                                                <div class="spinner-grow spinner-grow-sm" role="status">
                                                                </div>
                                                            @else 
                                                                +237 {!!$telephone[0].' '.$telephone[1].$telephone[2].' '.$telephone[3].$telephone[4].' '.$telephone[5].$telephone[6].' '.$telephone[7].$telephone[8]!!}
                                                            @endif
                                                        </dd>
                                                    </dl>
                                                    <dl class="row">
                                                        <dt class="col-sm-6 text-truncate">
                                                            <i data-feather="map-pin" class="mr-1 border-bottom"></i>
                                                            <span class="card-text user-info-title font-weight-bold mb-0">Lieu de collecte</span>
                                                        </dt>
                                                        <dd class="col-sm-6 mt-1 mt-sm-0">{{explode('*/*',$commande->adresse_colis)[1]}}</dd>
                                                    </dl>
                                                    <div class="text-center">
                                                        <h6>Description Livraison</h6>
                                                        <div class=" px-2">
                                                            <p> {{$description}} </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="col-12">
                                            <div class="card m-0 p-0">
                                                <div class="card-header pt-0">
                                                    <h4 class="card-title">A la livraison<small class="text-muted"></small></h4>
                                                </div>
                                                <div class="card-body">
                                                    <dl class="row">
                                                        @php
                                                             $telephone = explode('*/*',$commande->adresse_livraison)[0] ;
                                                             $description = isset(explode('*/*',$commande->adresse_livraison)[2]) ? explode('*/*',$commande->adresse_livraison)[2] : 'Pas de détails';
                                                             $nom_destinataire = isset(explode('*/*',$commande->adresse_livraison)[3]) ? explode('*/*',$commande->adresse_livraison)[3] : 'Pas de Nom';
                                                        @endphp
                                                        <dt class="col-sm-6 text-truncate">
                                                            <i data-feather="phone" class="mr-1"></i>
                                                            <span class="card-text user-info-title font-weight-bold mb-0">Contacts à la livraison</span>
                                                        </dt>
                                                        <dd class="col-sm-6 mt-1 mt-sm-0">
                                                            @if($telephone == null)
                                                                <div class="spinner-grow spinner-grow-sm" role="status">
                                                                </div>
                                                            @else 
                                                                +237 {!!$telephone[0].' '.$telephone[1].$telephone[2].' '.$telephone[3].$telephone[4].' '.$telephone[5].$telephone[6].' '.$telephone[7].$telephone[8]!!}
                                                            @endif
                                                        </dd>
                                                    </dl>
                                                    <dl class="row">
                                                        <dt class="col-sm-6 text-truncate">
                                                            <i data-feather="at-sign" class="mr-1 border-bottom"></i>
                                                            <span class="card-text user-info-title font-weight-bold mb-0">Nom</span>
                                                        </dt>
                                                        <dd class="col-sm-6 mt-1 mt-sm-0">{{name($nom_destinataire)}}</dd>
                                                    </dl>
                                                    <dl class="row">
                                                        <dt class="col-sm-6 text-truncate">
                                                            <i data-feather="map-pin" class="mr-1 border-bottom"></i>
                                                            <span class="card-text user-info-title font-weight-bold mb-0">Lieu de livraison</span>
                                                        </dt>
                                                        <dd class="col-sm-6 mt-1 mt-sm-0">{{explode('*/*',$commande->adresse_livraison)[1]}}</dd>
                                                    </dl>
                                                    <div class="text-center">
                                                        <h6>Description Livraison</h6>
                                                        <div class=" px-2">
                                                            <p> {{$description}} </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ Description lists vertical-->
                    </div>
                    <div class="row">
                        <div class="col-md-7 col-sm-12 mx-auto">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Description du colis</h4>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-content ">
                                    <div class="card-body">
                                        <p class="card-text">
                                            {!! nl2br(e($commande->description)) !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @if($commande->details_commande->count() > 0 )
                        <div class="col-md-5 col-sm-12 mx-auto">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Produits</h4>
                                    <div class="heading-elements">
                                    </div>
                                </div>
                                <div class="card-content" style="">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>Nom</th>
                                                        <th>libelle</th>
                                                        <th>Quantité</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($commande->details_commande as $detail_commande )
                                                        <tr class="text-center">
                                                            <td>
                                                                {{$detail_commande->produit->noms}}
                                                            </td>
                                                            <td>
                                                                {{$detail_commande->produit->libelle}}
                                                            </td>
                                                            <td>
                                                                {{$detail_commande->quantite}}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    </div>
                </section>
                <!--/ Description list alignment -->

            </div>
        </div>
    </div>
    @php        
        $commande->statut == 'attente' ? $depart = 1 : $statuts;
        $commande->statut == 'attribue' ? $depart = 2 : $statuts;
        $commande->statut == 'encours' ? $depart = 3 : $statuts;
    @endphp
<!-- debut modale de changement de statut -->
                        
                        @if(!in_array($commande->statut ,['livre','annulee','supprime','echoue']))
                            <div class="modal fade modal-danger text-left" id="change_statut" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div></div>
                                            <h3 class="modal-title text-dark" id="myModalLabel120">Changer le satut</h3>
                                            <button type="button" class="bg-danger close m-0" data-dismiss="modal" aria-label="Close">
                                                <span class="text-white" aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body p-md-5 p-2 bg-light-secondary" id="conteneur">
                                            <div class="p-2 border bg-white rounded">
                                                @for($i=$depart;$i<count($statuts);$i++)
                                                    @if($commande->statut == 'attente')
                                                        @if($i == 2 || $i == 3)
                                                            @continue
                                                        @endif
                                                    @endif

                                            @if($commande->disponibility != 1 && in_array($statuts[$i], ['encours','livre']))
                                                @continue
                                            @endif
                                                <span id="erreur{{$commande->id}}" class="dropdown-item cursor-pointer" class="cursor pointer" data-toggle="modal" data-target="{{$statuts[$i] == 'attribue' ? '#choose_coursier' :'#danger'}}" onclick="put_statut('{{$statuts[$i]}}');
                                                @if($statuts[$i] !== 'attribue') 
                                                 remplir('{{route('commandes.destroy',$commande->id)}}','', 'Changer le statut de la commande à {{$statuts[$i] == 'attente' ? 'En attente' : ''}}{{$statuts[$i] == 'attribue' ? 'Attribuée' : ''}}{{$statuts[$i] == 'encours' ? 'En cours' : ''}}{{$statuts[$i] == 'livre' ? 'Livrée' : ''}}{{$statuts[$i] == 'annulee' ? 'Annulée' : ''}}{{$statuts[$i] == 'echoue' ? 'Echouée' : ''}}','button_remove')"
                                                @endif >
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
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
<!-- fin de modale de changement de statut -->
<!-- debut modale de choix du coursier -->
                            <div class="modal fade modal-danger text-left" id="choose_coursier" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <form method="POST" class="destroy_form col-12 mx-auto">
                                        @csrf
                                        @method('DELETE')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div></div>
                                            <h3 class="modal-title text-dark" id="myModalLabel120">Choisir le coursier</h3>
                                            <button type="button" class="bg-danger close m-0" data-dismiss="modal" aria-label="Close">
                                                <span class="text-white" aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body" id="conteneur_coursier">
                                                <div class=" col-12 pb-2 my-auto">
                                                    <div class="form-group">
                                                        <label for="id_coursier">Choisir Un Coursiers</label>
                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                        <div class="form-group">
                                                            <input type="hidden" class="statut" name="statut">
                                                            <select class=" @error('id_coursier')  is-invalid @enderror select2 form-control" id="id_coursier" name="id_coursier" onchange="" required>
                                                                <option value="">Coursiers</option>
                                                            @foreach($coursiers as $coursier)
                                                                <option class="text-uppercase" value="{{$coursier->id}}" {{$coursier->id == old('id_coursier') ? 'selected' : ''}}>
                                                                        {{$coursier->noms}} {{$coursier->prenoms}}
                                                                </option>
                                                            @endforeach
                                                            </select>
                                                        @error('id_coursier') 
                                                            <small class="alert alert-danger"> {{$message}} </small>
                                                        @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                        <div class="modal-footer" style="justify-content: space-around;">
                                            <button type="submit" class="btn btn-gradient-success btn-success round btn-lg">Confirmer</button>
                                            <button type="button" class="btn btn-gradient-danger btn-danger round btn-lg" data-dismiss="modal">Annuler</button>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        @endif
<!-- fin de modale de choix du coursier -->
    <!-- END: Content-->

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
	<script type="text/javascript">
        cliquer = 0;
        function  put_statut(id){
            var status = document.querySelectorAll('.statut');
            for (i = 0; i < status.length; i++) {
                status[i].value = id;
            }
        }
        document.querySelector('#Acceuil')?.classList.add('active');
	</script>
@endsection