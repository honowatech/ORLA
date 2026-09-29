@extends('superadmin/layout/template')
@section('menu')
    @include('superadmin/menu/menu')
@endsection
@section('title')
{{'Nouvel Abonnement'}}
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
                            <h2 class="content-header-title float-left mb-0">Nouvel Abonnement</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('SuperAdmin.home')}}">Acceuil</a>
                                </li>
                                <li class="breadcrumb-item active">Ajouter un Abonnement
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
                                    <form class="form" id="formulaire" action="{{route('Sa-abonnement.store')}}" method="POST" onsubmit="ajax();return false;">
                                    	@csrf
                                        <div class="row col-lg-8 mx-auto">
                                            <div class="col-12 col-md-6 mx-auto">
                                                <div class="form-group">
                                                        <label for="titre">Libellé</label>
                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge @error('titre') is-invalid @enderror">
                                                            <div class="input-group-prepend ">
                                                                <span class="input-group-text"><i data-feather="at-sign"></i></span>
                                                            </div>
                                                            <input type="text" id="titre" class="form-control @error('titre') is-invalid @enderror " name="titre" placeholder="Libellé" value="{{ old('titre') }}" required autofocus />
                                                        </div>
                                                        <small class="alert alert-danger" id="titre_error" hidden>Entrer le Libellé de l'abonnement</small>
                                                        @error('titre')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 mx-auto">
                                                <div class="form-group">
                                                    <label for="montant">montant</label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge @error('montant') is-invalid @enderror">
                                                            <input type="text" id="montant" class="form-control @error('montant') is-invalid @enderror " name="montant2" placeholder="10,000" onkeyup="format_montant('montant')" value="{{ old('montant2') }}" / required autofocus>
                                                            <div class="input-group-append ">
                                                                <span class="input-group-text">FCFA</span>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" id="montant2" name="montant" value="{{ old('salaire') }}" autofocus />
                                                        <small class="alert alert-danger" id="montant2_error" hidden>Entrer un montant</small>
                                                        @error('montant')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 mx-auto">
                                                <div class="form-group text-center">
                                                        <label for="accumulateur">Fréquence</label>
                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group w-50 bootstrap-touchspin mx-auto">
                                                            <input type="text" id="accumulateur" name="accumulateur" class="border touchspin-color form-control" value="{{ old('accumulateur') == null ? 1 : old('accumulateur') }}" min="1"data-bts-button-down-class="btn btn-dark" data-bts-button-up-class="btn btn-dark" required>
                                                        </div>
                                                        <small class="alert alert-danger" id="accumulateur_error" hidden>Entrer une Fréquence</small>
                                                        @error('accumulateur')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 mx-auto">
                                                <div class="form-group">
                                                    <label for="type_periode">Type de Période</label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <select class="select2 form-control" id="type_periode" name="type_periode" required>
                                                            <option value="">
                                                                Choisir un Type de Période
                                                            </option>
                                                            @foreach($types_periode as $key => $type_periode)
                                                                <option class="type_periode" 
                                                                    value="{{$key}}"
                                                                    {{$key == old('type_periode') ? 'selected' : ''}}>
                                                                    {{$type_periode}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <small class="alert alert-danger" id="type_periode_error" hidden>Sélectionner la période</small>
                                                        @error('type_periode')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 mx-auto">
                                                <div class="form-group text-center">
                                                        <label for="periode_grace">Période de grace (Jours)</label>
                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group w-50 bootstrap-touchspin mx-auto">
                                                            <input type="text" id="periode_grace" name="periode_grace" class="border touchspin-color form-control" value="{{ old('periode_grace') == null ? 0 : old('periode_grace') }}" min="0"data-bts-button-down-class="btn btn-dark" data-bts-button-up-class="btn btn-dark" required>
                                                        </div>
                                                        <small class="alert alert-danger" id="periode_grace_error" hidden>Entrer une Fréquence</small>
                                                        @error('periode_grace')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
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
        document.querySelector('#abonnement-create').classList.add('active');
        function ajax(){
            recap = recapituler('modal','recap');
            if(!recap){
                recap_ajax('recapitulatif',datas,'{{route('Sa-abonnement.recap_create')}}','POST')
            }  
        }
        function Sa_reset(){
            setTimeout(function(){
                format_montant('montant')
            },100)
        }
        $(window).on('load', function() {
            format_montant('montant')
        })
	</script>
@endsection