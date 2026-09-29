@extends('client/templates/template')
@section('title')
     {{' Voir Pofil'}}
@endsection
@section('contenu')
@if(isset($user->client_utilisateur->infos_perso->cni) && $user->client_utilisateur->infos_perso->cni != null)
    <div class="content app-content bg-light-success border">
@else
    <div class="content app-content bg-light-danger border">
@endif
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Profil</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                                    </li>
                                    <li class="breadcrumb-item cursor pointer active"> Profil
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
	    <div class="content-body">
                <!-- account setting page -->
                <section id="page-account-settings">
                    <div class="text-dark">
                        <div class="pt-2 col-12">
                            <div class="row">
                                <div class="col-md-8 p-0 card mx-auto">
                                    <div class="card-header">
                                        <h4 class="card-title">Informations du compte<small class="text-muted ml-md-1">
                                            @if(isset($user->client_utilisateur->infos_perso->cni) && $user->client_utilisateur->infos_perso->cni != null)
                                            <div class="bg-light-success border text-center d-inline-block px-1">
                                                Authentifié
                                            </div>
                                            @else
                                            <div class="bg-light-danger border text-center d-inline-block px-1" title="Veuillez contacter un responsable pour l'Authentification">
                                                Non authentifié
                                            </div>
                                            @endif
                                        </small></h4>
                                    </div>
                                    <div class="card user-card mb-0">
                                        <div class="card-body px-0">
                                            <div class="text-center mx-auto  mb-2">
                                                @php
                                                    $plus = auth()->user()->client_utilisateur->type_client == 1 ? '_entreprise' : '';
                                                    $path = 'app-assets/images/avatars/client'.$plus.'.jpeg';
                                                @endphp
                                                <a class="d-none d-md-block " href="javascript:void(0);">
                                                    <img src="{{asset($path)}}" height="100" width="100" alt="User avatar" />
                                                </a>
                                                <a class="d-block d-md-none " href="javascript:void(0);">
                                                    <img src="{{asset($path)}}" height="80" width="80" alt="User avatar" />
                                                </a>
                                            </div>
                                            <div class="row col-lg-10 mx-auto">
                                                <div class="col-5 col-md-6 p-0 m-auto"><i data-feather='chevron-right' class="mr-1"></i> Noms </div>
                                                <div class="col-7 col-md-5 p-0 m-auto">
                                                    <div class="text-right p-0 col-12 m-auto">
                                                        {{$user->noms}} 
                                                        ({{$user->client_utilisateur->noms}}{{$user->client_utilisateur->Prenoms}})
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row col-lg-10 mx-auto">
                                                <div class="col-5 col-md-6 p-0 m-auto"><i data-feather='chevron-right' class="mr-1"></i> Email </div>
                                                <div class="col-7 col-md-5 p-0 m-auto">
                                                    <div class="text-right p-0 col-12 m-auto">
                                                        {{$user->email}}
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row col-lg-10 mx-auto">
                                                <div class="col-5 col-md-6 p-0 m-auto"><i data-feather='chevron-right' class="mr-1"></i> Téléphone </div>
                                                <div class="col-7 col-md-5 p-0 m-auto">
                                                    <div class="d-md-none text-right p-0 col-12 m-auto">
                                                        {{phone2($user->telephone)}}
                                                    </div>
                                                    <div class="d-md-block d-none text-right p-0 col-12 m-auto">
                                                        {{phone($user->telephone)}}
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            @if(isset($user->client_utilisateur->infos_perso->cni) && $user->client_utilisateur->infos_perso->cni != null)
                                                <div class="row col-lg-10 mx-auto">
                                                    <div class="col-5 col-md-6 p-0 m-auto"><i data-feather='chevron-right' class="mr-1"></i> Téléphone 2 </div>
                                                    <div class="col-7 col-md-5 p-0 m-auto">
                                                        <div class="text-right d-md-none  p-0 col-12 m-auto">
                                                            {{$user->client_utilisateur->infos_perso->telephone2 == null ? '...' :phone2($user->client_utilisateur->infos_perso->telephone2)}}
                                                        </div>
                                                        <div class="d-md-block d-none text-right p-0 col-12 m-auto">
                                                            {{$user->client_utilisateur->infos_perso->telephone2 == null ? '...' :phone($user->client_utilisateur->infos_perso->telephone2)}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row col-lg-10 mx-auto">
                                                    <div class="col-5 col-md-6 p-0 m-auto"><i data-feather='chevron-right' class="mr-1"></i> Né(e) le </div>
                                                    <div class="col-7 col-md-5 p-0 m-auto">
                                                        <div class="text-right p-0 col-12 m-auto">
                                                            {{$user->client_utilisateur->infos_perso->date_naissance == null ? '...' :Ladate($user->client_utilisateur->infos_perso->date_naissance)}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row col-lg-10 mx-auto">
                                                    <div class="col-5 col-md-6 p-0 m-auto"><i data-feather='chevron-right' class="mr-1"></i> Né(e) le </div>
                                                    <div class="col-7 col-md-5 p-0 m-auto">
                                                        <div class="text-right p-0 col-12 m-auto">
                                                            {{$user->client_utilisateur->infos_perso->lieu_naissance == null ? '...' :$user->client_utilisateur->infos_perso->lieu_naissance}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row col-lg-10 mx-auto">
                                                    <div class="col-5 col-md-6 p-0 m-auto"><i data-feather='chevron-right' class="mr-1"></i> CNI</div>
                                                    <div class="col-7 col-md-5 p-0 m-auto">
                                                        <div class="text-right p-0 col-12 m-auto">
                                                            {{$user->client_utilisateur->infos_perso->cni == null ? '...' :$user->client_utilisateur->infos_perso->cni}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row col-lg-10 mx-auto">
                                                    <div class="col-5 col-md-6 p-0 m-auto"><i data-feather='chevron-right' class="mr-1"></i> CNI</div>
                                                    <div class="col-7 col-md-5 p-0 m-auto">
                                                        <div class="text-right p-0 col-12 m-auto">
                                                            {{$user->client_utilisateur->infos_perso->quartier == null ? '...' :$user->client_utilisateur->infos_perso->quartier->libelle}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                            @endif
                                            <div class="col-12 text-center mt-2">
                                                <a class=" btn ml-1 btn-info mx-auto" href="{{route('Clientusers.edit',$user->id)}}">
                                                    <i data-feather="edit" class="mr-50"></i>
                                                    Editer le profil
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </section>
        </div>
    </div>
<!-- / account setting page -->
</div>

@endsection

@section('javascript')
    <script type="text/javascript">
        document.querySelector('#Profil').classList.add('active');
    </script>
@endsection