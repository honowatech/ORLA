@extends('coursier/templates/template')
@section('title')
     {{'Editer le Pofil'}}
@endsection
@section('contenu')
@if(isset($user->coursier_utilisateur->infos_perso->cni) && $user->coursier_utilisateur->infos_perso->cni != null)
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
                            <h2 class="content-header-title float-left mb-0">Editer Le Profil</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                                    </li>
                                    <li class="breadcrumb-item cursor pointer active">Editer Le Profil
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
                                    <h4 class="card-title">Editer le Profil du coursier
                                        <small class="text-muted ml-md-1">
                                            @if(isset($user->coursier_utilisateur->infos_perso->cni) && $user->coursier_utilisateur->infos_perso->cni != null)
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
                                            <div class="text-center mx-auto">
                                                <a class="d-none d-md-block " href="javascript:void(0);">
                                                    <img src="{{asset('app-assets/images/avatars/coursier.png')}}" height="100" width="100" alt="User avatar" />
                                                </a>
                                                <a class="d-block d-md-none " href="javascript:void(0);">
                                                    <img src="{{asset('app-assets/images/avatars/coursier.png')}}" height="80" width="80" alt="User avatar" />
                                                </a>
                                            </div>
                                            <!--/ header media -->

                                            <!-- form -->
                                            <form class="form" action="{{route('Coursierusers.update',$user->id)}}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                @php
                                                    $noms = $user->coursier_utilisateur == null ? $user->noms : $user->coursier_utilisateur->noms;
                                                    $prenoms = $user->coursier_utilisateur == null ? : $user->coursier_utilisateur->prenoms;
                                                @endphp
                                                <div class="">
                                                    <div class="col-md-10 col-bg-6 mx-auto col-12 col-lg-8">
                                                        <div class="form-group">
                                                                <label for="noms">Noms</label>
                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                            <div class="form-group">
                                                                <div class="input-group input-group-merge @error('noms') is-invalid @enderror">
                                                                    <div class="input-group-prepend ">
                                                                        <span class="input-group-text"><i data-feather="at-sign"></i></span>
                                                                    </div>
                                                                    <input type="text" id="noms" class="form-control  @error('noms') is-invalid @enderror" name="noms" autofocus placeholder="Noms" value="{{ old('noms') == null ? $noms : old('noms') }}" required/>
                                                                    <div class="input-group-append  ">
                                                                        <span class="input-group-text">
                                                                            <label for="noms" class="p-auto m-auto">
                                                                                <i data-feather="edit"></i>
                                                                            </label>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                @error('noms')
                                                                <small class="alert alert-danger">{{ $message }}</small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                                <label for="prenoms">Prénoms</label>
                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                            <div class="form-group">
                                                                <div class="input-group input-group-merge @error('prenoms') is-invalid @enderror">
                                                                    <div class="input-group-prepend ">
                                                                        <span class="input-group-text"><i data-feather="at-sign"></i></span>
                                                                    </div>
                                                                    <input type="text" id="prenoms" class="form-control  @error('prenoms') is-invalid @enderror" name="prenoms" placeholder="Prénoms" value="{{ old('noms') == null ? $prenoms : old('prenoms') }}" required/>
                                                                    <div class="input-group-append  ">
                                                                        <span class="input-group-text">
                                                                            <label for="prenoms" class="p-auto m-auto">
                                                                                <i data-feather="edit"></i>
                                                                            </label>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                @error('prenoms')
                                                                <small class="alert alert-danger">{{ $message }}</small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-10 col-bg-6 mx-auto col-12 col-lg-8">
                                                        <div class="form-group">
                                                                <label for="email-icon">Email</label>
                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                            <div class="form-group">
                                                                <div class="input-group input-group-merge  @error('email') is-invalid @enderror">
                                                                    <div class="input-group-prepend ">
                                                                        <span class="input-group-text "><i data-feather="mail"></i></span>
                                                                    </div>
                                                                    <input type="email" id="email-icon" class="  form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ old('email') == null ? $user->email : old('email') }}"  required/>
                                                                    <div class="input-group-append  ">
                                                                        <span class="input-group-text">
                                                                            <label for="email-icon" class="p-auto m-auto">
                                                                                <i data-feather="edit"></i>
                                                                            </label>
                                                                        </span>
                                                                    </div>

                                                                </div>
                                                                @error('email')
                                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-10 col-bg-6 mx-auto col-12 col-lg-8">
                                                        <div class="form-group">
                                                                <label for="email-icon">Numéro de téléphone</label>
                                                                <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                            <div class="input-group input-group-merge @error('telephone')  is-invalid @enderror">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="mr-1 flag-icon flag-icon-cm"></i>(+237)</span>
                                                                </div>
                                                                <input type="text" class=" @error('telephone')  is-invalid @enderror form-control" placeholder="123456789" id="phone_number" name="telephone2" value="{{ old('telephone') == null ? $user->telephone : old('telephone') }}" onkeyup="phone()" required/>
                                                                    <div class="input-group-append  ">
                                                                        <span class="input-group-text">
                                                                            <label for="phone_number" class="p-auto m-auto">
                                                                                <i data-feather="edit"></i>
                                                                            </label>
                                                                        </span>
                                                                    </div>
                                                            </div>
                                                                    <input type="text" id="phone_number2" name="telephone" onkeyup="phone()" hidden/>
                                                            @error('telephone') 
                                                                    <small class="alert alert-danger"> {{$message}} </small>
                                                            @enderror
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
                                                        <button type="reset" onclick="setTimeout(function(){phone()},2)" class="col-lg-5 col-bg-5 col-12 btn round btn-danger mr-md-3 mr-2  waves-effect">
                                                            <i class="mr-1" data-feather="x"></i> Effacer
                                                        </button>
                                                        <button type="submit" class="col-lg-5 col-bg-4 col-12 round btn btn-success waves-effect waves-float waves-light mt-2 mt-md-0 ml-bg-3">
                                                            <i class="mr-1" data-feather='download'></i> Enregistrer
                                                        </button>
                                                    </div>

                                                    <div class="d-block d-lg-none col-md-10 col-lg-8 col-bg-8 col-12 mt-lg-2 mt-2 mb-2 mx-auto text-center">
                                                        <button type="submit" class="col-lg-5 col-bg-4 col-8 btn round btn-success waves-effect">
                                                            <i class="mr-1" data-feather='download'></i> Enregistrer
                                                        </button>
                                                        <button type="reset" onclick="setTimeout(function(){phone()},2)" class="col-lg-5 col-bg-5 col-8 round btn btn-danger waves-effect waves-float waves-light mt-2 mt-lg-0 ">
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
        document.querySelector('#Editer')?.classList.add('active');
        function  phone(){
            var phone_number2 = document.querySelector('#phone_number2')
                phone_number = document.querySelector('#phone_number');
            if(phone_number.value.length == 9 && phone_number.value[0] == '6' ){
                phone_number.value = phone_number.value[0]+' '+phone_number.value[1]+''+phone_number.value[2]+' '+phone_number.value[3]+''+phone_number.value[4]+' '+phone_number.value[5]+''+phone_number.value[6]+' '+phone_number.value[7]+''+phone_number.value[8]
            }else{
                phone_number.value = phone_number.value.replaceAll(' ','')
            }
            phone_number2 .value = phone_number.value.replaceAll(' ','')
        }phone()
    </script>
@endsection