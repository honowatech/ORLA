@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')

@section('title')
{{'Créer un Véhicule '}}
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
                        <h2 class="content-header-title float-left mb-0">Ajouter un Véhicule</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item active">Ajouter un Véhicule
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
                                    <form class="form" action="{{route('vehicule.store')}}" method="POST">
                                        @csrf
                                        <div class="row col-lg-8 col-md-10 mx-auto">
                                            <div class="col-md-6 col-12 mx-auto">
                                                <div class="form-group">
                                                    <label for="id_type"> Type de véhicule </label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <select class=" @error('id_type')  is-invalid @enderror select2 form-control" id="id_type" name="id_type"  required>
                                                            <option value="">Choisir un Type de véhicule</option>
                                                            @foreach($types_vehicule as $type_vehicule)
                                                                <option
                                                                    value="{{$type_vehicule->id}}"
                                                                    {{$type_vehicule->id == old('id_type') ? 'selected' : ''}}>
                                                                    {{$type_vehicule->libelle}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @error('id_type') 
                                                            <small class="alert alert-danger"> {{$message}} </small>
                                                    @enderror
                                                    </div>
                                                </div>
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
                                            <div class="col-md-6 col-12 m-auto">
                                                <div class="form-group">
                                                    <label for="immatriculation">Immatriculation</label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge @error('immatriculation') is-invalid @enderror">
                                                            <div class="input-group-prepend ">
                                                                <span class="input-group-text"><i data-feather='stop-circle'></i></span>
                                                            </div>
                                                            <input type="text" id="immatriculation" class="form-control @error('immatriculation') is-invalid @enderror" name="immatriculation" placeholder="Immatriculation" value="{{ old('immatriculation') }}" / required>
                                                        </div>
                                                        @error('immatriculation')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 m-auto">
                                                <div class="form-group">
                                                    <label for="model">Modèle</label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge @error('modele') is-invalid @enderror">
                                                            <div class="input-group-prepend ">
                                                                <span class="input-group-text"><i data-feather='clipboard'></i></span>
                                                            </div>
                                                            <input type="text" id="modele" class="form-control @error('modele') is-invalid @enderror" name="modele" placeholder="Modèle" value="{{ old('modele') }}" / required>
                                                        </div>
                                                        @error('modele')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 m-auto">
                                                <div class="form-group">
                                                    <label for="marque">Marque</label>
                                                    <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge @error('marque') is-invalid @enderror">
                                                            <div class="input-group-prepend ">
                                                                <span class="input-group-text"><i data-feather='tag'></i></span>
                                                            </div>
                                                            <input type="text" id="marque" class="form-control @error('marque') is-invalid @enderror" name="marque" placeholder="Marque" value="{{ old('marque') }}" / required>
                                                        </div>
                                                        @error('marque')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 m-auto">
                                                <div class="form-group">
                                                    <label for="couleur">Couleur</label>
                                                    <span class="text-info cursor-pointer" title="Optionnel">Optionnel</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge @error('couleur') is-invalid @enderror">
                                                            <div class="input-group-prepend ">
                                                                <span class="input-group-text"><i data-feather='type'></i></span>
                                                            </div>
                                                            <input type="text" id="couleur" class="form-control @error('couleur') is-invalid @enderror" name="couleur" placeholder="exp : Bleu" value="{{ old('couleur') }}" />
                                                        </div>
                                                        @error('couleur')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-12 mx-auto">
                                                <div class="form-group">
                                                    <label for="description">description</label>
                                                    <span class="text-info cursor-pointer" title="Optionnel">Optionnel</span>
                                                    <div class="form-group">
                                                        <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" placeholder="Description du véhicule" rows="3">{{ old('description') }}</textarea>
                                                        @error('description')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
	<script type="text/javascript">
		document.querySelector('#VehiculeAjouter')?.classList.add('active');
	</script>
@endsection