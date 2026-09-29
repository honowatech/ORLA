@extends('admin/templates/template')
@section('title')
    {{'Enregistrer une commande'}}
@endsection
@section('contenu')


    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Nouvelle Commande</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('home')}}l">Acceuil</a>
                                    </li>
                                    <li class="breadcrumb-item active"> Nouvelle Commande
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- account setting page -->
                <section id="page-account-settings">
                    <div class="row">
                        <!-- left menu section -->
                        <div class="col-lg-4 col-sm-7 col-12 mb-md-0 mx-auto">
                            <ul class="nav nav-pills row justify-content-center nav-left">
                                <!-- commandes simple et rapide -->
                                <li class="nav-item">
                                    <a class="nav-link @if( session()->get('type_commande') != 'entreprise') active @endif" href="#account-vertical-general"  onclick="reinitialisation();change_type('simple')" data-toggle="pill" aria-expanded="true">
                                        <i data-feather="clock" class="font-medium-3 mr-1"></i>
                                        <span class="font-weight-bold">Simple</span>
                                    </a>
                                </li>
                                <!-- commandes concernant un client -->
                                <li class="nav-item">
                                    <a class="nav-link @if( session()->get('type_commande') == 'entreprise')active @endif" data-toggle="pill" href="#account-vertical-general"  onclick="reinitialisation();change_type('entreprise');" aria-expanded="false">
                                        <i data-feather="lock" class="font-medium-3 mr-1"></i>
                                        <span class="font-weight-bold">Entreprise</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!--/ left menu section -->

                        <!-- right content section -->
                        <div class=" col-sm-12 mx-auto">
                            <div class="card">
                                <div class="card-body">
                                    <div class="tab-content">
                                        <!-- commandes simple et rapide -->
                                        <div role="tabpanel" class="tab-pane  active " id="account-vertical-general" aria-labelledby="account-pill-general" aria-expanded="true">
                                            <!-- form -->
                                            <form action="{{route('commandes.store')}}" id="form1" method="POST" class="validate-form mt-2">
                                                @csrf
                                                <input type="text" id="type_commande" hidden name="type_commande" value="{{ session()->get('type_commande') != 'entreprise'? 'simple' : 'entreprise' }}">
                                                <div class="row">
                                                    <fieldset style="border: 2px solid #00000010" class="simple_info col-11 mb-1 row mx-auto p-2">
                                                        <legend class="pl-1">Infos du client</legend>
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                                <label for="phone_number">Numéro de Téléphone</label>
                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                <div class="input-group input-group-merge @error('telephone')  is-invalid @enderror">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i class="mr-1 flag-icon flag-icon-cm"></i>+237</span>
                                                                    </div>
                                                                    <input type="text" class="simple_info @error('telephone')  is-invalid @enderror form-control" placeholder="6 12 34 56 78" id="phone_number" name="telephone2" value="{{old('telephone2') }}" onclick="selectionner_infos('{{route('commandeClient')}}','phone_number2','result_client','phone_number');" onkeyup="initialise(['id_client','nom_client'],true);phone('phone_number');selectionner_infos('{{route('commandeClient')}}','phone_number2','result_client','phone_number');" autofocus required/>
                                                                </div>
                                                                <div class="dropdown-menu mx-auto resultat pxs-1" id="result_client" style="overflow: auto; max-height: 245px; min-width:100px;" x-placement="bottom">
                                                                    <span>
                                                                        <b class="text-center">Remplir le champ pour avoir des propositions</b>
                                                                    </span>
                                                                </div>
                                                                <input type="text" hidden id="phone_number2" name="telephone"/>
                                                                <input type="text" hidden id="id_client" value="{{old('id_client') }}" name="id_client"/>
                                                                @error('telephone') 
                                                                        <small class="alert alert-danger"> {{$message}} </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label for="nom_client" id="label">Nom du client</label>
                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                <div class="input-group input-group-merge @error('nom_client') is-invalid @enderror"  id="fetelephone">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i class="mr-1" data-feather="at-sign"></i></span>
                                                                    </div>
                                                                    <input type="text" class="simple_info @error('nom_client')  is-invalid @enderror form-control" placeholder="Nom et prénoms" id="nom_client" name="nom_client" value="{{old('nom_client') }}" required/>
                                                                </div>
                                                                @error('nom_client') 
                                                                        <small class="alert alert-danger"> {{$message}} </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset style="border: 2px solid #00000010" class="boutique_info col-11 mb-1 row mx-auto p-2">
                                                        <legend class="pl-1">Infos sur l'entreprise</legend>
                                                        <div  class="col-md-10 col-12 mx-auto">
                                                            <div class="form-group">
                                                                <label for="entreprise"> Entreprise </label>
                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                <div class="form-group">
                                                                    <select class="boutique_info @error('entreprise')  is-invalid @enderror select2 form-control" onchange="change_value('entreprise');choose()" id="entreprise" name="entreprise" required />
                                                                        <option class="entreprise" value="" selected>Choisir une Entreprise
                                                                        </option>
                                                                        @foreach($entreprises as $entreprise)
                                                                        <option class="entreprise" data-id="{{$entreprise->id}}" data-phone="{{$entreprise->telephone}}" 
                                                                            value="{{$entreprise->id}}"
                                                                            {{$entreprise->id == old('entreprise') ? 'selected' : ''}}{{session()->has('entreprise') == $entreprise->id ? 'selected' : ''}}>{{$entreprise->noms}} {{$entreprise->Prenoms}}
                                                                        </option>
                                                                        @endforeach
                                                                    </select>
                                                                <input type="text" id="id_client2" hidden value="{{old('id_client2') }}" name="id_client2"/>
                                                                    <small id="entreprise_error" class="alert alert-danger" hidden> Veuillez faire une selection </small>
                                                                @error('entreprise') 
                                                                    <small class="alert alert-danger"> {{$message}} </small>
                                                                @enderror
                                                                </div>
                                                            </div>
                                                    </fieldset>
                                                    <fieldset style="border: 2px solid #00000010" id="boutique_info" class="col-11 mb-1 row mx-auto p-2">
                                                        <legend class="pl-1">Infos sur la Boutique</legend>
                                                        <div class="col-12 pr-0 row">
                                                            <div class="col-md-8 pr-md-1 pr-0 col-12 mx-auto">
                                                                <div class="form-group">
                                                                    <label for="id_boutique">Choisir La boutique</label>
                                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                    <div class="form-group mb-0" id="div_boutique" hidden>
                                                                        <select class="boutique_info @error('id_boutique') is-invalid @enderror select2 form-control" id="id_boutique" name="id_boutique" onchange="put_info_entreprise()" required>
                                                                        </select>
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <div class="spinner-border text-info" role="status" id="spinner">
                                                                            <span class="sr-only"></span>
                                                                        </div>
                                                                    </div>
                                                                    <small class="alert alert-danger"><span id="erreur1"></span><br> </small>
                                                                    <small id="id_boutique_error" class="alert alert-danger" hidden> Veuillez faire une selection </small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-10 pr-0 pr-md-1 col-12 mx-auto">
                                                                <div class="form-group">
                                                                    <label for="produit">Produit</label>
                                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                    <div class="form-group">
                                                                        <select class=" @error('produit')  is-invalid @enderror select2 form-control boutique_info" id="produit" onchange="upgrade_ligne_produit('decrire');" multiple name="produit" required>
                                                                            @foreach($produits as $produit)
                                                                                <option data-description="{{$produit->noms}} ___ {{$produit->description}}" data-nom="{{$produit->noms}}" data-id="{{$produit->id}}" class="text-uppercase decrire produit"
                                                                                    value="{{$produit->id}}"
                                                                                    @if(session()->has('table_produit'))
                                                                                    {{in_array($produit->id, session()->get('table_produit') ) ? 'selected' : ''}}
                                                                                    @endif
                                                                                    >
                                                                                    {{$produit->noms}}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        <input type="text" name="table_produit" id="table_produit" hidden>
                                                                        <small id="produit_error" class="alert alert-danger" hidden> Veuillez faire une selection </small>
                                                                    @error('produit') 
                                                                            <small class="alert alert-danger"> {{$message}} </small>
                                                                    @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-8 col-12 mx-auto"  id="lister_produits">
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset style="border: 2px solid #00000010" class="col-11 mb-1 row mx-auto p-2">
                                                        <legend class="pl-1">Infos de Livraison</legend>
                                                        <div class="col-md-6 col-12" id="collecte">
                                                            <h4 class="col-12 text-center">Adresse à la collecte</h4>
                                                            <div class="row">
                                                                <div class="row m-auto">
                                                                    <div class="col-12">
                                                                        <div class="form-group">
                                                                            <label for="contact_colis" id="label">Contact</label>
                                                                            <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                            <div class="input-group input-group-merge @error('contact_colis')  is-invalid @enderror">
                                                                                <div class="input-group-prepend">
                                                                                    <span class="input-group-text"><i class="mr-1 flag-icon flag-icon-cm"></i>+237</span>
                                                                                </div>
                                                                                <input type="text" class=" @error('contact_colis')  is-invalid @enderror form-control" placeholder="6 12 34 56 78" id="contact_colis" onkeyup="phone('contact_colis');" name="contact_colis" value="{{old('contact_colis') }}" required/>
                                                                            </div>
                                                                            <input type="text" id="contact_colis2" name="contact_colis" hidden name="telephone"/>
                                                                            @error('contact_colis') 
                                                                                    <small class="alert alert-danger"> {{$message}} </small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6 mx-auto col-12">
                                                                        <div class="form-group">
                                                                            <label for="ville_collecte"> Ville </label>
                                                                            <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                            <div class="form-group">
                                                                                <select class=" @error('ville_collecte')  is-invalid @enderror select2 form-control" id="ville_collecte" name="ville_collecte" required />
                                                                                    @foreach($villes as $ville)
                                                                                        <option class="ville_collecte" 
                                                                                            value="{{$ville->id}}"
                                                                                            {{$ville == old('ville_collecte') ? 'selected' : ''}}>{{$ville->libelle}}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            @error('ville_collecte') 
                                                                                    <small class="alert alert-danger"> {{$message}} </small>
                                                                            @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6 mx-auto col-12">
                                                                        <div class="form-group" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                                            <label for="lieu_collecte">Lieu</label>
                                                                            <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                            <div class="input-group input-group-merge @error('lieu_collecte')  is-invalid @enderror">
                                                                                <div class="input-group-prepend">
                                                                                    <span class="input-group-text"><i class="mr-1" data-feather="map-pin"></i></span>
                                                                                </div>
                                                                                <input type="text" class=" @error('lieu_collecte')  is-invalid @enderror form-control" placeholder="Lieu de collecte" id="lieu_collecte" name="lieu_collecte" value="{{old('lieu_collecte') }}" onclick="selectionner_infos('{{route('lieu')}}','lieu_collecte','result_collecte')" onkeyup="initialise(['id_quartier_colis']);montant_commande();selectionner_infos('{{route('lieu')}}','lieu_collecte','result_collecte')" required/>
                                                                            </div>
                                                                            <div class="px-1 dropdown-menu mx-auto resultat" id="result_collecte" style="overflow: auto; max-height: 245px; min-width:100px;" x-placement="bottom">
                                                                                <span>
                                                                                    <b class="text-center">Remplir le champ pour avoir des propositions</b>
                                                                                </span>
                                                                            </div>
                                                                            <input type="text" hidden id="id_quartier_colis" value="{{old('id_quartier_colis')}}" name="id_quartier_colis"/>
                                                                            @error('lieu_collecte') 
                                                                                    <small class="alert alert-danger"> {{$message}} </small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12  mx-auto">
                                                                        <div class="form-group">
                                                                            <label for="description_collecte">Description du lieu de collecte</label>
                                                                            <span class="text-info cursor-pointer" title="Optionnel">Optionnel</span>
                                                                            <textarea class="@error('description_collecte')  is-invalid @enderror form-control" placeholder="Décrire le Lieu de collecte" rows="3" id="description_collecte" name="description_collecte">{{old('description_collecte')}}</textarea>
                                                                            @error('description_collecte') 
                                                                                    <small class="alert alert-danger"> {{$message}} </small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12 mx-auto" id="livraison">
                                                            <h4 class="col-12 text-center">Adresse à la Livraison</h4>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="contact_livraison" id="label">Contact</label>
                                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                        <div class="input-group input-group-merge @error('contact_livraison')  is-invalid @enderror">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i class="mr-1 flag-icon flag-icon-cm"></i>+237</span>
                                                                            </div>
                                                                            <input type="text" class=" @error('contact_livraison')  is-invalid @enderror form-control" placeholder="6 12 34 56 78" id="contact_livraison" onkeyup="phone('contact_livraison');" name="contact_livraison2" value="{{old('contact_livraison') }}" required/>
                                                                        </div>
                                                                        <input type="text" id="contact_livraison2" name="contact_livraison"hidden name="telephone"/>
                                                                        @error('contact_livraison') 
                                                                                <small class="alert alert-danger"> {{$message}} </small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="nom_livraison" id="label">Nom du Destinataire</label>
                                                                        <span class="text-info cursor-pointer" title="Optionnel">Optionnel</span>
                                                                        <div class="input-group input-group-merge @error('nom_livraison') is-invalid @enderror"  id="fetelephone">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i class="mr-1" data-feather="at-sign"></i></span>
                                                                            </div>
                                                                            <input type="text" class="@error('nom_livraison')  is-invalid @enderror form-control" placeholder="Nom et prénoms" id="nom_livraison" name="nom_livraison" value="{{old('nom_livraison') }}"/>
                                                                        </div>
                                                                        @error('nom_livraison') 
                                                                                <small class="alert alert-danger"> {{$message}} </small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 col-12">
                                                                    <div class="form-group">
                                                                        <label for="ville_livraison"> Ville </label>
                                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                        <div class="form-group">
                                                                            <select class=" @error('ville_livraison')  is-invalid @enderror select2 form-control" id="ville_livraison" name="ville_livraison" required />
                                                                                @foreach($villes as $ville)
                                                                                    <option class="ville_livraison" 
                                                                                        value="{{$ville->id}}"
                                                                                        {{$ville == old('ville_livraison') ? 'selected' : ''}}>{{$ville->libelle}}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        @error('ville_livraison') 
                                                                                <small class="alert alert-danger"> {{$message}} </small>
                                                                        @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 col-12">
                                                                    <div class="form-group" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                                        <label for="lieu_livraison">Lieu</label>
                                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                        <div class="input-group input-group-merge @error('lieu_livraison')  is-invalid @enderror">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i class="mr-1" data-feather="map-pin"></i></span>
                                                                            </div>
                                                                            <input type="text" class=" @error('lieu_livraison')  is-invalid @enderror form-control" placeholder="Lieu de collecte" id="lieu_livraison" name="lieu_livraison" value="{{old('lieu_livraison') }}" onclick="selectionner_infos('{{route('lieu2')}}','lieu_livraison','result_livraison')" onkeyup="initialise(['id_quartier_livraison']);montant_commande();selectionner_infos('{{route('lieu2')}}','lieu_livraison','result_livraison')" required/>
                                                                        </div>
                                                                        <div class="px-1 dropdown-menu mx-auto resultat" id="result_livraison" style="overflow: auto; max-height: 245px; min-width:100px;" x-placement="bottom">
                                                                            <span>
                                                                                <b class="text-center">Remplir le champ pour avoir des propositions</b>
                                                                            </span>
                                                                        </div>
                                                                        <input type="text" value="{{old('id_quartier_livraison')}}"  id="id_quartier_livraison" hidden name="id_quartier_livraison"/>
                                                                        @error('lieu_livraison') 
                                                                                <small class="alert alert-danger"> {{$message}} </small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-12  mx-auto">
                                                                    <div class="form-group">
                                                                        <label for="description_livraison">Description du lieu de livraison</label>
                                                                        <span class="text-info cursor-pointer" title="Optionnel">Optionnel</span>
                                                                        <textarea class=" @error('description_livraison')  is-invalid @enderror form-control" placeholder="Décrire le Lieu de livraison" rows="3" id="description_livraison" name="description_livraison">{{old('description_livraison')}}</textarea>
                                                                        @error('description_livraison') 
                                                                                <small class="alert alert-danger"> {{$message}} </small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset style="border: 2px solid #00000010" class="col-11 mb-1 row mx-auto p-2">
                                                        <legend class="pl-1">Infos sur le Paiement</legend>
                                                        <div class="col-md-6 col-12 m-auto">
                                                            <div class="text-center mx-auto">
                                                                <label>Le coursier récupère l'argent ? </label><span class="text-danger cursor-pointer" title="Obligatoire"> * </span>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="form-group row col-md-10 col-lg-8 mx-auto justify-content-around">
                                                                    @foreach($modes_de_paiement as $mode_de_paiement)
                                                                    <div class="form-check custom-control custom-control-info custom-radio mt-1">
                                                                        <input onchange="montant_collecte('div_collecter','montant_collecter','mode_paiement')" class="mode_paiement custom-control-input" type="radio" name="mode_de_paiement" id="{{$mode_de_paiement}}" value="{{$mode_de_paiement}}" {{$mode_de_paiement == old('mode_de_paiement') ? 'checked' : ''}}>
                                                                        <label class="custom-control-label" id="for{{$mode_de_paiement}}" for="{{$mode_de_paiement}}">
                                                                            {{$mode_de_paiement == 'speedex' ? 'Non' : ''}}
                                                                            {{$mode_de_paiement == 'coursier' ? 'Oui' : ''}}
                                                                        </label>
                                                                    </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12 p-0">
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label for="montant_livraison">Frais de livraison</label>
                                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                    <div class="input-group input-group-merge @error('montant_livraison')  is-invalid @enderror">
                                                                        <input type="text" class="@error('montant_livraison')  is-invalid @enderror form-control" placeholder="1,000" id="montant_livraison" name="montant_livraison2" onkeyup="format_montant('montant_livraison');remplir_montant('montant_livraison')" value="{{old('montant_livraison') == null ? '1000' : old('montant_livraison')}}" required />
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text">FCFA</span>
                                                                        </div>
                                                                    </div>
                                                                    @error('montant_livraison') 
                                                                            <small class="alert alert-danger"> {{$message}} </small>
                                                                    @enderror
                                                                </div>
                                                                <input type="text" id="montant_livraison2" name="montant_livraison" value="{{old('montant_livraison')}}" hidden/>
                                                            </div>
                                                            <div class="col-12 mx-auto" id="div_collecter">
                                                                <div class="form-group">
                                                                    <label for="montant_collecter">Montant à collecter</label>
                                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                    <div class="input-group input-group-merge @error('montant_collecter')  is-invalid @enderror">
                                                                        <input type="text" class=" @error('montant_collecter')  is-invalid @enderror form-control" placeholder="Tout l'argent à récupérer" id="montant_collecter" name="montant_collecter2" onkeyup="format_montant('montant_collecter');remplir_montant('montant_collecter')" value="{{old('montant_collecter')}}" required />
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text">FCFA</span>
                                                                        </div>
                                                                    </div>
                                                                    @error('montant_collecter') 
                                                                            <small class="alert alert-danger"> {{$message}} </small>
                                                                    @enderror
                                                                </div>
                                                                <input type="text" id="montant_collecter2" name="montant_collecter" value="{{old('montant_collecter')}}" hidden/>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset style="border: 2px solid #00000010" class="col-11 mb-1 row mx-auto p-2">
                                                        <legend class="pl-1">Date de livraison</legend>
                                                        <div class="col-md-6 col-12  mx-auto">
                                                            <div class="form-group">
                                                                <label for="date">Date</label>
                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                <div class="input-group input-group-merge @error('date')  is-invalid @enderror">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i class="mr-1" data-feather="calendar"></i></span>
                                                                    </div>
                                                                    <input class="form-control flatpickr-human-friendly @error('date')  is-invalid @enderror "  placeholder="January 01, 2024" tabindex="0" type="text" id="date" readonly="readonly" name="date" required value="{{old('date') == null ? now() : old('date')}}">
                                                                </div>
                                                                @error('date') 
                                                                        <small class="alert alert-danger"> {{$message}} </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label for="time">Heure</label>
                                                                <span class="text-info cursor-pointer" title="Optionnel">Optionnel</span>
                                                                <div class="row">
                                                                    <div class="col-9">
                                                                        <div class="input-group input-group-merge @error('time')  is-invalid @enderror">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i class="mr-1" data-feather="clock"></i></span>
                                                                            </div>
                                                                            <input type="text" id="time" class="flatpickr-time flatpickr text-left form-control input @error('time') is-invalid @enderror " name="time" style="height: 2.714rem;" placeholder="Heure:Minute" value="{{old('time')}}"/>
                                                                        </div>
                                                                    </div>
                                                                    <button type="button" onclick="time.value='';" class="col-2 btn btn-gradient-danger btn-danger" title="Effacer l'heure"><i data-feather="x"></i></button>
                                                                </div>
                                                                @error('time') 
                                                                        <small class="alert alert-danger"> {{$message}} </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset style="border: 2px solid #00000010" class="col-11 mb-1 row mx-auto p-2">
                                                        <legend class="pl-1">Infos supplémentaires</legend>
                                                        <div class="col-md-8 col-12  mx-auto">
                                                            <div class="form-group">
                                                                <label for="description">Description</label>
                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                <textarea type="number" class="@error('description')  is-invalid @enderror form-control" placeholder="Décrire le colis" rows="3" id="description0" name="description" required >{{old('description')}}</textarea>
                                                                @error('description') 
                                                                        <small class="alert alert-danger"> {{$message}} </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <div class="d-none d-md-block text-center col-bg-5 col-lg-8 col-md-12 col-12 mx-auto">
                                                        <button type="reset" class="btn col-8 col-sm-7 col-lg-5  col-xl-3 col-md-4 btn-danger mt-1 mt-md-2 mr-md-4">
                                                            <i class="mr-1" data-feather='x'></i> Effacer
                                                        </button>
                                                        <button onclick="seeRecap()" type="button" class="recap btn col-8 col-sm-7 col-lg-5 col-xl-3 col-md-4 btn-success mt-1 mt-md-2">
                                                            <i class="mr-1" data-feather='download'></i> Continuer
                                                        </button>
                                                    </div>
                                                    <div class="d-block d-md-none text-center col-bg-5 col-lg-8 col-md-12 col-12 mx-auto">
                                                        <button  onclick="seeRecap()" type="button" class="recap btn col-8 col-sm-7 col-lg-5  col-xl-3 col-md-4 btn-success mt-1 mt-md-2 mr-md-2">
                                                            <i class="mr-1" data-feather='download'></i> Continuer
                                                        </button>
                                                        <button type="reset" class="btn col-8 col-sm-7 col-lg-5 col-xl-3 col-md-4 btn-danger mt-1 mt-md-2">
                                                            <i class="mr-1" data-feather='x'></i> Effacer
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                            <!--/ form -->
                                        </div>
                                        <!--/ commandes simple et rapide -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ right content section -->
                    </div>
                </section>
                <!-- / account setting page -->
                <div class="modal fade" id="exampleModalScrollable" tabindex="-1" aria-labelledby="exampleModalScrollableTitle" aria-modal="true" role="dialog">
                                                    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalScrollableTitle">Récap de la commande</h5>
                                                                <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">×</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-6 col-12"> Type de commande :</div> <div class="col-md-6 col-12 text-uppercase" id="Type_commande_modal"></div>
                                                                </div>
                                                                <hr>
                                                                <div class="boutique_info">
                                                                    <div class="row">
                                                                        <div class="boutique_info col-md-6 col-12">Nom de L'entreprise :</div> <div class="col-md-6 col-12 text-uppercase" id="nom_entreprise_modal"></div>
                                                                    </div>
                                                                    <hr>
                                                                </div>
                                                                <div class="simple_info">
                                                                    <div class="row ">
                                                                        <div class="simple_info col-md-6 col-12">Nom du client :</div> <div class="col-md-6 col-12  text-uppercase" id="nom_client_modal"></div>
                                                                    </div>
                                                                    <hr>
                                                                </div>
                                                                <div class="simple_info">
                                                                    <div class="row">
                                                                        <div class="simple_info col-md-6 col-12">Numéro du client :</div> <div class="col-md-6 col-12" id="telephone_client_modal"></div>
                                                                    </div>
                                                                    <hr>
                                                                </div>
                                                                <div class="boutique_info">
                                                                    <div class="row">
                                                                        <div class="boutique_info col-md-6 col-12">Nom de la Boutique :</div> <div class="col-md-6 col-12  text-uppercase" id="boutique_modal"></div>
                                                                    </div>
                                                                    <hr>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6 col-12">Contact à la collecte :</div> <div class="col-md-6 col-12" id="contact_colis_modal"></div>
                                                                </div>
                                                                <hr>
                                                                <div class="row">
                                                                    <div class="col-md-6 col-12">Lieu de la collecte :</div> <div class="col-md-6 col-12 text-uppercase" id="lieu_colis_modal"></div>
                                                                </div>
                                                                <hr>
                                                                <div class="row">
                                                                    <div class="col-md-6 col-12">Contact à la livraison :</div> <div class="col-md-6 col-12" id="contact_livraison_modal"></div>
                                                                </div>
                                                                <hr>
                                                                <div class="row">
                                                                    <div class="col-md-6 col-12">Lieu de la livraison :</div> <div class="col-md-6 col-12 text-uppercase" id="lieu_livraison_modal"></div>
                                                                </div>
                                                                <hr>
                                                                <div class="row">
                                                                    <div class="col-md-6 col-12">Montant de Livraison :</div> <div class="col-md-6 col-12" id="montant_livraison_modal"></div>
                                                                </div>
                                                                <hr>
                                                                <div class="row">
                                                                    <div class="col-md-6 col-12">Montant à récupérer :</div> 
                                                                    <div class="col-md-6 col-12" id="mode_paiement_modal"></div>
                                                                </div>
                                                                <hr>
                                                                <div class="row">
                                                                    <div class="col-md-6 col-12">Date de Livraison :</div> 
                                                                    <div class="col-md-6 col-12" id="date_modal"></div>
                                                                </div>
                                                                <hr>
                                                                <div class="row">
                                                                    <div class="col-md-6 col-12 my-auto">Description du colis :</div> <div class="col-md-6 col-12" id="description_modal"></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer justify-content-center justify-content-md-around pt-2">
                                                                    <button data-dismiss="modal" class="d-none d-md-block btn col-12 col-md-5 col-lg-4 btn-danger round  mx-auto">
                                                                        <i class="mr-1" data-feather='x'></i> Annuler
                                                                    </button>
                                                                    <button type="button" onclick="tosubmit()" class="d-none d-md-block btn round col-12 btn-success col-md-5 col-lg-4 mx-auto">
                                                                        <i class="mr-1" data-feather='download'></i> Enregistrer
                                                                    </button>

                                                                    <button type="button" onclick="tosubmit()" class="d-block d-md-none btn round col-12 btn-success col-md-5 col-lg-4 mx-auto">
                                                                        <i class="mr-1" data-feather='download'></i> Enregistrer
                                                                    </button>
                                                                    <button data-dismiss="modal" class="d-block d-md-none btn col-12 col-md-5 col-lg-4 btn-danger round  mx-auto">
                                                                        <i class="mr-1" data-feather='x'></i> Annuler
                                                                    </button>
                                                            </div>
                                                        </div>
                                                    </div>
                </div>
            </div>
        </div>
        <div onmo class="text-center" hidden id="spinner2">
            <div class="col-12 text-center">
                <div class="spinner-border text-dark" role="status">
                    <span class="sr-only"></span>
               </div>                                         
            </div>
        </div>
        <div id="exemple_ligne_produit" hidden>
            <div id="liste_#id">
                <hr>
                <div class="col-12 text-end position-absolute">
                    <button type="button" class="close delete round btn-danger btn-gradient-danger" onclick="delete_ligne('#id','decrire')" style="width: 2.5rem; height: 2.5rem;">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="row col-12 p-0">
                    <div class="col-md-7 col-12 m-auto">
                        <div class="form-group">
                            <span>#produit</span>
                            <input type="hidden" class="boutique_info form-control text-center" id="Produit#id" name="produit#id" required value="#id" />
                        </div>
                    </div>
                    <div class="col-md-5 col-12">
                        <div class="form-group">
                            <label for="quantite#id">Quantité</label>
                            <span class=" text-danger cursor-pointer" title="Obligatoire">*</span>
                            <div class="input-group input-group-merge @error('quantite#id')  is-invalid @enderror">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i data-feather='shopping-bag'></i></span>
                                </div>
                                <input type="number" class="boutique_info form-control text-center" placeholder="Quantité" id="quantite#id" name="quantite#id" required value="1" onkeyup="voir_description('decrire','description0')" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- pop-up message succes -->
@if(session()->has('success'))
<div class="modal fade modal-danger text-left" id="modals-success" tabindex="-1" role="dialog" aria-labelledby="modals-success" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div></div>
                <h3 class="modal-title text-dark" id="myModalLabel120"> Information </h3>
                <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <div class="modal-body">
            <p class="text-center">
                {!!session()->get('success')!!}
            </p>
        </div>
        <div class="modal-footer" style="justify-content: center;">
            <button type="button" class="btn btn-gradient-info btn-info round waves-effect waves-float waves-light" data-dismiss="modal">Terminer</button>
        </div>
        </div>
    </div>
</div>
<script type="text/javascript">
                    function success(){

                    button = document.getElementById('succes_button');
                    button.click();

                    }    
</script>
@endif
    <!-- END: Content-->
@include('admin/pages/commandes/script/bloc_script')
@include('admin/pages/commandes/script/produits_script')
<script type="text/javascript">
    @if (session()->get('type_commande') != 'entreprise') 
        change_type('simple')
    @else
        change_type('entreprise')
    @endif
    @if(session()->has('success'))
    window.onload = function() {success()}
    @endif
    @php
        // Set the timezone to Europe/Berlin (GMT+1)
        date_default_timezone_set('Africa/Douala');
        // Get the current timestamp
        $currentTimestamp = time();
        // Extract the hour and minute from the timestamp
        $hour = date('H', $currentTimestamp);
        $minute = date('i', $currentTimestamp);
    @endphp
    @if(old('time')!=null)
        document.querySelector('#time').value = '{{old('time')}}'
    @else
        window.onload = function() {
            console.log('{{$hour}}:{{$minute}}')
            document.querySelector('#time').value = '{{$hour}}:{{$minute}}'
        }
    @endif
</script>
@endsection
