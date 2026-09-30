@extends('admin/templates/template')

@section('title')
{{'Créer un Coursier '}}
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
                        <h2 class="content-header-title float-left mb-0">Ajouter un Coursier</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item active">Ajouter un Coursier
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
                                        <div class="row">
                                        	<div class="col-md-6 col-12 d-flex space-between p-0">
                                                <div class="col-6 form-group ">
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
                                                <div class="col-6 form-group">
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
                                            <div class="col-md-6 col-12 user_div" hidden >
                                                <div class="form-group">
                                                        <label for="email-icon">Email</label>
                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge  @error('email') is-invalid @enderror">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i data-feather="mail"></i></span>
                                                            </div>
                                                            <input type="email" id="email-icon" class="user_input form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ old('email') }}" />
                                                        </div>
                                                        @error('email')
                                                                <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 user_div" hidden > 
                                                <div class="form-group">
                                                    <label for="password">Mot de Passe</label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge form-password-toggle @error('password') is-invalid @enderror">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                            </div>
                                                            <input type="password" class="user_input form-control @error('password') is-invalid @enderror" id="password" placeholder="Mot de Passe" value="" name="password"aria-describedby="basic-default-password1" />
                                                        </div>
                                                        @error('password') 
                                                            <small class="alert alert-danger"> {{$message}} </small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="add_user_account" id="add_user_account" value="0">
                                            
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
                                            <div class="mt-2 text-center col-bg-5 col-lg-8 col-md-12 col-12 mx-auto">
                                                <div  type="btn" class="btn btn-outline-info mr-2 waves-effect waves-float waves-light mx-auto" onclick="createUserAccount()">Creer Son Compte Utilisateur?</div >
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
        function createUserAccount(){
            var user_div = document.querySelectorAll('.user_div'),
                user_input = document.querySelectorAll('.user_input'),
                add_user_account = document.querySelector('#add_user_account');
                if (add_user_account.value == 1) {
                    add_user_account.value = 0;
                    for (i = 0; i < user_div.length; i++) {
                        user_div[i].hidden = true;
                        user_input[i].required = false;
                    }
                    
                }else{
                    add_user_account.value = 1;
                    for (i = 0; i < user_div.length; i++) {
                        user_div[i].hidden = false;
                        user_input[i].required = true;
                    }
                }
        }
         @if(session()->has('error_user'))
            createUserAccount()
        @endif
    </script>
@endsection