@extends('admin/templates/template')
@section('title')
    {{'Tous les Produits'}}
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
                        <h2 class="content-header-title float-left mb-0">Liste des Produits</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item active">Liste des Produits
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
            </div>
    <div class="card pl-1 pr-1">

            <div class="content-body"> 
                <div class="row justify-content-between bg-light-secondary" >
                    <a class="btn btn-info waves-effect col-md-3  col-sm-7 col-10 mx-auto mx-lg-1 col-lg-2 m-1" href="{{route('produits.create')}}">
                        <i class="mr-1" data-feather='plus'></i>Nouveau
                    </a>
                        <form action="{{ route('produits.index') }}" class="col-md-3 col-sm-7 col-10 col-8 mx-lg-1 p-0 mx-auto col-lg-3 mt-1" id="search" method="POST">
                            @csrf
                            @method('GET')
                            <div class="form-group">
                                <div class="input-group input-group-merge">
                                    <div class="input-group-prepend ">
                                        <span class="input-group-text"><i data-feather='search'></i></span>
                                    </div>
                                    <input type="search" class="form-control" name="recherche" value="{{ session()->get('search')}}" placeholder="Rechercher">
                                </div>
                            </div>
                            <input type="hidden" name="page" id="page" value="">
                        </form>
                    </div>
                </div>
                <!-- Table without card start -->
                <div class="row" id="table-without-card">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr class="text-center">
                                        <th>N°</th>
                                        <th>Noms</th>
                                        <th>libelle</th>
                                        <th>Date création</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $count = 20*$produits->currentPage() -19 ;
                                    @endphp
                                    @foreach($produits as $produit)
                                        <tr class="text-center">
                                            <td>
                                                {{$count}}
                                            </td>
                                            <td>
                                                {{$produit->noms}}
                                            </td>
                                            <td>
                                                {{$produit->libelle}}
                                            </td>
                                            <td>
                                                <div class="badge badge-light-dark">
                                                    {{Ladate($produit->created_at)}}
                                                </div>
                                                à
                                                <div class="badge badge-light-dark">
                                                    {{Heure($produit->created_at)}}
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <button type="button" style="margin-bottom: 0px !important;" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                        <i data-feather="more-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" onclick="responses_ajax('info_description',['{{$produit->id}}'],'{{route('produits.show',$produit->id)}}','GET')" data-toggle="modal" data-target="#description">
                                                            <i data-feather='eye'></i>
                                                            <span class="ml-1">Description</span>
                                                        </a>
                                                        <a class="dropdown-item" href="{{route('produits.edit',$produit)}}">
                                                            <i data-feather="edit-2" class="mr-50"></i>
                                                            <span class="ml-1">Modifier</span>
                                                        </a>
                                                        @if($produit->details_commande->count() == 0 && $produit->stock->count() == 0)
                                                                <span class="text-danger dropdown-item" data-toggle="modal" onclick="remplir('{{route('produits.destroy',$produit->id)}}',{{ Js::from(e($produit->noms)) }},'Supprimer ce Produit' ,'button_footer')" data-target="#danger">
                                                                <i data-feather='trash-2'></i>
                                                                <span class="ml-2 text-danger" type="button">
                                                                    Supprimer
                                                                </span>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @php $count++ @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="border-top mt-1">
                        <div class="d-flex col-md-10 mx-auto" style=" padding:1rem; overflow: auto; border-radius: 4px;">
                            <ul class="pagination"  style="margin-bottom:  0rem">
                            {{-- Previous Page Link --}}
                                @if ($produits->onFirstPage())
                                    <li class="page-item" style="opacity: 0.6; cursor: no-drop;">
                                        <span disabled class="page-link">Précédent</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" onclick="mettre({{ $produits->currentPage()-1 }})" rel="prev" aria-label="@lang('pagination.previous')">Précédent</a>
                                    </li>
                                @endif
                                @for($i=1;$i<=$produits->lastPage();$i++)
                                    <li class="page-item @if($i==$produits->currentPage()) active @endif" >
                                        <a class="page-link" onclick="@if($i!=$produits->currentPage()) mettre({{$i}}) @endif" rel="prev" aria-label="@lang('pagination.previous')">{{$i}}</a>
                                    </li>
                                @endfor
                                {{-- Next Page Link --}}
                                @if ($produits->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" onclick="mettre({{ $produits->currentPage()+1 }})" rel="next" aria-label="@lang('pagination.next')">Suivant</a>
                                    </li>
                                @else
                                    <li class="page-item" style="opacity: 0.6; cursor: no-drop;">
                                        <span disabled class="page-link">Suivant</span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

<div class="modal fade" id="description" tabindex="-1" aria-labelledby="descriptionTitle" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="descriptionTitle"> Informations </h5>
                <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-10 mx-auto" id="info_description">
                        Infos de chaque Produit ici !
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center justify-content-md-around pt-1">
                <button data-dismiss="modal" class="btn px-2 btn-success btn-gradient-success round mx-auto">
                    Ok
                </button>
            </div>
        </div>
    </div>
</div>
</div>
</div>                         
</div>
</div>
</div>
</div>
<div id="button_footer" hidden>
    <form method="POST" class="destroy_form">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-gradient-success btn-success round btn-lg">Confirmer</button>
    </form>
<button type="button" class="btn btn-gradient-danger btn-danger round btn-lg" data-dismiss="modal">Annuler</button>
</div>

@endsection
@section('javascript')
    <script type="text/javascript">
        function enlever(id){
            var ligne = document.querySelector('#ligne'+id);
            if (ligne.hidden) {
                setTimeout(function(){
                    ligne.hidden = false
                },280)
            }else{
                    ligne.hidden = true
            }
        }
        function trouver(id){
            var ligne = document.querySelector('#ligne'+id);
            ligne.click()
        }
        function mettre(number_page){
           document.querySelector('#page').value = number_page;
           document.querySelector('#search').submit() 
        }
        document.querySelector('#ProduitsListe')?.classList.add('active');
    </script>
@endsection