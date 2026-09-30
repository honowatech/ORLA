@extends('admin/templates/template')

@section('title')
    {{'Ajouter un Produit'}}
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
                        <h2 class="content-header-title float-left mb-0">Ajouter un Produit</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item active">Ajouter un Produit
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
                                    <form class="form" action="{{route('produits.store')}}" method="POST">
                                    	@csrf
                                        <div class="row">
                                            <div class="col-md-5 mx-auto col-12">
                                                <div class="form-group">
                                                        <label for="libelle">Noms</label>
                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge @error('noms') is-invalid @enderror">
                                                            <div class="input-group-prepend ">
                                                                <span class="input-group-text"><i data-feather='shopping-cart'></i></span>
                                                            </div>
                                                            <input type="text" id="noms" class="form-control @error('noms') is-invalid @enderror" name="noms" placeholder="Exp : Banane" value="{{ old('noms') }}" / required>
                                                        </div>
                                                        @error('noms')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mx-auto col-12">
                                                <div class="form-group">
                                                        <label for="libelle">Libellé</label>
                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-merge @error('libelle') is-invalid @enderror">
                                                            <div class="input-group-prepend ">
                                                                <span class="input-group-text"><i data-feather='at-sign'></i></span>
                                                            </div>
                                                            <input type="text" id="libelle" class="form-control @error('libelle') is-invalid @enderror" name="libelle" placeholder="Exp : Ban" value="{{ old('libelle') }}" / required>
                                                        </div>
                                                        @error('libelle')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-8 mx-auto col-12">
                                                <div class="form-group">
                                                        <label for="description">Description</label>
                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                    <div class="form-group">
                                                            <textarea type="text" id="description" class="form-control @error('description') is-invalid @enderror" name="description" placeholder="Description brève du produit" rows="2" required>{{ old('description') }}</textarea>
                                                        @error('description')
                                                        <small class="alert alert-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
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
		document.querySelector('#ProduitsAjouter')?.classList.add('active');
	</script>
@endsection