@extends('admin/templates/template')

@section('title')
{{'Editer '}} {{$user->noms}}
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
                        <h2 class="content-header-title float-left mb-0">Editer {{$user->noms}}</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{route('users.index')}}">Liste des Utilisateurs</a>
                            </li>
                            <li class="breadcrumb-item active">Editer {{$user->noms}}
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
                                    <form class="form" action="{{route('users.update',$user->id)}}" method="POST">
                                    	@csrf
                                        @method('PATCH')
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
                                                            <input type="text" id="noms" class="form-control @error('noms') is-invalid @enderror" name="noms" placeholder="Noms et Prénoms" value="{{ old('noms') == null ? $user->noms : old('noms') }}" / required>
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
                                                            <input type="email" id="email-icon" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ old('email') == null ? $user->email : old('email') }}"  required/>
                                                        </div>
                                                        @error('email')
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
		                                                <input type="number" class="@error('telephone')  is-invalid @enderror form-control" placeholder="123456789" id="phone_number" name="telephone" value="{{ old('telephone') == null ? $user->telephone : old('telephone') }}" required/>
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
		document.querySelector('#UserListe')?.classList.add('active');
	</script>
@endsection