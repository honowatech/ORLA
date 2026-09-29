@extends('admin/templates/template')
@section('title')
Informations sur {{$coursier->noms}} {{$coursier->prenoms}}
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
                            <h2 class="content-header-title float-left mb-0">Infos sur {{$coursier->noms.' '.$coursier->prenoms}}</h2>
                            <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{route('coursiers.index')}}">Liste des Coursiers</a>
                            </li>
                            <li class="breadcrumb-item active">Infos sur {{$coursier->noms}} {{$coursier->prenoms}}
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
                                        <div class="col-bg-6 col-lg-6 d-flex flex-column justify-content-between border-container-lg">
                                            <div class="user-avatar-section">
                                                <div class="d-flex justify-content-start">
                                                    <img class="img-fluid rounded" src="{{asset('app-assets/images/avatars/user.png')}}" height="104" width="125" alt="User avatar" />
                                                    <div class="d-flex flex-column ml-1">
                                                        <div class="user-info mb-1">
                                                            <h4 class="mb-0 ">{{$coursier->noms.' '.$coursier->prenoms}}</h4>
                                                            <span class="card-text ">{{'Coursier'}}</span>
                                                        </div>
                                                        <div class="d-flex flex-wrap">
                                                            <a href="{{route('coursiers.edit',$coursier->id)}}" class="round btn btn-outline-dark">Modifier</a>
                                                            <button class="btn ml-md-1 ml-bg-1 @if($coursier->statut == 1) round btn-outline-danger  @else btn-outline-success  @endif " data-toggle="modal" onclick="remplir('{{route('coursiers.destroy',$coursier->id)}}','{{$coursier->noms.' '.$coursier->prenoms}}', @if($coursier->statut == 1) 'Désactiver' @else 'Activer' @endif ,'button_footer')" data-target="#danger">
                                                            @if($coursier->statut == 1)
                                                                    Désactiver
                                                            @else
                                                                    Activer
                                                            @endif
                                                        </button>
                                                        </div>
                                                        <div class="d-flex flex-wrap">
                                                            <button data-toggle="modal" data-target="#select2InModal" class="round col-12 btn btn-dark">Attribuer les Zones</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center user-total-numbers">
                                                <div class="d-flex align-items-center mr-2">
                                                    <div class="color-box bg-light-dark">
                                                        <i data-feather='map-pin' class="text-info"></i>
                                                    </div>
                                                    <div class="ml-1">
                                                        <h5 class="mb-0 text-center">{{$coursier->zone_coursier->count()}}</h5>
                                                        <small>Zones Associées</small>
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
                                                    <span class="badge badge-pill @if($coursier->statut == 0)badge-light-danger @else badge-light-success @endif">
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
                                                <div class="d-flex flex-wrap">
                                                    <div class="user-info-title">
                                                        <i data-feather="phone" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Contacts</span>
                                                    </div>
                                                    <p class="card-text mb-0">(+237) {{$coursier->telephone}}</p>
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

                        <!-- zone Permissions Starts -->
                        <div class="col-md-12">
                            <!-- zone Permissions -->
                            <div class="card">
                                <div class="card-header row" style="gap: 2rem;">
                                    <h4 class=" card-title">Zones Attribuées( {{$coursier->zone_coursier->count()}} )</h4>
                                    <div class="">
                                        <button type="button" class="btn round btn-dark" data-toggle="modal" data-target="#select2InModal">
                                            Attribuer des zones
                                        </button>
                                    </div>
                                </div>
                                            <!-- modale de sélection des viles -->
                                                <div class="modal fade text-left" id="select2InModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
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
                                                                    <select class=" @error('zone')  is-invalid @enderror select2 form-control" id="zone" multiple='multiple' onchange="remplir_vrai_input()">
                                                                        @foreach($zones as $zone)
                                                                            <option value="{{$zone->id}}">
                                                                                {{$zone->libelle}}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer d-flex justify-content-around">
                                                                <form action="{{route('details_zone.store')}}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="zones" id="vrai_input" required>
                                                                    <input type="hidden"  name="id_coursier" value="{{$coursier->id}}">
                                                                    <button type="submit" class="btn btn-outline-success"> Valider </button>
                                                                </form>
                                                                <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Annuler</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <!-- fin modale de sélection des viles -->
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
                                                            onclick="put_zone_id('{{$zone->zone_affectee->id}}'); remplir('{{route('details_zone.update',$coursier->id)}}','{{$coursier->noms}} {{$coursier->prenoms}}', 'désattribuer {{$zone->zone_affectee->libelle}} à','button_remove')" data-target="#danger">

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
                    <!-- User Timeline & Permissions Starts -->
                    <div class="row">
                    </div>
                    <!-- User Timeline & Permissions Ends -->
                </section>

            </div>
        </div>
    </div>
    <!-- END: Content-->
<div id="button_footer" hidden>
    <form method="POST" class="destroy_form">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-gradient-success btn-success round btn-lg">Confirmer</button>
    </form>
<button type="button" class="btn btn-gradient-danger btn-danger round btn-lg" data-dismiss="modal">Annuler</button>
</div>
<div id="button_remove" hidden>
    <form method="POST" class="destroy_form">
        @csrf
        @method('PATCH')
        <input type="hidden" class="id_zone" name="id_zone">
        <input type="hidden"  name="remove" value="1">
        <button type="submit" class="btn btn-outline-info">Confirmer</button>
    </form>
<button type="button" class="btn btn-outline-danger" data-dismiss="modal">Annuler</button>
</div>
@endsection
@section('javascript')
    <script type="text/javascript">
        function remplir_vrai_input(){
            $(document).ready(function() {
              // Récupération des données sélectionnées
              const zone = $("#zone").val();

              // Affichage des données sélectionnées dans la console
              valeur = '';
              virgule = ','
              for (var i = zone.length - 1; i >= 0; i--) {
                    i == 0 ? virgule = '' : virgule = ',';
                 valeur += zone[i]+virgule;
              }
                document.querySelector('#vrai_input').value = valeur;
                console.log(document.querySelector('#vrai_input').value)
            });
        }
        function  put_zone_id(id){
            var zones = document.querySelectorAll('.id_zone');
            for (i = 0; i < zones.length; i++) {
                zones[i].value = id;
            }
        }
        document.querySelector('#CoursierListe').classList.add('active');
    </script>
@endsection