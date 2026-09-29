@extends('admin/templates/template')
@section('title')
    Informations sur le client {{$client->type__client->libelle}} {{$client->noms}}
@endsection
@section('contenu')

    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
            <div class="content-wrapper">
               <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Infos sur {{$client->noms.' '.$client->Prenoms}}</h2>
                            <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{route('clients.index')}}">Liste des Client</a>
                            </li>
                            <li class="breadcrumb-item active">Infos sur {{$client->noms.' '.$client->Prenoms}}
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
            </div>
        </div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <section class="app-user-view">
                    <!-- User Card & Plan Starts -->
                    <div class="row">
                        <!-- User Card starts-->
                        <div class="col-md-12">
                            <div class="card user-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-bg-6 col-sm-10 col-lg-6 mt-2 mt-xl-0 mx-auto d-flex flex-column justify-content-between border-container-lg">
                                            <div class="user-avatar-section">
                                                <div class="d-flex justify-content-start">
                                                    <img class="img-fluid rounded" src="{{asset('app-assets/images/avatars/user.png')}}" height="140" width="140" alt="User avatar" />
                                                    <div class="d-flex flex-column ">
                                                        <div class="user-info mb-1">
                                                            <h4 class="mb-0 ">{{$client->noms.' '.$client->Prenoms}}</h4>
                                                            <span class="card-text ">{{'Client'}}</span>
                                                        </div>
                                                        <div class="d-flex flex-wrap">
                                                            <a href="{{route('clients.edit',$client->id)}}" class="btn btn-info mr-1 round ">Modifier</a>
                                                            <button class=" btn @if($client->statut == 1) round btn-danger  @else btn-outline-success  @endif " data-toggle="modal" onclick="remplir('{{route('clients.destroy',$client->id)}}','{{$client->noms.' '.$client->Prenoms}}', @if($client->statut == 1) 'Désactiver' @else 'Activer' @endif ,'button_footer')" data-target="#danger">
                                                            @if($client->statut == 1)
                                                                    Désactiver
                                                            @else
                                                                    Activer
                                                            @endif
                                                        </button>
                                                        </div>
                                                        @if(strtoupper($client->type__client->libelle)==strtoupper('entreprise'))
                                                            <div class="d-flex flex-wrap">
                                                                <button type="button" id="new_building" class="round col-12 btn btn-warning" data-toggle="modal" data-target="#exampleModalScrollable">
                                                                    <i class="m-0 p-0" data-feather='plus'></i>
                                                                    <small>
                                                                        <span class="d-none d-lg-inline-block"> Ajouter les </span> boutiques
                                                                    </small>
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-5 col-10 col-md-6 col-sm-8 col-md-6 col-lg-6 mt-2 mt-xl-0 mx-auto">
                                            <div class="user-info-wrapper">
                                                <div class="d-flex flex-lg-wrap">
                                                    <div class="user-info-title">
                                                        <i data-feather="user" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Noms</span>
                                                    </div>
                                                    <p class="card-text mb-0"><span class="d-none d-lg-inline-block">{{$client->noms}} </span> {{$client->Prenoms}}</p>
                                                </div>
                                                <div class="d-flex flex-lg-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="check" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Status</span>
                                                    </div>
                                                    <span class="badge badge-pill @if($client->statut == 0)badge-light-danger @else badge-light-success @endif">
                                                        @if($client->statut == 0)
                                                            Désactivé
                                                        @else 
                                                            Actif
                                                        @endif
                                                    </span>
                                                    
                                                </div>
                                                <div class="d-flex flex-lg-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="star" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Rôle</span>
                                                    </div>
                                                    <p class="card-text mb-0"><span class="d-none d-lg-inline-block">{{'Client'}}</span> ({{$client->Type__client->libelle}}) </p>
                                                </div>
                                                <div class="d-flex flex-lg-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="flag" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Pays</span>
                                                    </div>
                                                    <p class="card-text mb-0">{{'Cameroun'}}</p>
                                                </div>
                                                <div class="d-flex flex-lg-wrap">
                                                    <div class="user-info-title">
                                                        <i data-feather="phone" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Contacts</span>
                                                    </div>
                                                    <p class="card-text mb-0"><span class="d-none d-lg-inline-block">(+237)</span> {{$client->telephone}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /User Card Ends-->
                        <!-- /Plan CardEnds -->
                    </div>
                    <!-- User Card & Plan Ends -->


                    <div class="row">

                        <!-- boutique Permissions Starts -->
                        <div class="col-md-12">
                            <!-- boutique Permissions -->
                            <div class="card">
                                <div class="col-lg-6 col-12">
                                    <div class="card-header row">
                                        <h4 class=" card-title">
                                             Boutiques ( {{$client->boutiques->count()}} )
                                        </h4>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-borderless">
                                        <thead class="thead-light">
                                            <tr class="text-center">
                                                <th>N°</th>
                                                <th>Libelle</th>
                                                <th>quartier</th>
                                                <th>status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $row=1;
                                            @endphp
                                            @foreach($client->boutiques as $boutique)
                                                <tr class="text-center">
                                                    <td>
                                                        {{$row}}
                                                    </td>
                                                    <td>
                                                        {{$boutique->libelle}}
                                                    </td>
                                                    <td>
                                                        {{$boutique->quartier_boutique!=null?$boutique->quartier_boutique->libelle:'Speedex'}}
                                                    </td>
                                                    <td>  
                                                        <span class="badge badge-pill @if($boutique->statut == 0)badge-light-danger @else badge-light-success @endif">
                                                            @if($boutique->statut == 0)
                                                                Désactivée
                                                            @else 
                                                                Active
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <button type="button" style="margin-bottom: 0px !important;" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                                <i data-feather="more-vertical"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                {{-- <a class="dropdown-item" href="{{route('boutiques.show',$boutique->id)}}">
                                                                    <i data-feather='eye'></i>
                                                                    <span class="ml-1">Détails</span>
                                                                </a> --}}
                                                                {{-- <a class="dropdown-item" href="{{route('boutiques.edit',$boutique->id)}}">
                                                                    <i data-feather="edit-2" class="mr-50"></i>
                                                                    <span class="ml-1">Editer</span>
                                                                </a> --}}
                                                                <span class=" @if($boutique->statut == 1) text-danger @else text-success @endif dropdown-item" data-toggle="modal" onclick="remplir('{{route('boutiques.destroy',$boutique->id)}}','{{$boutique->libelle}}',@if($boutique->statut == 1) 'Désactiver' @else 'Activer' @endif ,'button_footer')" data-target="#danger">
                                                                    @if($boutique->statut == 1)
                                                                        <i data-feather='user-x'></i>
                                                                        <span class="ml-1 text-danger" type="button">
                                                                            Désactiver
                                                                        </span>
                                                                    @else
                                                                        <i data-feather='user-check'></i>
                                                                        <span class="ml-1 text-success" type="button">
                                                                            Activer
                                                                        </span> 
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                            @php
                                                $row++
                                            @endphp
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /boutique Permissions -->
                        </div>
                        <!-- boutique Permissions Ends -->
                    </div>
                </section>
                <div class="scrolling-inside-modal">
                    <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable h-100" role="document">
                            <div class="modal-content m-auto">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Nouvelle Boutique ({{$client->noms}} {{$client->Prenoms}})</span></h5>
                                    <button type="button" title="Fermer" class="close m-0" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">  
                                    <div class="card-body pb-0">
                                        <form class="form" action="{{route('boutiques.store')}}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class=" col-12">
                                                            <label for="noms">libelle de la Boutique</label>
                                                            <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                        <div class="form-group">
                                                            <div class="input-group input-group-merge @error('libelle') is-invalid @enderror">
                                                                <div class="input-group-prepend ">
                                                                    <span class="input-group-text"><i data-feather='shopping-cart'></i></span>
                                                                </div>
                                                                <input type="text" id="libelle" class="form-control @error('libelle') is-invalid @enderror" name="libelle" placeholder="Nom de la Boutique" value="{{ old('libelle') }}" / required>
                                                            </div>
                                                            @error('libelle')
                                                            <small class="alert alert-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>
                                                </div>
                                                <div class=" col-12">
                                                    <div class="form-group">
                                                        <label for="id_quartier">Quartier de la boutique</label>
                                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                            <select class=" @error('id_quartier')  is-invalid @enderror select2 form-control" id="id_quartier" name="id_quartier" required>
                                                                <option value="">Choisir un Quartier</option>
                                                                @foreach($quartiers as $quartier)
                                                                    <option
                                                                        value="{{$quartier->id}}"
                                                                        {{$quartier->id == old('id_quartier') ? 'selected' : ''}}>
                                                                        {{$quartier->libelle}}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        @error('id_quartier') 
                                                                <small class="alert alert-danger"> {{$message}} </small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class=" col-12">
                                                            <label for="noms">Nom Client</label>
                                                            <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                        <div class="form-group">
                                                            <div class="input-group input-group-merge">
                                                                <div class="input-group-prepend ">
                                                                    <span class="input-group-text"><i data-feather='at-sign'></i></span>
                                                                </div>
                                                                <input type="text" hidden id="id_client" name="id_client" placeholder="Id du client" value="{{$client->id}}" / required>
                                                                <input type="text" id="id_client" style="cursor: not-allowed;" class="form-control" placeholder="Nom client" value="{{$client->noms}} {{$client->Prenoms}}" / disabled>
                                                            </div>
                                                        </div>
                                                </div>
                                                <div class=" col-12">
                                                            <label for="noms">Localisation</label>
                                                            <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                                        <div class="form-group">
                                                            <textarea type="text" id="localisation" class="form-control @error('localisation') is-invalid @enderror" name="localisation" rows="3" placeholder="Text précis sur la Localisation de la Boutique" required>{{ old('localisation') }}</textarea>
                                                            @error('localisation')
                                                            <small class="alert alert-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>
                                                </div>
                                                <div class="col-12 d-lg-flex space-around mt-1">
                                                    <div class="col-lg-6 form-group pb-0 ">
                                                        <button type="reset" class=" col-12 btn btn-danger round waves-effect" data-dismiss="modal">annuler</button>
                                                    </div>
                                                    <div class="col-lg-6 form-group mb-0 pb-0">
                                                        <button type="submit" class=" col-12 btn btn-success round  waves-effect waves-float waves-light">Enregistrer</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->
<div id="button_footer" hidden>
<button type="button" class="btn btn-danger round" data-dismiss="modal">Annuler</button>
    <form method="POST" class="destroy_form">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-success round">Confirmer</button>
    </form>
</div>
@endsection
@section('javascript')
    <script type="text/javascript">
    @if(strtoupper($client->type__client->libelle)==strtoupper('entreprise'))
        @if(session()->has('erreur'))
            document.querySelector('#new_building').click();
        @endif
    @endif
        document.querySelector('#ClientsListe').classList.add('active');
    </script>
@endsection