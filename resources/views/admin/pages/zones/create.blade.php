@extends('admin/templates/template')

@section('title')
{{'Créer une Zone '}}
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
                        <h2 class="content-header-title float-left mb-0">Ajouter une Zone</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item active">Ajouter une Zone
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
                                    <form class="form" action="{{route('zone.store')}}" method="POST">
                                        @csrf
                                        @method('POST')
                                        <div class="row">
                                            <div class="col-md-12 col-12">
                                                <div class="form-group">
                                                        <label for="libelle">Nom(s) de la Zone</label>
                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge @error('libelle') is-invalid @enderror">
                                                            <div class="input-group-prepend ">
                                                                <span class="input-group-text"><i data-feather='at-sign'></i></span>
                                                            </div>
                                                            <input type="text" id="libelle" class="form-control @error('libelle') is-invalid @enderror" name="libelle" placeholder="noms et Prénoms" value="{{ old('libelle') }}" / autofocus required>
                                                        </div>
                                                        @error('libelle')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 mx-auto">
                                                <div class="form-group">
                                                    <label for="id_ville"> Villes </label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <select class=" @error('id_ville')  is-invalid @enderror select2 form-control" id="id_ville" name="id_ville" onchange="choose()" required>
                                                            <option value="0">Choisir une ville</option>
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
                                            <div class="col-md-6 col-12 mx-auto">
                                                <div class="form-group">
                                                    <label for="id_quartier_associe">Choisir des Quartiers</label>
                                                    <div class="form-group mb-0" id="div_id_quartier_associe"  hidden>
                                                        <select class=" @error('id_quartier_associe')  is-invalid @enderror select2 form-control" id="id_quartier_associe" multiple='multiple' name="inutile" onchange="remplir_vrai_input()">
                                                        </select>

                                                        <input type="hidden" name="id_quartier_associe" id="vrai_input">

                                                    </div>
                                                    <div class="text-center">
                                                        <div class="spinner-border text-info" role="status" id="spinner">
                                                            <span class="sr-only"></span>
                                                        </div>
                                                    </div>
                                                    @error('id_quartier_associe') 
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
	<script type="text/javascript">
        function remplir_vrai_input(){
            $(document).ready(function() {
              // Récupération des données sélectionnées
              const id_quartier_associe = $("#id_quartier_associe").val();

              // Affichage des données sélectionnées dans la console
              valeur = '';
              virgule = ','
              for (var i = id_quartier_associe.length - 1; i >= 0; i--) {
                    i == 0 ? virgule = '' : virgule = ',';
                 valeur += id_quartier_associe[i]+virgule;
              }
                document.querySelector('#vrai_input').value = valeur;
                console.log(document.querySelector('#vrai_input').value)
            });
        }
		document.querySelector('#zoneAjouter').classList.add('active');
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
                    data : { id_ville : id_ville.value, '_token' : "{{ csrf_token() }}" },
                    success: function(response)
                    {
                        spinner.hidden = true;
                        div_id_quartier_associe.hidden = false;
                        id_quartier_associe.innerHTML = response;
                        remplir_vrai_input()
                    },
                    error: function(){
                        alert(" Un problème est survenu veuillez rééseiller plus tard !!!");
                    }
            });
        }
        choose()
	</script>
@endsection