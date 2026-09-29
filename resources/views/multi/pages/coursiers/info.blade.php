@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')
@section('title')
Informations sur {{$coursier->noms}} {{$coursier->prenoms}}
@endsection
@section('contenu')

    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
            <div class="content-wrapper">
            <div class="content-header row">
               <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Infos sur {{$coursier->noms.' '.$coursier->prenoms}}</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{route('coursiers.index')}}">Liste des Livreur</a>
                                    </li>
                                    <li class="breadcrumb-item active">Infos sur {{$coursier->noms}} {{$coursier->prenoms}}
                                    </li>
                                </ol>
                            </div>
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
                            <div class="card user-card {{ $coursier->infos_perso->cni == null ? 'bg-light-danger' : '' }}" title="{{ $coursier->infos_perso->cni == null ? 'Livreur Non Authentifié' : 'Livreur Authentifié' }}">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-bg-6 col-lg-6 d-flex flex-column justify-content-between border-container-lg">
                                            <div class="user-avatar-section">
                                                <div class="d-flex justify-content-start">
                                                    <i data-feather="user" class="m-auto" style="width: 10rem; height: 10rem;"></i>
                                                    <div class="d-flex flex-column ml-1">
                                                        <div class="user-info mb-1">
                                                            <h4 class="mb-0 ">{{$coursier->noms.' '.$coursier->prenoms}}</h4>
                                                            <span class="card-text ">{{'Coursier'}}</span>
                                                        </div>
                                                        <div class="d-flex flex-wrap pb-1">
                                                            @if($coursier->infos_perso->cni == null)
                                                                <a class="text-info btn-info btn-sm" title="Cliquer Pour authentifier" href="{{route('informations_personnels.edit',$coursier->id.'-2')}}"> Authentifier </a>
                                                            @else 
                                                                <i data-feather="user-check" class="text-success mr-1"></i> 
                                                                <small class="text-success">Authentifié</small>
                                                            @endif
                                                        </div>
                                                        <div class="d-flex flex-wrap" style="gap: 0.3rem;">
                                                            <a href="{{route('coursiers.edit',$coursier->id)}}" class=" btn btn-dark">Modifier</a>
                                                            <button class="btn @if($coursier->statut == 1)  btn-danger  @else btn-success  @endif " data-toggle="modal" onclick="remplir('{{route('coursiers.destroy',$coursier->id)}}','{{$coursier->noms.' '.$coursier->prenoms}}', @if($coursier->statut == 1) 'Désactiver' @else 'Activer' @endif ,'button_footer')" data-target="#danger">
                                                                @if($coursier->statut == 1)
                                                                    Désactiver
                                                                @else
                                                                    Activer
                                                                @endif
                                                            </button>
                                                        </div>
                                                        <div class="d-flex flex-wrap">
                                                            <button data-toggle="modal" data-target="#select2InModal" class=" col-12 btn btn-dark">
                                                                <i data-feather='share-2' class="mr-50"></i>
                                                                <span class="d-none d-md-inline-block">
                                                                    Attribuer des 
                                                                </span>
                                                                Zones
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-bg-6 col-lg-6 mt-2 mt-xl-0">
                                            <div class="user-info-wrapper">
                                                <div class="d-flex flex-wrap">
                                                    <div class="user-info-title">
                                                        <i data-feather="user" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Noms</span>
                                                    </div>
                                                    <p class="card-text mb-0">{{$coursier->noms.' '.$coursier->prenoms}}</p>
                                                </div>
                                                <div class="d-flex flex-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="check" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Status</span>
                                                    </div>
                                                    <span class="badge badge-glow @if($coursier->statut == 0)badge-danger @else badge-success @endif">
                                                        @if($coursier->statut == 0)
                                                            Désactivé
                                                        @else 
                                                            Actif
                                                        @endif
                                                    </span>
                                                    
                                                </div>
                                                <div class="d-flex flex-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="star" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Rôle</span>
                                                    </div>
                                                    <p class="card-text mb-0">{{'Coursier'}}</p>
                                                </div>
                                                <div class="d-flex flex-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="flag" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Pays</span>
                                                    </div>
                                                    <p class="card-text mb-0">{{'Cameroun'}}</p>
                                                </div>
                                                <div class="d-flex flex-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="map-pin" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Ville</span>
                                                    </div>
                                                    <p class="card-text mb-0">{{$coursier->user->ville->libelle}}</p>
                                                </div>
                                                <div class="d-flex flex-wrap">
                                                    <div class="user-info-title">
                                                        <i data-feather="phone" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Contacts</span>
                                                    </div>
                                                    <p class="card-text mb-0 d-none d-md-inline-block">{{phone($coursier->telephone)}} @if($coursier->infos_perso->telephone2!=null) / {{phone($coursier->infos_perso->telephone2)}}@endif</p>
                                                    <p class="card-text mb-0 d-inline-block d-md-none">{{phone2($coursier->telephone)}} <br> @if($coursier->infos_perso->telephone2!=null){{phone2($coursier->infos_perso->telephone2)}}@endif</p>
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
                    <div class="row">

                        <!-- zone Permissions Starts -->
                        <div class="col-md-12">
                            <!-- zone Permissions -->
                            <div class="card">
                                <div class="card-header pb-0 pt-1 row" style="gap: 2rem;">
                                    <h4 class="">Véhicules Attribués( {{$coursier->vehicules->count()}} )</h4>
                                    <div class="">
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#vehucule_form">
                                            <i data-feather="truck" class="mr-50"></i> 
                                            <span class="d-none d-md-inline-block">
                                                Attribuer des 
                                            </span> Véhicules
                                        </button>
                                    </div>
                                </div>
                                <!-- modale de sélection des viles -->
                                <div class="modal fade text-left" id="vehucule_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="myModalLabel1">Modale de Sélection</h4>
                                                <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{route('coursiers.update',$coursier)}}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="attribuate" value="1">
                                                <div class="modal-body">
                                                    <p>Attribuer les Véhicules à {{$coursier->noms}} {{$coursier->prenoms}}</p>
                                                    <label for="vehicules">Choisir un ou plusieurs vehicules</label>
                                                    <div class="form-group mb-0" id="div_vehicules">
                                                    <select class=" @error('vehicules')  is-invalid @enderror select2 form-control" id="vehicules" name="vehicules[]" multiple='multiple'>
                                                        @foreach($vehicules as $vehicule)
                                                            <option value="{{$vehicule->id}}">
                                                                {{$vehicule->type->libelle}}_{{$vehicule->immatriculation}} ({{$vehicule->marque}} {{$vehicule->modele}})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer d-flex justify-content-around">
                                                <button type="submit" class="btn btn-success"> Valider </button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                 <!-- fin modale de sélection des viles -->
                                <div class="table-responsive">
                                    <table class="table table-striped table-borderless">
                                        <thead class="thead-light">
                                            <tr class="text-center">
                                                <th>N°</th>
                                                <th>type</th>
                                                <th>Immatriculation</th>
                                                <th>Marque</th>
                                                <th>enlever</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $row=1;
                                            @endphp
                                            @foreach($coursier->Vehicules as $vehicule)
                                                <tr class="text-center">
                                                    <td>
                                                        {{$row}}
                                                    </td>
                                                    <td>
                                                        {{$vehicule->type->libelle}}
                                                    </td>
                                                    <td>
                                                        {{$vehicule->immatriculation}}
                                                    </td>
                                                    <td>
                                                        {{$vehicule->marque}}
                                                    </td>
                                                    <td>
                                                        <div class="cursor-pointer w-25 m-auto" title="Retire la zone à {{$coursier->noms}} {{$coursier->prenoms}}" data-toggle="modal" onclick="remplir('{{route('vehicule.destroy',$vehicule->id)}}','', ' désattribuer <br> {{$vehicule->type->libelle}}_{{$vehicule->immatriculation}} ({{$vehicule->type->marque}} {{$vehicule->marque}}) <br> à {{$coursier->noms}} {{$coursier->prenoms}}','button_remove2')" data-target="#danger">
                                                            <i data-feather='x-circle' class="text-danger cursor-pointer" height="24" width="24"></i>   
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
                            <!-- /zone Permissions -->
                        </div>
                        <!-- zone Permissions Ends -->
                    </div>
                    <!-- User Card & Plan Ends -->

                    <div class="row">

                        <!-- zone Permissions Starts -->
                        <div class="col-md-12">
                            <!-- zone Permissions -->
                            <div class="card">
                                <div class="card-header pb-0 pt-1 row" style="gap: 2rem;">
                                    <h4 class=" card-title">Zones Attribuées( {{$coursier->zone_coursier->count()}} )</h4>
                                    <div class="">
                                        <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#select2InModal">
                                            <i data-feather='share-2' class="mr-50"></i>
                                            <span class="d-none d-md-inline-block">
                                                Attribuer des 
                                            </span> Zones
                                        </button>
                                    </div>
                                </div>
                               <!-- modale de sélection des zones -->
                                <div class="modal fade text-left" id="select2InModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{route('details_zone.store')}}" method="POST">
                                                @csrf
                                                <input type="hidden"  name="id_coursier" value="{{$coursier->id}}">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myModalLabel1">Modale de Sélection</h4>
                                                    <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Attribuer les zones à {{$coursier->noms}} {{$coursier->prenoms}}</p>
                                                    <label for="zone">Choisir une ou plusieurs Zones</label>
                                                    <div class="form-group mb-0" id="div_zone">
                                                        <select class=" @error('zone')  is-invalid @enderror select2 form-control" name="zones[]" id="zone" multiple='multiple'>
                                                            @foreach($zones as $zone)
                                                                @if($zone->details_zone->count() > 0 )
                                                                    @php
                                                                        $pass = true;
                                                                        foreach($zone->details_zone as $detail){
                                                                            if ($detail->id_coursier == $coursier->id){
                                                                                $pass = false ; 
                                                                                break;
                                                                            }
                                                                        }
                                                                        if($pass == false){
                                                                            continue;
                                                                        }
                                                                     @endphp
                                                                @endif
                                                                <option value="{{$zone->id}}">
                                                                    {{$zone->libelle}}
                                                                    {{$zone->ville->libelle}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer d-flex justify-content-around">
                                                    <button type="submit" class="btn btn-success"> Valider </button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- fin modale de sélection des zones -->
                                <div class="table-responsive">
                                    <table class="table table-striped table-borderless">
                                        <thead class="thead-light">
                                            <tr class="text-center">
                                                <th>N°</th>
                                                <th>Nom</th>
                                                <th>enlever</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $row=1;
                                            @endphp
                                            @foreach($coursier->zone_coursier as $zone)
                                                <tr class="text-center">
                                                    <td>
                                                        {{$row}}
                                                    </td>
                                                    <td>
                                                        {{$zone->zone_affectee->libelle}}
                                                    </td>
                                                    <td>
                                                        <div class="cursor-pointer w-25 m-auto" title="Retire la zone à {{$coursier->noms}} {{$coursier->prenoms}}" data-toggle="modal" 
                                                            onclick="put_zone_id('{{$zone->zone_affectee->id}}'); remplir('{{route('details_zone.update',$coursier->id)}}','{{$coursier->noms}} {{$coursier->prenoms}}', ' désattribuer {{$zone->zone_affectee->libelle}} à ','button_remove')" data-target="#danger">

                                                        <i data-feather='x-circle' class="text-danger cursor-pointer" height="24" width="24"></i>
                                                            
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
                            <!-- /zone Permissions -->
                        </div>
                        <!-- zone Permissions Ends -->
                    </div>
                </section>

            </div>
        </div>
    </div>
    <!-- END: Content-->
<div id="button_footer" hidden>
    <form method="POST" class="destroy_form">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-gradient-success btn-success ">Confirmer</button>
    </form>
<button type="button" class="btn btn-gradient-danger btn-danger " data-dismiss="modal">Annuler</button>
</div>
<div id="button_remove" hidden>
    <form method="POST" class="destroy_form">
        @csrf
        @method('PATCH')
        <input type="hidden" class="id_zone" name="id_zone">
        <input type="hidden"  name="remove" value="1">
        <button type="submit" class="btn btn-success">Confirmer</button>
    </form>
<button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
</div>
<div id="button_remove2" hidden>
    <form method="POST" class="destroy_form">
        @csrf
        @method('DELETE')
        <input type="hidden" class="id_vehicule" name="attribuate" value="0">
        <input type="hidden"  name="remove" value="1">
        <button type="submit" class="btn btn-success">Confirmer</button>
    </form>
<button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
</div>
@endsection
@section('javascript')
    <script type="text/javascript">
        function  put_zone_id(id){
            var zones = document.querySelectorAll('.id_zone');
            for (i = 0; i < zones.length; i++) {
                zones[i].value = id;
            }
        }
        document.querySelector('#CoursierListe').classList.add('active');
    </script>
@endsection