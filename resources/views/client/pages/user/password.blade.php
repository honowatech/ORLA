@extends('client/templates/template')
@section('title')
     {{'Changer le mot de Passe'}}
@endsection
@section('contenu')
@if(isset($user->client_utilisateur->infos_perso->cni) && $user->client_utilisateur->infos_perso->cni != null)
    <div class="content app-content bg-light-success border">
@else
    <div class="content app-content bg-light-danger border">
@endif
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Changer le mot de Passe</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                                    </li>
                                    <li class="breadcrumb-item cursor pointer active">Changer le mot de Passe
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
                    <div class="my-1">
                        <!-- right content section -->
                        <div class="col-md-8 p-0 mx-auto">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Changer le mot de passe
                                        <small class="text-muted ml-md-1">
                                            @if(isset($user->client_utilisateur->infos_perso->cni) && $user->client_utilisateur->infos_perso->cni != null)
                                            <div class="bg-light-success border text-center d-inline-block px-1">
                                                Authentifié
                                            </div>
                                            @else
                                            <div class="bg-light-danger border text-center d-inline-block px-1" title="Veuillez contacter un responsable pour l'Authentification">
                                                Non authentifié
                                            </div>
                                            @endif
                                        </small>
                                    </h4>
                                </div>
                                <div class="card-body p-0 ">
                                    <div class="tab-content">
                                        <!-- general tab -->
                                        <div role="tabpanel" class="tab-pane active" id="account-vertical-general" aria-labelledby="account-pill-general" aria-expanded="true">
                                            <!-- header media -->
                                            <div class="text-center mx-auto  mb-2">
                                                @php
                                                    $plus = auth()->user()->client_utilisateur->type_client == 1 ? '_entreprise' : '';
                                                    $path = 'app-assets/images/avatars/client'.$plus.'.jpeg';
                                                @endphp
                                                <a class="d-none d-md-block " href="javascript:void(0);">
                                                    <img src="{{asset($path)}}" height="100" width="100" alt="User avatar" />
                                                </a>
                                                <a class="d-block d-md-none " href="javascript:void(0);">
                                                    <img src="{{asset($path)}}" height="80" width="80" alt="User avatar" />
                                                </a>
                                            </div>
                                            <!--/ header media -->

                                            <!-- form -->
                                            <form class="form" action="{{route('Clientpassword.update',Auth::user()->id)}}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="">
                                                    <div class="col-md-10 mx-auto col-12 col-lg-8">
                                                        <div class="form-group">
                                                                <label for="password">Mot de passe actuel</label>
                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                            <div class="form-group">
                                                                <div class="input-group input-group-merge form-password-toggle @error('password') is-invalid @enderror">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                                    </div>
                                                                    <input type="password" autocomplete="" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Mot de Passe" value="" name="password" aria-describedby="basic-default-password1"  required/>
                                                                </div>
                                                                @error('password')
                                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-10 mx-auto col-12 col-lg-8">
                                                        <div class="form-group">
                                                                <label for="password_new">Nouveau mot de passe </label>
                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                            <div class="form-group">
                                                                <div class="input-group input-group-merge form-password-toggle @error('password_new') is-invalid @enderror">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                                    </div>
                                                                    <input type="password" oninput="samepass('password_new','password_confirm')" autocomplete="" class="form-control @error('password_new') is-invalid @enderror" id="password_new" placeholder="Mot de Passe" value="" name="password_new" aria-describedby="basic-default-password1"  required/>
                                                                </div>
                                                                @error('password_new')
                                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-10 mx-auto col-12 col-lg-8">
                                                        <div class="form-group">
                                                                <label for="password_confirm">Confirmer le mot de passe </label>
                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                            <div class="form-group">
                                                                <div class="input-group input-group-merge form-password-toggle @error('password_confirm') is-invalid @enderror">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                                    </div>
                                                                    <input type="password" oninput="samepass('password_new','password_confirm')" autocomplete="" class="form-control @error('password_confirm') is-invalid @enderror" id="password_confirm" placeholder="Mot de Passe" value="" name="password_confirm" aria-describedby="basic-default-password1"  required/>
                                                                </div>
                                                                    <small class="alert alert-danger" id="password_confirmerror" hidden>Les mots de passes ne correspondent pas</small>
                                                                    <small class="alert alert-success" id="password_confirmsuccess" hidden>Les mots de passes correspondent</small>
                                                                @error('password_confirm')
                                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="pt-2 col-sm-11 mx-auto col-12 col-lg-10 col-xl-8 row justify-content-around text-center">
                                                        <button type="reset" onclick="setTimeout(function(){phone()},2)" class="btn btn-danger  waves-effect waves-float waves-light">
                                                            <i data-feather="x" style="width: 20px; height: 20px;"></i>
                                                        </button>
                                                        <button type="submit" class="btn btn-success waves-effect waves-float waves-light ml-bg-3">
                                                            <i data-feather='check' style="width: 20px; height: 20px;"></i>
                                                        </button>
                                                    </div>
                                                    {{-- <div class="d-none d-lg-block col-md-10 col-lg-10 col-xl-7 col-12 mt-lg-4 mt-2 mb-2 mx-auto text-center">
                                                        <button type="reset" class="col-lg-5 col-bg-5 col-12 btn round btn-danger mr-md-3 mr-2  waves-effect">
                                                            <i class="mr-1" data-feather="x"></i> Effacer
                                                        </button>
                                                        <button type="submit" class="col-lg-5 col-bg-4 col-12 round submit_button btn btn-success waves-effect waves-float waves-light mt-2 mt-md-0 ml-bg-3">
                                                            <i class="mr-1" data-feather='edit-3'></i> Changer
                                                        </button>
                                                    </div>

                                                    <div class="d-block d-lg-none col-md-10 col-lg-8 col-bg-8 col-12 mt-lg-2 mt-2 mb-2 mx-auto text-center">
                                                        <button type="submit" class="col-lg-5 col-bg-4 col-8 btn round submit_button btn-success waves-effect">
                                                            <i class="mr-1" data-feather='edit-3'></i> Changer
                                                        </button>
                                                        <button type="reset" class="col-lg-5 col-bg-5 col-8 round btn btn-danger waves-effect waves-float waves-light mt-2 mt-lg-0 ">
                                                            <i class="mr-1" data-feather="x"></i> Effacer
                                                        </button>
                                                    </div> --}}
                                                </div>
                                            </form>
                                            <!--/ form -->
                                        </div>
                                        <!--/ general tab -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ right content section -->
                    </div>
            </section>
        </div>
    </div>
                <!-- / account setting page -->
</div>

@endsection

@section('javascript')
    <script type="text/javascript">
        function samepass(id_new,id_confirm){
            var input_new = document.querySelector('#'+id_new),
                input_confirm = document.querySelector('#'+id_confirm),
                error_confirm = document.querySelector('#'+id_confirm+'error'),
                success_confirm = document.querySelector('#'+id_confirm+'success');
            if (input_new.value == '' || input_confirm.value == '') {
                error_confirm.hidden = true;
                success_confirm.hidden = true;
                $('.submit_button').prop("disabled", true);
            }else if(input_new.value == input_confirm.value){
                error_confirm.hidden = true;
                success_confirm.hidden = false;
                $('.submit_button').prop("disabled", false);
            }else{
                error_confirm.hidden = false;
                success_confirm.hidden = true;
                $('.submit_button').prop("disabled", true);
            }
        }
        document.querySelector('#password').classList.add('active');
    </script>
@endsection