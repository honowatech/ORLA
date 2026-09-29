@extends('superadmin/layout/template')
@section('menu')
    @include('superadmin/menu/menu')
@endsection
@section('title')
{{'Editer l\'api '.$api->name}}
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
                                <h2 class="content-header-title float-left mb-0">Editer l'api {{$api->name}}</h2>
                                <div class="breadcrumb-wrapper">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{route('SuperAdmin.home')}}">Acceuil</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="{{route('Sa-parametre.index')}}">
                                                Paramètres
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item active">Editer L'api {{$api->name}}
                                        </li>
                                    </ol>
                                </div>
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
                                    <h3 class="card-title">Formulaire</h3>
                                </div>
                                <div class="card-body">
                                    <form class="form" id="formulaire" action="{{route('Sa-api.update',$api->id)}}" method="POST" onsubmit="ajax();return false;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="name" value="{{$api->name}}">
                                        <div class="row col-lg-8 mx-auto">
                                            <div class="col-12 col-md-6 mx-auto">
                                                <div class="form-group">
                                                        <label for="user">User Name</label>
                                                        <span class="text-info cursor-pointer" title="Optionnel">Optionnel</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge @error('user') is-invalid @enderror">
                                                            <div class="input-group-prepend ">
                                                                <span class="input-group-text"><i data-feather='at-sign'></i></span>
                                                            </div>
                                                            <input type="text" id="user" class="form-control @error('user') is-invalid @enderror " name="user" placeholder="Login" value="{{ old('user') == null ? $api->user : old('user') }}" / autofocus>
                                                        </div>
                                                        <small class="alert alert-danger" id="user_error" hidden>Entrer le User Name </small>
                                                        @error('user')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 mx-auto">
                                                <div class="form-group">
                                                        <label for="password">Mot de Passe</label>
                                                        <span class="text-info cursor-pointer" title="Optionnel">Optionnel</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge form-password-toggle @error('password') is-invalid @enderror">
                                                            <div class="input-group-prepend ">
                                                                <span class="input-group-text"><i data-feather='eye'></i></span>
                                                            </div>
                                                            <input type="password" id="password" class="form-control form-control-merge @error('password') is-invalid @enderror " name="password" placeholder="Mot de Passe" value="{{ old('password') == null ? $api->password : '' }}" aria-describedby="basic-default-password1" />
                                                        </div>
                                                        <small class="alert alert-danger" id="password_error" hidden>Entrer le Mot de Passe </small>
                                                        @error('password')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 mx-auto">
                                                <div class="form-group">
                                                        <label for="key">Clé d'Api</label>
                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge @error('key') is-invalid @enderror">
                                                            <div class="input-group-prepend ">
                                                                <span class="input-group-text"><i data-feather="key"></i></span>
                                                            </div>
                                                            <input type="text" id="key" class="form-control @error('key') is-invalid @enderror " name="key" placeholder="Clé de l'Api {{$api->name}}" value="{{ old('key') == null ? $api->key : old('key') }}" / required>
                                                        </div>
                                                        <small class="alert alert-danger" id="key_error" hidden>Entrer la Clé de l'Api {{$api->name}}</small>
                                                        @error('key')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 mx-auto">
                                                <div class="form-group">
                                                        <label for="secret">Secret de l'Api {{$api->name}}</label>
                                                        <span class="text-info cursor-pointer" title="Optionnel">Optionnel</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge @error('secret') is-invalid @enderror">
                                                            <div class="input-group-prepend ">
                                                                <span class="input-group-text"><i data-feather="lock"></i></span>
                                                            </div>
                                                            <input type="text" id="secret" class="form-control @error('secret') is-invalid @enderror " name="secret" placeholder="Secret de l'Api {{$api->name}}" value="{{ old('secret') == null ? $api->secret : old('secret') }}" />
                                                        </div>
                                                        <small class="alert alert-danger" id="secret_error" hidden>Entrer le Secret de l'Api {{$api->name}}</small>
                                                        @error('secret')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mx-auto col-12">
                                            <div class="d-none d-md-block text-center col-bg-5 col-lg-8 col-md-12 col-12 mx-auto">
                                                 <button type="reset" class="btn col-8 col-sm-7 col-lg-5  col-xl-3 col-md-4 btn-danger mt-1 mt-md-2 mr-md-4">
                                                    <i class="mr-1" data-feather='x'></i> Effacer
                                               </button>
                                                <button type="button" onclick="ajax()" class="btn col-8 col-sm-7 col-lg-5 col-xl-3 col-md-4 btn-success mt-1 mt-md-2">
                                                    <i class="mr-1" data-feather='check'></i> Enregistrer
                                                </button>
                                            </div>
                                            <div class="d-block d-md-none text-center col-bg-5 col-lg-8 col-md-12 col-12 mx-auto">
                                                <button type="button" onclick="ajax()" class="btn col-8 col-sm-7 col-lg-5  col-xl-3 col-md-4 btn-success mt-1 mt-md-2 mr-md-2">
                                                    <i class="mr-1" data-feather='check'></i> Enregistrer
                                                </button>
                                                <button type="reset" class="btn col-8 col-sm-7 col-lg-5 col-xl-3 col-md-4 btn-danger mt-1 mt-md-2">
                                                    <i class="mr-1" data-feather='x'></i> Effacer
                                                </button>
                                            </div>
                                        </div>
                                        <div>                                       
                                            <div class="modal fade" id="recap" tabindex="-1" aria-labelledby="descriptionTitle" aria-modal="true" role="dialog">
                                                <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document" id="modifiated">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="descriptionTitle">Boite de Confirmation</h5>
                                                            <button type="button" class="bg-danger close m-0" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true" class="text-white">×</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-11 mx-auto py-1" id="recapitulatif">
                                                                    Le Récap s'affichera ici !!!
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer justify-content-center justify-content-md-apt-1">
                                                            <div class="d-none d-lg-block text-center col-12 mx-auto">
                                                                 <button class="btn col-8 col-sm-7 col-lg-5  col-xl-3 col-md-8 btn-danger mt-1 mt-lg-2 mr-lg-4" data-dismiss="modal" aria-label="Close">
                                                                    <i class="mr-1" data-feather='x'></i> Annuler
                                                               </button>
                                                                <button type="button" onclick="valider('formulaire')" data-toggle="modal" data-target="#setting" class="btn col-8 col-sm-7 col-lg-5 col-xl-3 col-md-8 btn-success mt-1 mt-lg-2">
                                                                    <i class="mr-1" data-feather='check'></i> Confirmer
                                                                </button>
                                                            </div>
                                                            <div class="d-block d-lg-none text-center col-12 mx-auto">
                                                                <button type="button" onclick="valider('formulaire')" data-toggle="modal" data-target="#setting" class="btn col-8 col-sm-7 col-lg-5  col-xl-3 col-md-8 btn-success mt-1 mt-lg-2 mr-lg-2mx-auto">
                                                                    <i class="mr-1" data-feather='check'></i> Confirmer
                                                                </button>
                                                                <button class="btn col-8 col-sm-7 col-lg-5 col-xl-3 col-md-8 btn-danger mt-1 mt-lg-2mx-auto" data-dismiss="modal" aria-label="Close">
                                                                    <i class="mr-1" data-feather='x'></i> Anuller
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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
        document.querySelector('#parametre-index').classList.add('active');
        function ajax(){
            recap = recapituler('modal','recap');
            if(!recap){
                recap_ajax('recapitulatif',datas,'{{route('Sa-api.recap_edit')}}','POST')
            }  
        }
    </script>
@endsection