@extends('admin/templates/template')

@section('title')
{{'Détails sur '}} {{$user->noms}}
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
                            <h2 class="content-header-title float-left mb-0">Infos sur {{$user->noms}}</h2>
                            <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{route('users.index')}}">Liste des Utilisateurs</a>
                            </li>
                            <li class="breadcrumb-item active">Infos sur {{$user->noms}}
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
                                                    <img class="img-fluid rounded" src="{{asset('app-assets/images/avatars/user.png')}}" height="104" width="104" alt="User avatar" />
                                                    <div class="d-flex flex-column ml-1">
                                                        <div class="user-info mb-1">
                                                            <h4 class="mb-0 ml-1">{{$user->noms}}</h4>
                                                            <span class="card-text ml-1">{{$user->email}}</span>
                                                        </div>
                                                        <div class="d-flex flex-wrap">
                                                            <a href="{{route('users.edit',$user->email)}}" class="btn btn-outline-dark ml-1">Modifier</a>
                                                            <button class=" btn ml-1 @if($user->statut == 1) btn-outline-danger  @else btn-outline-success  @endif " data-toggle="modal" onclick="remplir('{{route('users.destroy',$user->id)}}',{{ Js::from(e($user->noms)) }}, @if($user->statut == 1) 'Désactiver' @else 'Activer' @endif ,'button_footer')" data-target="#danger">
                                                            @if($user->statut == 1)
                                                                    Désactiver
                                                            @else
                                                                    Activer
                                                            @endif
                                                        </button>
                                                        </div>
                                                        <div>
                                                            <button class="text-danger btn btn-danger" data-toggle="modal" onclick="remplir('{{route('password.destroy',$user->id)}}',{{ Js::from(e($user->noms)) }}, ' Réinitialiser le mot de passe de ' ,'button_footer')" data-target="#danger"> Réinitialiser</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center user-total-numbers">
                                                <div class="d-flex align-items-center mr-2">
                                                    <div class="color-box bg-light-info">
                                                        <i data-feather='shopping-bag' class="text-info"></i>
                                                    </div>
                                                    <div class="ml-1">
                                                        <h5 class="mb-0 text-center">{{$user->commandes_enregistrees->count()}}</h5>
                                                        <small>Commandes enregistrées</small>
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
                                                    <p class="card-text mb-0">{{$user->noms}}</p>
                                                </div>
                                                <div class="d-flex flex-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="check" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Status</span>
                                                    </div>
                                                    <span class="badge badge-pill @if($user->statut == 0)badge-light-danger @else badge-light-success @endif">
	                                            		@if($user->statut == 0)
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
                                                    <p class="card-text mb-0">{{$user->type_utilisateur->libelle}}</p>
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
                                                    <p class="card-text mb-0">(+237) {{$user->telephone}}</p>
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

                    <!-- User Timeline & Permissions Starts -->
                    <div class="row">

                        <!-- User Permissions Starts -->
                        <div class="col-md-12">
                            <!-- User Permissions -->
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Permissions</h4>
                                </div>
                                <p class="card-text ml-2">Permission accordées Au rôle " {{$user->type_utilisateur->libelle}} " </p>
                                <div class="table-responsive">
                                    <table class="table table-striped table-borderless">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Module</th>
                                                <th>Lecture</th>
                                                <th>Ecriture</th>
                                                <th>Création</th>
                                                <th>Suppression</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td> {{$user->type_utilisateur->libelle}} </td>
                                                <td>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="admin-read" 
                                                        @if( $user->type_utilisateur->id == 1 || $user->type_utilisateur->id == 2 || $user->type_utilisateur->id == 3 || $user->type_utilisateur->id == 4)
                                                        	checked
                                                        @endif
                                                        disabled />
                                                        <label class="custom-control-label" for="admin-read"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="admin-write" disabled 
                                                        @if( $user->type_utilisateur->id == 1 || $user->type_utilisateur->id == 2 || $user->type_utilisateur->id == 3 || $user->type_utilisateur->id == 4)
                                                        	checked
                                                        @endif />
                                                        <label class="custom-control-label" for="admin-write"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="admin-create" disabled 
                                                        @if( $user->type_utilisateur->id == 1 || $user->type_utilisateur->id == 2 || $user->type_utilisateur->id == 4)
                                                        	checked
                                                        @endif />
                                                        <label class="custom-control-label" for="admin-create"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="admin-delete" disabled 
                                                        @if( $user->type_utilisateur->id == 1 || $user->type_utilisateur->id == 2)
                                                        	checked
                                                        @endif/>
                                                        <label class="custom-control-label" for="admin-delete"></label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /User Permissions -->
                        </div>
                        <!-- User Permissions Ends -->
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
@endsection
@section('javascript')
	<script type="text/javascript">
		document.querySelector('#UserListe')?.classList.add('active');
	</script>
@endsection