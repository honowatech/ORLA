@extends('admin/templates/template')

@section('title')
{{'Créer un Utilisateur '}}
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
                        <h2 class="content-header-title float-left mb-0">Ajouter un Utilisateur</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item active">Ajouter un Utilisateur
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
                                    <form class="form" action="{{route('users.store')}}" method="POST">
                                    	@csrf
                                        <div class="row">
                                        	<div class="col-md-6 col-12 mx-auto">
                                                <div class="form-group">
                                                        <label for="noms">Noms et Prénoms</label>
                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge @error('noms') is-invalid @enderror">
                                                            <div class="input-group-prepend ">
                                                                <span class="input-group-text"><i data-feather="user"></i></span>
                                                            </div>
                                                            <input type="text" id="noms" class="form-control @error('noms') is-invalid @enderror" name="noms" placeholder="Noms et Prénoms" value="{{ old('noms') }}" / required>
                                                        </div>
                                                        @error('noms')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 mx-auto">
                                                <div class="form-group">
                                                        <label for="email-icon">Email</label>
                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge  @error('email') is-invalid @enderror">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i data-feather="mail"></i></span>
                                                            </div>
                                                            <input type="email" autocomplete="" id="email-icon" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ old('email') }}"  required/>
                                                        </div>
                                                        @error('email')
                                                            	<small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 mx-auto">
                                                <div class="form-group">
                                                    <label for="password">Mot de Passe</label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
						                                <div class="input-group input-group-merge form-password-toggle @error('password') is-invalid @enderror">
						                                	<div class="input-group-prepend">
						                                		<span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
	                                                        </div>
						                                    <input type="password" autocomplete="" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Mot de Passe" value="" name="password"aria-describedby="basic-default-password1"  required/>
						                                </div>
						                                @error('password') 
						                                	<small class="alert alert-danger"> {{$message}} </small>
						                                @enderror
						                            </div>
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
		                                                <input type="number" class="@error('telephone')  is-invalid @enderror form-control" placeholder="123456789" id="phone_number" name="telephone" value="{{old('telephone')}}" required/>
		                                            </div>
		                                            @error('telephone') 
						                                	<small class="alert alert-danger"> {{$message}} </small>
						                            @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 mx-auto">
                                                <div class="form-group">
		                                            <label for="type_user">Type d'Utilisateur</label>
		                                            <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
		                                            <div class="form-group">
		                                                <select class="select2 form-control" id="type_user" name="type_user" onchange="choose()"  required>
		                                                	@foreach($typesUser as $typeUser)
		                                                		<option
		                                                			value="{{$typeUser->id}}"
		                                                			{{$typeUser->id == old('type_user') ? 'selected' : ''}}>
		                                                			{{$typeUser->libelle}}
		                                                		</option>
		                                                	@endforeach
		                                                </select>
		                                            </div>
					                            </div>
                                            </div>
                                            <div class="col-md-6 col-12 mx-auto">
                                                <div class="form-group">
		                                            <label for="id_compte_associe">Compte Lié</label>
		                                            <div class="form-group mb-0" id="div_id_compte_associe"  hidden>
		                                                <select class="@error('id_compte_associe')  is-invalid @enderror select2 form-control" id="id_compte_associe" name="id_compte_associe" disabled>
		                                                </select>
		                                            </div>
		                                            <div class="text-center">
				                                        <div class="spinner-border text-info" role="status" id="spinner">
				                                            <span class="sr-only"></span>
				                                        </div>
				                                    </div>
				                                    @error('id_compte_associe') 
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
		document.querySelector('#UserAjouter')?.classList.add('active');
		function choose(){
			var type_user = document.querySelector('#type_user'),
				div_id_compte_associe = document.querySelector('#div_id_compte_associe'),
				id_compte_associe = document.querySelector('#id_compte_associe'),
				spinner = document.querySelector('#spinner');
			id_compte_associe.disabled = false;
			spinner.hidden = false;
			id_compte_associe.required = false;
			div_id_compte_associe.hidden = true;
			jQuery.ajax({
                    url: '{{route('compteLie')}}',
                    type : 'POST',
                    data : { type_user : type_user.value, '_token' : "{{ csrf_token() }}" },
                    success: function(response)
                    {
                    	spinner.hidden = true;
						div_id_compte_associe.hidden = false;
						id_compte_associe.innerHTML = response;
						if(type_user.value == 1){
							id_compte_associe.disabled = true;
						}else{
							id_compte_associe.required = true;
						}
                    },
                    error: function(){
                        alert(" Un problème est survenu veuillez rééseiller plus tard !!!");
                    }
            });
        }
        choose()
	</script>
@endsection