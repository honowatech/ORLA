@extends('client/templates/template')
@section('title')
    {{'Modifier une commande'}}
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
                            <h2 class="content-header-title float-left mb-0">Modifier une Commande</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('home')}}l">Acceuil</a>
                                    </li>
                                    <li class="breadcrumb-item active"> Modifier une Commande
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

                        <!-- right content section -->
                        <div class=" col-sm-12 mx-auto">
                            <div class="card">
                                <div class="card-body">
                                    <div class="tab-content">
                                        <!-- commandes simple et rapide -->
                                        <div role="tabpanel" class="card" id="account-vertical-general" aria-labelledby="account-pill-general" aria-expanded="true">
                                            <!-- form -->
                                            <form action="{{route('Clientcommandes.update',$commande->id)}}" id="form1" method="POST" class="validate-form mt-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" hidden name="type_commande" value="simple">
                                                <div class="row">
                                                    <fieldset style="border: 2px solid #00000010" class="col-11 mb-1 row mx-auto p-2">
                                                        <legend class="pl-1">Infos de livraison</legend>
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <div class="col-12 px-0 text-md-center">
                                                                    <label for="contact_colis">Addresse du colis</label>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-7 col-12">
                                                                        <div class="form-group pb-0">
                                                                            <div class="col-12 text-center">
                                                                                <label for="contact_colis">Contact</label>
                                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                            </div>
                                                                        <div class="input-group input-group-merge @error('contact_colis')  is-invalid @enderror">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i class="mr-1 flag-icon flag-icon-cm"></i>+237</span>
                                                                            </div>
                                                                                <input type="text" class="@error('contact_colis')  is-invalid @enderror form-control" placeholder="Contact du colis" id="contact_colis" onkeyup="phone('contact_colis')" name="contact_colis2" value="{{old('contact_colis2') == null ? explode('*/*',$commande->adresse_colis)[0] : old('contact_colis2')}}" required />
                                                                            </div>
                                                                            <input type="text" id="contact_colis2" name="contact_colis" hidden />
                                                                            @error('contact_colis') 
                                                                                    <small class="alert alert-danger"> {{$message}} </small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-5 col-12">
                                                                        <div class="form-group">
                                                                            <div class="col-12 text-center">
                                                                                <label for="lieu_colis">Lieu</label>
                                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                            </div>
                                                                            <div class="input-group input-group-merge @error('lieu_colis')  is-invalid @enderror">
                                                                                <div class="input-group-prepend">
                                                                                    <span class="input-group-text"><i data-feather="map-pin"></i></span>
                                                                                </div>
                                                                                <input type="text" class="@error('lieu_colis')  is-invalid @enderror form-control" placeholder="Quartier" id="lieu_colis" name="lieu_colis" value="{{old('lieu_colis') == null ? explode('*/*',$commande->adresse_colis)[1] : old('lieu_colis')}}" required />
                                                                            </div>
                                                                            
                                                                            @error('lieu_colis') 
                                                                                    <small class="alert alert-danger"> {{$message}} </small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    @php
                                                                         $description = isset(explode('*/*',$commande->adresse_colis)[2]) ? explode('*/*',$commande->adresse_colis)[2] : '';
                                                                    @endphp
                                                                    <div class="col-12  mx-auto">
                                                                        <div class="form-group">
                                                                            <label for="description_collecte">Description du lieu de collecte</label>
                                                                            <span class="text-info cursor-pointer" title="Optionnel">Optionnel</span>
                                                                            <textarea class="@error('description_collecte')  is-invalid @enderror form-control" placeholder="Décrire le Lieu de collecte" rows="3" id="description_collecte" name="description_collecte">{{old('description_collecte') == null ? $description : old('description_collecte')}}</textarea>
                                                                            @error('description_collecte') 
                                                                                    <small class="alert alert-danger"> {{$message}} </small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <div class="col-12 px-0 text-md-center">
                                                                    <label for="contact_livraison">Addresse de livraison</label>
                                                                </div>
                                                                <div class="row">
                                                                    @php
                                                                         $nom_destinataire = isset(explode('*/*',$commande->adresse_livraison)[3]) ? explode('*/*',$commande->adresse_livraison)[3] : '';
                                                                    @endphp
                                                                    <div class="col-12">
                                                                        <div class="form-group">
                                                                            <label for="nom_livraison" id="label">Nom du Destinataire</label>
                                                                            <span class="text-info cursor-pointer" title="Optionnel">Optionnel</span>
                                                                            <div class="input-group input-group-merge @error('nom_livraison') is-invalid @enderror"  id="fetelephone">
                                                                                <div class="input-group-prepend">
                                                                                    <span class="input-group-text"><i class="mr-1" data-feather="at-sign"></i></span>
                                                                                </div>
                                                                                <input type="text" class="simple_info @error('nom_livraison')  is-invalid @enderror form-control" placeholder="Nom et prénoms" id="nom_livraison" name="nom_livraison" value="{{old('nom_livraison') == null ? $nom_destinataire : old('nom_livraison')}}"/>
                                                                            </div>
                                                                            @error('nom_livraison') 
                                                                                    <small class="alert alert-danger"> {{$message}} </small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-7 col-12">
                                                                        <div class="form-group pb-0">
                                                                            <div class="col-12 text-center">
                                                                                <label for="contact_livraison">Contact</label>
                                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                            </div>
                                                                        <div class="input-group input-group-merge @error('contact_livraison')  is-invalid @enderror">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i class="mr-1 flag-icon flag-icon-cm"></i>+237</span>
                                                                            </div>
                                                                                <input type="text" class="@error('contact_livraison')  is-invalid @enderror form-control" placeholder="Contact de livraison" id="contact_livraison" onkeyup="phone('contact_livraison')" name="contact_livraison2" value="{{old('contact_livraison2') == null ? explode('*/*',$commande->adresse_livraison)[0] : old('contact_livraison2')}}" required />
                                                                            </div>
                                                                            <input type="text" id="contact_livraison2" name="contact_livraison" hidden/>
                                                                            @error('contact_livraison') 
                                                                                    <small class="alert alert-danger"> {{$message}} </small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-5 col-12">
                                                                        <div class="form-group">
                                                                            <div class="col-12 text-center">
                                                                                <label for="lieu_livraison">Lieu</label>
                                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                            </div>
                                                                            <div class="input-group input-group-merge @error('lieu_livraison')  is-invalid @enderror">
                                                                                <div class="input-group-prepend">
                                                                                    <span class="input-group-text"><i data-feather="map-pin"></i></span>
                                                                                </div>
                                                                                <input type="text" class="@error('lieu_livraison')  is-invalid @enderror form-control" placeholder="Quartier" id="lieu_livraison" name="lieu_livraison" value="{{old('lieu_livraison') == null ? explode('*/*',$commande->adresse_livraison)[1] : old('lieu_livraison')}}" required />
                                                                            </div>
                                                                            
                                                                            @error('lieu_livraison') 
                                                                                    <small class="alert alert-danger"> {{$message}} </small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    @php
                                                                         $description = isset(explode('*/*',$commande->adresse_livraison)[2]) ? explode('*/*',$commande->adresse_livraison)[2] : '';
                                                                    @endphp
                                                                    <div class="col-12  mx-auto">
                                                                        <div class="form-group">
                                                                            <label for="description_livraison">Description du lieu de livraison</label>
                                                                            <span class="text-info cursor-pointer" title="Optionnel">Optionnel</span>
                                                                            <textarea class=" @error('description_livraison')  is-invalid @enderror form-control" placeholder="Décrire le Lieu de livraison" rows="3" id="description_livraison" name="description_livraison">{{old('description_livraison') == null ? $description : old('description_livraison')}}</textarea>
                                                                            @error('description_livraison') 
                                                                                    <small class="alert alert-danger"> {{$message}} </small>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label for="montant_livraison">Montant_livraison</label>
                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                <div class="input-group input-group-merge @error('montant_livraison')  is-invalid @enderror">
                                                                    <input type="text" class="@error('montant_livraison')  is-invalid @enderror form-control numeral-mask" placeholder="10,000" id="montant_livraison" name="montant_livraison2" onkeyup="remplir_montant('montant_livraison')" value="{{old('montant_livraison') == null ? $commande->montant_livraison : old('montant_livraison')}}" required />
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
                                                        {{-- <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label for="mode_de_paiement"> Le coursier récupère l'argent?</label>
                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                <div class="form-group">
                                                                    <select class=" @error('mode_de_paiement')  is-invalid @enderror select2 form-control" id="mode_de_paiement" name="mode_de_paiement" required />
                                                                        <option value="">Choisir un mode</option>
                                                                        @foreach($modes_de_paiement as $mode_de_paiement)
                                                                            <option
                                                                                value="{{$mode_de_paiement}}"
                                                                                {{$mode_de_paiement == old('mode_de_paiement') ? 'selected' : ''}} {{old('mode_de_paiement') == null && $mode_de_paiement == $commande->mode_de_paiement ? 'selected' : ''}} >
                                                                                {{$mode_de_paiement == 'coursier' ? 'oui' : ''}}
                                                                                {{$mode_de_paiement == 'speedex' ? 'Non' : ''}}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                @error('mode_de_paiement') 
                                                                        <small class="alert alert-danger"> {{$message}} </small>
                                                                @enderror
                                                                </div>
                                                            </div>
                                                        </div> --}}
                                                        <div class="col-md-6 col-12  mx-auto">
                                                            <div class="form-group">
                                                                <label for="date">Date de livraison</label>
                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                <div class="input-group input-group-merge @error('date')  is-invalid @enderror">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i class="mr-1" data-feather="calendar"></i></span>
                                                                    </div>
                                                                    <input class="form-control flatpickr-human-friendly @error('date')  is-invalid @enderror "  placeholder="January 01, 2024" tabindex="0" type="text" id="date" readonly="readonly" name="date" required value="{{old('date_livraison') == null ? $commande->date_livraison : old('date_livraison')}}">
                                                                </div>
                                                                @error('date') 
                                                                        <small class="alert alert-danger"> {{$message}} </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <div class="d-none d-md-block text-center col-bg-5 col-lg-8 col-md-12 col-12 mx-auto">
                                                        <button type="reset" onclick="tout()" class="btn col-8 col-sm-7 col-lg-5  col-xl-3 col-md-4 btn-danger mt-1 mt-md-2 mr-md-4">
                                                            <i class="mr-1" data-feather='x'></i> Effacer
                                                        </button>
                                                        <button type="submit" class="btn col-8 col-sm-7 col-lg-5 col-xl-3 col-md-4 btn-success mt-1 mt-md-2">
                                                            <i class="mr-1" data-feather='download'></i> Enregistrer
                                                        </button>
                                                    </div>
                                                    <div class="d-block d-md-none text-center col-bg-5 col-lg-8 col-md-12 col-12 mx-auto">
                                                        <button type="submit" class="btn col-8 col-sm-7 col-lg-5  col-xl-3 col-md-4 btn-success mt-1 mt-md-2 mr-md-2">
                                                            <i class="mr-1" data-feather='download'></i> Enregistrer
                                                        </button>
                                                        <button type="reset" onclick="tout()" class="btn col-8 col-sm-7 col-lg-5 col-xl-3 col-md-4 btn-danger mt-1 mt-md-2">
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
                </section>
                <!-- / account setting page -->

            </div>
        </div>
    </div>
    <!-- END: Content-->
@endsection

@section('javascript')
    <script type="text/javascript">
        document.querySelector('#Commandes')?.classList.add('active');
        function remplir_montant(id){
            var phone_number2 = document.querySelector('#'+id+'2'),
                phone_number = document.querySelector('#'+id).value.replaceAll(',','');
            phone_number2.value = phone_number.replaceAll(',','')
        }
        function  phone(id){
            var phone_number2 = document.querySelector('#'+id+'2'),
                phone_number = document.querySelector('#'+id).value.replaceAll(' ','');
            if(parseInt(phone_number).toString().length == 9 && phone_number[0] == '6'){
                document.querySelector('#'+id).value = phone_number[0]+' '+phone_number[1]+''+phone_number[2]+' '+phone_number[3]+''+phone_number[4]+' '+phone_number[5]+''+phone_number[6]+' '+phone_number[7]+''+phone_number[8]
            }else{
                document.querySelector('#'+id).value = phone_number.replaceAll(' ','')
            }
            phone_number2.value = document.querySelector('#'+id).value.replaceAll(' ','');
        }
        function tout(){
            setTimeout(function(){       
                phone('contact_colis');
                phone('contact_livraison');
                remplir_montant('montant_livraison');
            },1)
        }
        phone('contact_colis');
        phone('contact_livraison');
        remplir_montant('montant_livraison');
    </script>
@endsection