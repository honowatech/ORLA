@extends('admin/templates/template')

@section('title')
{{'Détails sur '}} {{$zones->libelle}}
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
                            <h2 class="content-header-title float-left mb-0">Infos la zone "{{$zones->libelle}}"</h2>
                            <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{route('zone.index')}}">Liste des Zones</a>
                            </li>
                            <li class="breadcrumb-item active">Infos sur la zone "{{$zones->libelle}}"
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
                    <!-- user Card & Plan Starts -->
                    <div class="row">
                        <!-- user Card starts-->
                        <div class="col-md-12">
                            <div class="card user-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-bg-6 col-lg-6 d-flex flex-column justify-content-between border-container-lg">
                                            <div class="zone-avatar-section">
                                                <div class="d-flex justify-content-start">
                                                    <img class="img-fluid rounded" src="{{asset('app-assets/images/avatars/map-pin.png')}}" height="104" width="104" alt="User avatar" />
                                                    <div class="d-flex flex-column ml-1">
                                                        <div class="zone-info mb-1">
                                                            <h4 class="mb-0 ml-2">{{$zones->libelle}}</h4>
                                                            <span class="card-text ml-3"> de {{$zones->ville->libelle}}</span>
                                                        </div>
                                                        <div class="d-flex flex-wrap">
                                                            <a href="{{route('zone.edit',$zones->id)}}" class="btn round btn-dark ml-2">Modifier</a>
                                                            <button class=" btn ml-2 @if($zones->statut == 1) round btn-danger  @else btn-outline-success  @endif " data-toggle="modal" onclick="remplir('{{route('zone.destroy',$zones->id)}}',{{ Js::from(e($zones->libelle)) }}, @if($zones->statut == 1) 'Désactiver' @else 'Activer' @endif ,'button_footer')" data-target="#danger">
                                                            @if($zones->statut == 1)
                                                                    Désactiver
                                                            @else
                                                                    Activer
                                                            @endif
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
                                                        <i data-feather='at-sign' class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Libellé</span>
                                                    </div>
                                                    <p class="card-text mb-0">{{$zones->libelle}}</p>
                                                </div>
                                                <div class="d-flex flex-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="check" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Status</span>
                                                    </div>
                                                    <span class="badge badge-pill @if($zones->statut == 0)badge-light-danger @else badge-light-success @endif">
                                                        @if($zones->statut == 0)
                                                            Désactivé
                                                        @else 
                                                            Actif
                                                        @endif
                                                    </span>
                                                    
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
                                                        <i data-feather="map-pin" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Ville</span>
                                                    </div>
                                                    <p class="card-text mb-0"> {{$zones->ville->libelle}} </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /zone Card Ends-->
                        <!-- /Plan CardEnds -->
                    </div>
                    <!-- zone Card & Plan Ends -->

                    <!-- zone Timeline & Permissions Starts -->
                    <div class="row">

                        <!-- zone Permissions Starts -->
                        <div class="col-md-12">
                            <!-- zone Permissions -->
                            <div class="card">
                                <div class="card-header row" style="gap: 2rem;">
                                    <h4 class=" card-title">Quartiers Associés ( {{$zones->quartiers->count()}} )</h4>
                                    <div class="">
                                        <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#select2InModal">
                                            Ajouter des quartiers
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
                                                                <p>Sélectionner un ou plusiers Quartiers</p>
                                                            
                                                                <label for="id_quartier_associe">Choisir des Quartiers</label>
                                                                <div class="form-group mb-0" id="div_id_quartier_associe">
                                                                    <select class=" @error('id_quartier_associe')  is-invalid @enderror select2 form-control" id="id_quartier_associe" multiple='multiple' onchange="remplir_vrai_input()">
                                                                        @foreach($quartiers_libres as $quartier_libre)
                                                                            <option value="{{$quartier_libre->id}}">
                                                                                {{$quartier_libre->libelle}}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer d-flex justify-content-around">
                                                                <form action="{{route('zone.destroy',$zones->id)}}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <input type="hidden" name="id_quartier_associe" id="vrai_input" required>
                                                                    <input type="hidden"  name="add" value="1">
                                                                    <button type="submit" class="btn btn-outline-success"> Valider </button>
                                                                </form>
                                                                <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Annuller</button>
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
                                                $row=1
                                            @endphp
                                            @foreach($zones->quartiers as $quartier)
                                                <tr class="text-center">
                                                    <td>
                                                        {{$row}}
                                                    </td>
                                                    <td>
                                                        {{$quartier->libelle}}
                                                    </td>
                                                    <td>
                                                        <div class="cursor-pointer w-25 m-auto" title="Enlever le quartier de {{$zones->libelle}}" data-toggle="modal" 
                                                            onclick="put_quartier_id('{{$quartier->id}}'); remplir('{{route('zone.destroy',$zones->id)}}',{{ Js::from(e($zones->libelle)) }}, {{ Js::from('Enlever '.e($quartier->libelle).' de la zone') }},'button_remove')" data-target="#danger">

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
                    <!-- zone Timeline & Permissions Ends -->
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
        @method('DELETE')
        <input type="hidden" class="id_quartier" name="id_quartier">
        <input type="hidden"  name="remove" value="1">
        <button type="submit" class="btn btn-gradient-success btn-success round btn-lg">Confirmer</button>
    </form>
    <button type="button" class="btn btn-gradient-danger btn-danger round btn-lg" data-dismiss="modal">Annuler</button>
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
        function  put_quartier_id(id){
            var quartiers = document.querySelectorAll('.id_quartier');
            for (i = 0; i < quartiers.length; i++) {
                quartiers[i].value = id;
            }
        }
		document.querySelector('#zoneListe').classList.add('active');
	</script>
@endsection