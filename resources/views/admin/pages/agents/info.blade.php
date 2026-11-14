@extends('admin/templates/template')

@section('title')
{{'Infos sur '}} {{$agent->noms.' '.$agent->prenoms}}
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
                            <h2 class="content-header-title float-left mb-0">{{ __('Infos sur') }} {{$agent->noms.' '.$agent->prenoms}}</h2>
                            <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">{{ __('Acceuil') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{route('agents.index')}}">{{ __('Liste des agents') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('Infos sur') }} {{$agent->noms.' '.$agent->prenoms}}
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
                                                    <img class="img-fluid rounded" src="{{asset('app-assets/images/avatars/user.png')}}" height="104" width="104" alt="{{ __('User avatar') }}" />
                                                    <div class="d-flex flex-column ml-1">
                                                        <div class="user-info mb-1">
                                                            <h4 class="mb-0 ml-1">{{$agent->noms.' '.$agent->prenoms}}</h4>
                                                            <span class="card-text ml-1">{{'Agent'}}</span>
                                                        </div>
                                                        <div class="d-flex flex-wrap">
                                                            <a href="{{route('agents.edit',$agent->id)}}" class="btn btn-outline-dark ml-1">{{ __('Modifier') }}</a>
                                                            <button class=" btn ml-1 @if($agent->statut == 1) btn-outline-danger  @else btn-outline-success  @endif " data-toggle="modal" onclick="remplir('{{route('coursiers.destroy',$agent->id)}}','{{$agent->noms.' '.$agent->prenoms}}', @if($agent->statut == 1) '{{ __('Désactiver') }}' @else '{{ __('Activer') }}' @endif ,'button_footer')" data-target="#danger">
                                                            @if($agent->statut == 1)
                                                                    {{ __('Désactiver') }}
                                                            @else
                                                                    {{ __('Activer') }}
                                                            @endif
                                                        </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center user-total-numbers">
                                                <div class="d-flex align-items-center mr-2">
                                                    <div class="color-box bg-light-dark">
                                                        <i data-feather='shopping-bag'></i>
                                                    </div>
                                                    <div class="ml-1">
                                                        <h5 class="mb-0 text-center">{{$commandes_enregistrees}}</h5>
                                                        <small>{{ __('Commandes enregistrées') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-bg-6 col-lg-6 mt-2 mt-xl-0">
                                            <div class="user-info-wrapper">
                                                <div class="d-flex flex-wrap">
                                                    <div class="user-info-title">
                                                        <i data-feather="user" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">{{ __('Noms') }}</span>
                                                    </div>
                                                    <p class="card-text mb-0">{{$agent->noms.' '.$agent->prenoms}}</p>
                                                </div>
                                                <div class="d-flex flex-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="check" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">{{ __('Status') }}</span>
                                                    </div>
                                                    <span class="badge badge-pill @if($agent->statut == 0)badge-light-danger @else badge-light-success @endif">
                                                        @if($agent->statut == 0)
                                                            {{ __('Désactivé') }}
                                                        @else 
                                                            {{ __('Actif') }}
                                                        @endif
                                                    </span>
                                                    
                                                </div>
                                                <div class="d-flex flex-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="star" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">{{ __('Rôle') }}</span>
                                                    </div>
                                                    <p class="card-text mb-0">{{'Agent'}}</p>
                                                </div>
                                                <div class="d-flex flex-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="flag" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">{{ __('Pays') }}</span>
                                                    </div>
                                                    <p class="card-text mb-0">{{'Cameroun'}}</p>
                                                </div>
                                                <div class="d-flex flex-wrap">
                                                    <div class="user-info-title">
                                                        <i data-feather="phone" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">{{ __('Contacts') }}</span>
                                                    </div>
                                                    <p class="card-text mb-0">(+237) {{$agent->telephone}}</p>
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
                                    <h4 class="card-title">{{ __('Permissions') }}</h4>
                                </div>
                                <p class="card-text ml-2">{{ __('Permission accordées Au rôle " :v1 "', ['v1' => 'Agent']) }} </p>
                                <div class="table-responsive">
                                    <table class="table table-striped table-borderless">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>{{ __('Module') }}</th>
                                                <th>{{ __('Lecture') }}</th>
                                                <th>{{ __('Ecriture') }}</th>
                                                <th>{{ __('Création') }}</th>
                                                <th>{{ __('Suppression') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td> {{'Agent'}} </td>
                                                <td>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="admin-read" checked disabled />
                                                        <label class="custom-control-label" for="admin-read"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="admin-write" disabled
                                                            checked />
                                                        <label class="custom-control-label" for="admin-write"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="admin-create" checked disabled />
                                                        <label class="custom-control-label" for="admin-create"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="admin-delete" checked disabled />
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
        <button type="submit" class="btn btn-gradient-success btn-success round btn-lg">{{ __('Confirmer') }}</button>
    </form>
<button type="button" class="btn btn-gradient-danger btn-danger round btn-lg" data-dismiss="modal">{{ __('Annuler') }}</button>
</div>
@endsection
@section('javascript')
    <script type="text/javascript">
        document.querySelector('#AgentsListe').classList.add('active');
    </script>
@endsection