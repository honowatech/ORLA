@extends('admin/templates/template')

@section('title')
{{'Editer '}} {{$client->noms.' '.$client->Prenoms}}
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
                        <h2 class="content-header-title float-left mb-0">Editer {{$client->noms.' '.$client->Prenoms}}</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{route('clients.index')}}">Liste des clients</a>
                            </li>
                            <li class="breadcrumb-item active">Editer {{$client->noms.' '.$client->Prenoms}}
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
            </div>

            <div class="content-body">
                <!-- users edit start -->
                <section class="app-user-edit">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-pills" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link d-flex align-items-center @if(!session()->has('infos_perso_error')&&!session()->has('infos_perso')) active @endif" id="account-tab" data-toggle="tab" href="#account" aria-controls="account" role="tab" aria-selected="true">
                                        <i data-feather="user"></i><ba class="d-none d-sm-block">Compte ({{$client->type__client->libelle}})</ba>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex align-items-center @if(session()->has('infos_perso_error')||session()->has('infos_perso')) active @endif" id="information-tab" data-toggle="tab" href="#information" aria-controls="information" role="tab" aria-selected="false">
                                        <i data-feather="info"></i><ba class="d-none d-sm-block">Informations Personnelles</ba>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <!-- Account Tab starts -->
                                <div class="tab-pane @if(!session()->has('infos_perso_error')&&!session()->has('infos_perso')) active @endif" id="account" aria-labelledby="account-tab" role="tabpanel">
                                    <!-- users edit media object start -->
                                    <div class="media mb-2">
                                        <img src="{{asset('app-assets/images/avatars/user.png')}}" alt="users avatar" class="user-avatar users-avatar-shadow rounded mr-2 my-25 cursor-pointer" height="90" width="90" />
                                        <div class="media-body m-auto">
                                            <h2>{{$client->noms}} {{$client->Prenoms}}</h2>
                                            <span>Client <b>({{$client->type__client->libelle}})</b></span>
                                            <div> +237 <b style="letter-spacing: 2px;">{{$client->telephone}}</b></div>
                                        </div>
                                    </div>
                                    <!-- users edit media object ends -->
                                    <!-- users edit account form start -->
                                    <form class="form-validate" action="{{route('clients.update',$client->id)}}" method="POST">
                                        <div class="row">
                                            <div class="card-body">
                                                <div class="form" >
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="row">
                                                        <div class="col-lg-4 col-md-6 mx-auto">
                                                            <div class="form-group">
                                                                    <label for="noms">Nom(s)</label>
                                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                <div class="form-group">
                                                                    <div class="input-group input-group-merge @error('noms') is-invalid @enderror">
                                                                        <div class="input-group-prepend ">
                                                                            <span class="input-group-text"><i data-feather='at-sign'></i></span>
                                                                        </div>
                                                                        <input type="text" id="noms" class="form-control @error('noms') is-invalid @enderror" name="noms" placeholder="Nom(s)" value="{{ old('noms') == null ? $client->noms : old('noms') }}" / required>
                                                                    </div>
                                                                    @error('noms')
                                                                    <small class="alert alert-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-6 mx-auto">
                                                            <div class="form-group">
                                                                    <label for="prenoms">Prénom(s)</label>
                                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                <div class="form-group">
                                                                    <div class="input-group input-group-merge @error('prenoms') is-invalid @enderror">
                                                                        <div class="input-group-prepend ">
                                                                            <span class="input-group-text"><i data-feather='at-sign'></i></span>
                                                                        </div>
                                                                        <input type="text" id="prenoms" class="form-control @error('prenoms') is-invalid @enderror" name="prenoms" placeholder="Prénom(s)" value="{{ old('prenoms') == null ? $client->Prenoms : old('prenoms') }}" / required>
                                                                    </div>
                                                                    @error('prenoms')
                                                                    <small class="alert alert-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-6 mx-auto">
                                                            <div class="form-group">
                                                                <label for="phone_number">Numéro de Téléphone</label>
                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                                <div class="input-group input-group-merge @error('telephone')  is-invalid @enderror">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">CMR +237</span>
                                                                    </div>
                                                                    <input type="number" class="@error('telephone')  is-invalid @enderror form-control" placeholder="123456789" id="phone_number" name="telephone" value="{{ old('telephone') == null ? $client->telephone : old('telephone') }}" required/>
                                                                </div>
                                                                @error('telephone') 
                                                                        <small class="alert alert-danger"> {{$message}} </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="d-none d-md-block text-center col-bg-5 col-lg-8 col-md-12 col-12 mx-auto">
                                                            <button type="reset" class="btn col-8 col-sm-7 col-lg-5  col-xl-3 col-md-4 btn-danger mt-1 mt-md-2 mr-md-4">
                                                                <i class="mr-1" data-feather='x'></i> Effacer
                                                            </button>
                                                            <button type="submit" class="btn col-8 col-sm-7 col-lg-5 col-xl-3 col-md-4 btn-success mt-1 mt-md-2">
                                                                <i class="mr-1" data-feather='download'></i> Enregistrer
                                                            </button>
                                                        </div>
                                                        <div class="d-block d-md-none text-center col-bg-5 col-lg-8 col-md-12 col-12 mx-auto">
                                                            <button type="reset" class="btn col-8 col-sm-7 col-lg-5  col-xl-3 col-md-4 btn-success mt-1 mt-md-2 mr-md-2">
                                                                <i class="mr-1" data-feather='download'></i> Enregistrer
                                                            </button>
                                                            <button type="submit" class="btn col-8 col-sm-7 col-lg-5 col-xl-3 col-md-4 btn-danger mt-1 mt-md-2">
                                                                <i class="mr-1" data-feather='x'></i> Effacer
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- users edit account form ends -->
                                </div>
                                <!-- Account Tab ends -->
                                <!-- Information Tab starts -->
                                <div class="tab-pane @if(session()->has('infos_perso_error')||session()->has('infos_perso')) active @endif" id="information" aria-labelledby="information-tab" role="tabpanel">
                                    <!-- users edit Info form start -->
                                    <form class="form-validate" action="{{route('informations_personnels.store')}}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$client->id}}">
                                        <input type="hidden" name="type" value="client">
                                        <div class="row mt-1">
                                            <div class="col-12">
                                                <h4 class="mb-1">
                                                    <i data-feather="user" class="font-medium-4 mr-25"></i>
                                                    <span class="align-middle">Information Personnelle</span>
                                                </h4>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="form-group">
                                                    <label for="phone_number2">Second Numéro de Téléphone</label>
                                                    <span class="text-info cursor-pointer" title="Ce champ est Optionnel">Optionnel</span>
                                                    <div class="input-group input-group-merge @error('telephone2')  is-invalid @enderror">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">CMR +237</span>
                                                        </div>
                                                        <input type="number" autofocus class="@error('telephone2')  is-invalid @enderror form-control" placeholder="123456789" id="phone_number2" name="telephone2" value="{{ old('telephone2') == null ? $client->infos_perso->telephone2 : old('telephone2') }}"/>
                                                    </div>
                                                        @error('telephone2') 
                                                            <small class="alert alert-danger"> {{$message}} </small>
                                                        @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                    <div class="form-group">
                                                        <label for="fp-default">Date de Naissance</label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span> 
                                                    <div class="input-group input-group-merge @error('date_naissance')  is-invalid @enderror">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i data-feather='calendar'></i></span>
                                                        </div>
                                                        <input type="text" id="fp-default" placeholder="Année-Mois-Jour" name="date_naissance" readonly="readonly" class="@error('date_naissance')  is-invalid @enderror flatpickr-human-friendly flatpickr-input form-control" required value="{{ old('date_naissance') == null ? $client->infos_perso->date_naissance : old('date_naissance') }}"/>
                                                    </div>
                                                        @error('date_naissance') 
                                                            <small class="alert alert-danger"> {{$message}} </small>
                                                        @enderror
                                                </div>  
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="form-group">
                                                    <label for="lieu_naissance">Lieu de Naissance</label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span> 
                                                    <div class="input-group input-group-merge @error('lieu_naissance')  is-invalid @enderror">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i data-feather='map-pin'></i></span>
                                                        </div>
                                                    <input id="lieu_naissance" type="text" class="form-control @error('lieu_naissance')  is-invalid @enderror" placeholder="Lieu de Naissance" name="lieu_naissance" required value="{{ old('lieu_naissance') == null ? $client->infos_perso->lieu_naissance : old('lieu_naissance') }}"/>
                                                    </div>
                                                        @error('lieu_naissance') 
                                                            <small class="alert alert-danger"> {{$message}} </small>
                                                        @enderror

                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="cni">Numéro de CNI ou Récipicé</label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="input-group input-group-merge @error('cni')  is-invalid @enderror">
                                                    <div class="input-group-prepend ">
                                                        <span class="input-group-text"><i data-feather='shield'></i></span>
                                                    </div>
                                                        <input type="text" class="@error('cni')  is-invalid @enderror form-control" placeholder="Numéro de CNI ou Récipicé" id="cni" name="cni" value="{{ old('cni') == null ? $client->infos_perso->cni : old('cni') }}" required />
                                                    </div>
                                                        @error('cni') 
                                                            <small class="alert alert-danger"> {{$message}} </small>
                                                        @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="lieu_delivrance">Lieu de Délivrance</label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span> 
                                                    <div class="input-group input-group-merge @error('lieu_delivrance')  is-invalid @enderror">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i data-feather='map-pin'></i></span>
                                                        </div>
                                                    <input id="lieu_delivrance" type="text" class="form-control @error('lieu_delivrance')  is-invalid @enderror" placeholder="Lieu de Naissance" name="lieu_delivrance" required value="{{ old('lieu_delivrance') == null ? $client->infos_perso->lieu_delivrance : old('lieu_delivrance') }}"/>
                                                    </div>
                                                        @error('lieu_delivrance') 
                                                            <small class="alert alert-danger"> {{$message}} </small>
                                                        @enderror

                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="date_delivrance">Date de Délivrance</label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span> 
                                                    <div class="input-group input-group-merge @error('date_delivrance')  is-invalid @enderror">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i data-feather='calendar'></i></span>
                                                        </div>
                                                        <input type="text" id="date_delivrance" placeholder="Année-Mois-Jour" name="date_delivrance" readonly="readonly" class="flatpickr-human-friendly flatpickr-input form-control @error('date_delivrance')  is-invalid @enderror" value="{{ old('date_delivrance') == null ? $client->infos_perso->date_delivrance : old('date_delivrance') }}" required />
                                                    </div>
                                                        @error('date_delivrance') 
                                                            <small class="alert alert-danger"> {{$message}} </small>
                                                        @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="date_expiration">Date d'Expiration</label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="input-group input-group-merge @error('date_expiration')  is-invalid @enderror">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i data-feather='calendar'></i></span>
                                                        </div>
                                                        <input type="text" id="date_expiration" placeholder="Année-Mois-Jour" name="date_expiration" readonly="readonly" class="form-control flatpickr-human-friendly flatpickr-input @error('date_expiration')  is-invalid @enderror" value="{{ old('date_expiration') == null ? $client->infos_perso->date_expiration : old('date_expiration') }}" required />
                                                    </div>
                                                        @error('date_expiration') 
                                                            <small class="alert alert-danger"> {{$message}} </small>
                                                        @enderror
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <h4 class="mb-1 mt-2">
                                                    <i data-feather="map-pin" class="font-medium-4 mr-25"></i>
                                                    <span class="align-middle">Addresse</span>
                                                </h4>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="form-group">
                                                    <label for="pays">Pays</label>
                                                    <span class="text-primary cursor-pointer" title="Ce champ est remplit Automatiquement">Automatique</span> 
                                                    <div class="input-group input-group-merge @error('id_quartier_associe')  is-valid @enderror">
                                                        <div class="input-group-prepend">
                                                                <span class="input-group-text @error('id_quartier_associe')  text-success @enderror"><i data-feather='flag'></i></span>
                                                        </div>
                                                        <input type="text" class="form-control @error('id_quartier_associe')  is-valid text-success @enderror text-center" id="pays"  name="pays" value="Cameroun" disabled>
                                                    </div>
                                                    @error('id_quartier_associe')
                                                        <small class="alert alert-success"> Le Pays est valide </small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="form-group">
                                                    <label for="id_ville">Choisir Une Ville </label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <select class=" @error('id_ville')  is-invalid @enderror select2 form-control" id="id_ville" name="id_ville" onchange="choose()">
                                                            @php
                                                                if (isset($client->infos_perso->quartier->libelle)) {
                                                                    $id_ville = $client->infos_perso->quartier->ville->id;
                                                                    $valeur_ville = $client->infos_perso->quartier->ville->libelle.'-(Enregistrée)';
                                                                }else {
                                                                    $id_ville = 0;
                                                                    $valeur_ville = 'Choisir une ville';
                                                                }
                                                            @endphp
                                                            <option value="{{$id_ville}}">{{$valeur_ville}}</option>
                                                            @foreach($villes as $ville)
                                                                <option
                                                                    value="{{$ville->id}}"
                                                                    {{$ville->id == old('id_ville') ? 'selected' : ''}}>
                                                                    {{$ville->libelle}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @error('id_ville')
                                                        <small class="alert alert-danger"> {{$message}} </small>
                                                    @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="form-group">
                                                    <label for="id_quartier_associe">Choisir un Quartier</label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group mb-0" id="div_id_quartier_associe">
                                                        <select class=" @error('id_quartier_associe')  is-invalid @enderror select2 form-control" id="id_quartier_associe"  name="id_quartier_associe" required>
                                                            @php
                                                                if (isset($client->infos_perso->quartier->libelle)) {
                                                                    $valeur = $client->infos_perso->quartier->ville->libelle.'-'.$client->infos_perso->quartier->libelle;
                                                                }else {
                                                                    $valeur = 'Choisir une Ville Ensuite un Quartier';
                                                                }
                                                            @endphp
                                                            <option value="{{ old('id_quartier_associe') == null ? $client->infos_perso->id_quartier : old('id_quartier_associe') }}">{{ old('id_quartier_associe') == null ? $valeur : 'Ancien choix' }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="text-center">
                                                        <div class="spinner-border text-info" role="status" id="spinner" hidden>
                                                            <span class="sr-only"></span>
                                                        </div>
                                                    </div>
                                                    @error('id_quartier_associe') 
                                                        <small class="alert alert-danger"> {{$message}} </small>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-12 col-md-12">
                                                <div class="form-group">
                                                    <label for="localisation">Localisation</label>
                                                    <span class="text-info cursor-pointer" title="Ce champ est Optionnel">Optionnel</span>
                                                    <textarea data-length="255" name="localisation" class="form-control char-textarea @error('localisation')  is-invalid @enderror" id="textarea-counter" rows="3" placeholder="Décrire la position exacte de votre Local..." >{{ old('localisation') == null ? $client->infos_perso->localisation : old('localisation') }}</textarea>
                                                    <small class="textarea-counter-value float-right"><span class="char-count"> 0 </span> / 255 </small>
                                                    @error('localisation') 
                                                            <small class="alert alert-danger"> {{$message}} </small>
                                                    @enderror
                                                </div>  
                                            </div>
                                            <div class="d-none d-md-block text-center col-bg-5 col-lg-8 col-md-12 col-12 mx-auto">
                                                <button type="reset" class="btn col-8 col-sm-7 col-lg-5  col-xl-3 col-md-4 btn-danger mt-1 mt-md-2 mr-md-4">
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
                                                <button type="reset" class="btn col-8 col-sm-7 col-lg-5 col-xl-3 col-md-4 btn-danger mt-1 mt-md-2">
                                                    <i class="mr-1" data-feather='x'></i> Effacer
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- users edit Info form ends -->
                                </div>
                                <!-- Information Tab ends -->
                            </div>
                        </div>
                    </div>
                </section>
            </div>  
	    </div>
	</div>
</div>

@endsection

@section('javascript')
	<script type="text/javascript">
		document.querySelector('#ClientsListe')?.classList.add('active');
        function choose(){
            var id_ville = document.querySelector('#id_ville'),
                div_id_quartier_associe = document.querySelector('#div_id_quartier_associe'),
                id_quartier_associe = document.querySelector('#id_quartier_associe'),
                spinner = document.querySelector('#spinner');
            id_quartier_associe.disabled = false;
            spinner.hidden = false;
            div_id_quartier_associe.hidden = true;
            jQuery.ajax({
                    url: '{{route('quartierLie')}}',
                    type : 'POST',
                    data : { id_ville : id_ville.value, all : 1 , '_token' : "{{ csrf_token() }}" },
                    success: function(response)
                    {
                        spinner.hidden = true;
                        div_id_quartier_associe.hidden = false;
                        id_quartier_associe.innerHTML = response;
                    },
                    error: function(){
                        alert(" Un problème est survenu veuillez rééseiller plus tard !!!");
                    }
            });
        }
        @error('id_quartier_associe') choose() @enderror
        @error('id_ville') choose() @enderror
	</script>
@endsection