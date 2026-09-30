@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')
@section('title')
{{'Créer un Livreur '}}
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
                        <h2 class="content-header-title float-left mb-0">Ajouter un Livreur</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item active">Ajouter un Livreur
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
            </div>
	    <div class="content-body">
<section id="multiple-column-form">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Formulaire</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form" action="{{route('coursiers.store')}}" method="POST">
                                    	@csrf
                                        <div class="row col-lg-8 col-md-10 mx-auto">
                                            <div class="col-md-6 col-12 user_div">
                                                    <label for="noms">Nom(s)</label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                <div class="form-group">
                                                    <div class="input-group input-group-merge @error('noms') is-invalid @enderror">
                                                        <div class="input-group-prepend ">
                                                            <span class="input-group-text"><i data-feather='at-sign'></i></span>
                                                        </div>
                                                        <input type="text" id="noms" class="form-control @error('noms') is-invalid @enderror" name="noms" placeholder="Nom(s)" value="{{ old('noms') }}" / required>
                                                    </div>
                                                    @error('noms')
                                                    <small class="alert alert-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 user_div">
                                                    <label for="prenoms">Prénom(s)</label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                <div class="form-group">
                                                    <div class="input-group input-group-merge @error('prenoms') is-invalid @enderror">
                                                        <div class="input-group-prepend ">
                                                            <span class="input-group-text"><i data-feather='at-sign'></i></span>
                                                        </div>
                                                        <input type="text" id="prenoms" class="form-control @error('prenoms') is-invalid @enderror" name="prenoms" placeholder="Prénom(s)" value="{{ old('prenoms') }}" / required>
                                                    </div>
                                                    @error('prenoms')
                                                    <small class="alert alert-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 mx-auto">
                                                <div class="form-group">
                                                    <label for="phone_number">Phone Number</label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
		                                            <div class="input-group input-group-merge @error('telephone')  is-invalid @enderror">
		                                                <div class="input-group-prepend">
		                                                    <span class="input-group-text">CMR (+237)</span>
		                                                </div>
		                                                <input type="text" class="@error('telephone')  is-invalid @enderror form-control" placeholder="1 23 45 67 89" id="phone_number" name="telephone2" oninput="phone('phone_number')" value="{{old('telephone2')}}" required/>
		                                            </div>
		                                            @error('telephone') 
						                                	<small class="alert alert-danger"> {{$message}} </small>
						                            @enderror
                                                </div>
                                                <input type="number" hidden class="@error('telephone')  is-invalid @enderror form-control" placeholder="1 23 45 67 89" id="phone_number2" name="telephone" value="{{old('telephone')}}" required/>
                                            </div>
                                            <div class="col-md-6 col-12 mx-auto">
                                                <div class="form-group">
                                                    <label for="id_ville"> Ville </label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <select class="disabled @error('id_ville')  is-invalid @enderror select2 form-control" id="id_ville" name="id_ville" @if(filter(['routeur','superviseur_ville'],auth()->user()) == 'true') disabled @endif required>
                                                            @if(filter(['routeur','superviseur_ville'],auth()->user()) == 'true')
                                                                <option selected>{{auth()->user()->ville->libelle}}</option>
                                                                <input type="hidden" name="id_ville" value="{{auth()->user()->id_ville}}">
                                                            @else
                                                                <option value="">Choisir une Ville</option>
                                                                @foreach($villes as $ville)
                                                                    <option
                                                                        value="{{$ville->id}}"
                                                                        {{$ville->id == old('id_ville') ? 'selected' : ''}}>
                                                                        {{$ville->libelle}}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    @error('id_ville') 
                                                            <small class="alert alert-danger"> {{$message}} </small>
                                                    @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 user_div" >
                                                <div class="form-group">
                                                        <label for="email-icon">Email</label>
                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge  @error('email') is-invalid @enderror">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i data-feather="mail"></i></span>
                                                            </div>
                                                            <input type="email" id="email-icon" class="user_input form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ old('email') }}" required />
                                                        </div>
                                                        @error('email')
                                                                <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 user_div" > 
                                                <div class="form-group">
                                                    <label for="password">Mot de Passe</label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge form-password-toggle @error('password') is-invalid @enderror">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                            </div>
                                                            <input type="password" class="user_input form-control @error('password') is-invalid @enderror" id="password" placeholder="Mot de Passe" value="" name="password"aria-describedby="basic-default-password1" required />
                                                        </div>
                                                        @error('password') 
                                                            <small class="alert alert-danger"> {{$message}} </small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="add_user_account" id="add_user_account" value="1">
                                        </div>
                                        <div class="row">
                                            <div class="d-none d-md-block text-center col-bg-5 col-lg-8 col-md-12 col-12 mx-auto">
                                                <button type="reset" class="btn col-8 col-sm-7 col-lg-5  col-xl-4 col-md-4 btn-danger mt-1 mt-md-2 mr-md-4">
                                                    <i class="mr-1" data-feather='x'></i> Effacer
                                                </button>
                                                <button type="submit" class="btn col-8 col-sm-7 col-lg-5 col-xl-4 col-md-4 btn-success mt-1 mt-md-2">
                                                    <i class="mr-1" data-feather='download'></i> Enregistrer
                                                </button>
                                            </div>
                                            <div class="d-block d-md-none text-center col-bg-5 col-lg-8 col-md-12 col-12 mx-auto">
                                                <button type="submit" class="btn col-8 col-sm-7 col-lg-5  col-xl-4 col-md-4 btn-success mt-1 mt-md-2 mr-md-2">
                                                    <i class="mr-1" data-feather='download'></i> Enregistrer
                                                </button>
                                                <button type="reset" class="btn col-8 col-sm-7 col-lg-5 col-xl-4 col-md-4 btn-danger mt-1 mt-md-2">
                                                    <i class="mr-1" data-feather='x'></i> Effacer
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>   
	    </div>
	</div>
</div>

@endsection

@section('javascript')
    <script type="text/javascript">
        document.querySelector('#CoursierAjouter')?.classList.add('active');
    </script>
@endsection