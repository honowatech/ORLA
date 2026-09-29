@extends('superadmin/layout/template')
@section('menu')
    @include('superadmin/menu/menu')
@endsection
@section('title')
{{'Nouveau Client'}}
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
                            <h2 class="content-header-title float-left mb-0">Nouveau Client</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('SuperAdmin.home')}}">Acceuil</a>
                                </li>
                                <li class="breadcrumb-item active">Ajouter un Client
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
                                    <h3 class="card-title">Formulaire</h3>
                                </div>
                                <div class="card-body">
                                    <form class="form" id="formulaire" action="{{route('Sa-client.store')}}" method="POST" onsubmit="ajax();return false;">
                                    	@csrf
                                        <div class="row col-lg-8 mx-auto">
                                            <div class="col-12 col-md-7 mx-auto">
                                                <div class="form-group">
                                                        <label for="name">Nom te Prénoms</label>
                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge @error('name') is-invalid @enderror">
                                                            <div class="input-group-prepend ">
                                                                <span class="input-group-text"><i data-feather="at-sign"></i></span>
                                                            </div>
                                                            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror " name="name" placeholder="Nom et Prénoms" value="{{ old('name') }}" required autofocus />
                                                        </div>
                                                        <small class="alert alert-danger" id="name_error" hidden>Entrer le Nom et le Prénom</small>
                                                        @error('name')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-5 mx-auto">
                                                <div class="form-group">
                                                        <label for="cni">Numéro de CNI</label>
                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge @error('cni') is-invalid @enderror">
                                                            <div class="input-group-prepend ">
                                                                <span class="input-group-text"><i data-feather='credit-card'></i></span>
                                                            </div>
                                                            <input type="text" id="cni" class="form-control @error('cni') is-invalid @enderror " name="cni" placeholder="Numéro de CNI" value="{{ old('cni') }}" / required autofocus>
                                                        </div>
                                                        <small class="alert alert-danger" id="cni_error" hidden>Entrer le numéro de CNI </small>
                                                        @error('cni')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 mx-auto">
                                                <div class="form-group">
                                                        <label for="telephone">Numéro de téléphone</label>
                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge  @error('telephone') is-invalid @enderror">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    <i class="mr-1 flag-icon flag-icon-cm"></i>
                                                                    +237
                                                                </span>
                                                            </div>
                                                            <input type="telephone" id="telephone" class="form-control @error('telephone') is-invalid @enderror" placeholder="Numéro de téléphone" value="{{ old('telephone') }}" onkeyup="phone('telephone');" name="telephone2" required/>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">
                                                                    <i data-feather="phone"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <input type="text" hidden id="telephone2" name="telephone"/>
                                                        <small class="alert alert-danger" id="telephone2_error" hidden>Entrer le Numéro de téléphone</small>
                                                        @error('telephone')
                                                                <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 mx-auto">
                                                <div class="form-group">
                                                        <label for="telephone_secondaire">Deuxième numéro de téléphone</label>
                                                        <span class="text-info cursor-pointer" title="Optionnel">Optionnel</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge  @error('telephone_secondaire') is-invalid @enderror">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    <i class="mr-1 flag-icon flag-icon-cm"></i>
                                                                    +237
                                                                </span>
                                                            </div>
                                                            <input type="telephone_secondaire" id="telephone_secondaire" class="optionnel form-control @error('telephone_secondaire') is-invalid @enderror" placeholder="Numéro de téléphone sécondaire" name="telephone_secondaire2" value="{{ old('telephone_secondaire') }}" onkeyup="phone('telephone_secondaire');"/>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">
                                                                    <i data-feather="phone"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <input type="text" hidden id="telephone_secondaire2" name="telephone_secondaire"/>
                                                        @error('telephone_secondaire')
                                                                <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mx-auto">
                                                <div class="form-group">
                                                        <label for="adresse">Adresse</label>
                                                        <span class="text-info cursor-pointer" title="Optionnel">Optionnel</span>
                                                    <div class="form-group">
                                                        <textarea type="text" id="adresse" class="form-control @error('adresse') is-invalid @enderror" name="adresse" rows="3" placeholder="Adresse détaillée du Client"/>{{ old('adresse') }}</textarea>
                                                        <small class="alert alert-danger" id="adresse_error" hidden>Entrer l' Adresse</small>
                                                        @error('adresse')
                                                            <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror('adresse')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mx-auto col-12">
                                            <div class="d-none d-md-block text-center col-bg-5 col-lg-8 col-md-12 col-12 mx-auto">
                                                 <button type="reset" onclick="Sa_reset()" class="btn col-8 col-sm-7 col-lg-5  col-xl-3 col-md-4 btn-danger mt-1 mt-md-2 mr-md-4">
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
                                                <button type="reset" onclick="Sa_reset()" class="btn col-8 col-sm-7 col-lg-5 col-xl-3 col-md-4 btn-danger mt-1 mt-md-2">
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
        document.querySelector('#client-create').classList.add('active');
        function ajax(){
            recap = recapituler('modal','recap');
            if(!recap){
                recap_ajax('recapitulatif',datas,'{{route('Sa-client.recap_create')}}','POST')
            }  
        }
        function Sa_reset(){
            setTimeout(function(){
                phone('telephone_secondaire');
                phone('telephone');
            },100)
        }
        $(window).on('load', function() {
            phone('telephone_secondaire');
            phone('telephone');
        })
	</script>
@endsection