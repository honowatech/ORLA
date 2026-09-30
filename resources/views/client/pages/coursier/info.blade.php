@extends('client/templates/template')
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
                                                    <div class="d-flex flex-wrap">
                                                    </div>
                                                    <div class="d-flex flex-column ml-1 my-auto">
                                                        <div class="user-info mb-1">
                                                            <h4 class="mb-0 ">{{$coursier->noms.' '.$coursier->prenoms}}</h4>
                                                            <span class="card-text ">{{'Coursier'}}</span>
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
                                    <h4 class=" card-title">Zones ( {{$coursier->zone_coursier->count()}} )</h4>
                                    <div class="">
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-borderless">
                                        <thead class="thead-light">
                                            <tr class="text-center">
                                                <th>N°</th>
                                                <th>Nom</th>
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
        document.querySelector('#Acceuil')?.classList.add('active');
    </script>
@endsection