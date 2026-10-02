@extends('superadmin/layout/template_error')
@section('menu')
@endsection
@section('title')
{{ 'Espace suspendu' }}
@endsection

@section('css')
@endsection
@section('content')
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="row">
                    <div class="col-12 p-2 p-md-4">
                        <section class="mx-2">
                            <h3 class="text-center mb-2 mb-md-4"> <i data-feather="alert-triangle" class="text-danger" style="height: 120px; width: 120px;"></i></h3>
                            <div class="row">
                                <div class="card col-md-10 col-12 py-2 m-auto">
                                    <div class="my-1">
                                        <h4 class="text-center">
                                            L'espace de <b>{{ $entreprise->name }}</b> a été <span class="text-danger">suspendu</span>
                                        </h4>
                                    </div>
                                    <div class="content-body text-center">
                                        <div class="col-lg-6 col-md-8 col-sm-10 mx-auto">
                                            <p>
                                                Pour réactiver votre espace, veuillez contacter <a href="{{ config('site.editeur.url') }}/contact">{{ config('site.editeur.nom') }}</a> via :
                                            </p>
                                            <div class="text-center my-2 row col-md-10 mx-auto">
                                                @if(count($telephones))
                                                <p>
                                                    <i data-feather="phone" class="mr-50"></i> Téléphones : {{ implode(' / ', array_map('Sa_phone', $telephones)) }}.
                                                </p>
                                                @endif
                                                @if($email)
                                                <p>
                                                    <i data-feather="mail" class="mr-50"></i> Email : {{ $email }}.
                                                </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row col-md-8 col-lg-6 mb-3 mx-auto" style="gap: 2rem;">
                                            <a class="btn btn-primary btn-gradient-primary mx-auto" href="{{ url()->current() }}">
                                                <i data-feather="loader" class="mr-50"></i>Actualiser
                                            </a>
                                        </div>
                                        <hr>
                                        <div class="row col-md-8 col-lg-6 mx-auto" style="gap: 2rem;">
                                            <div class="mx-auto">
                                                <button type="submit" class="btn btn-outline-danger" onclick="remplir('{{ route('logout') }}','', 'vous déconnecter' ,'button_deconnection')">
                                                    <i data-feather="power" class="mr-50"></i>Se déconnecter
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- END: Content-->
@endsection

@section('javascript')
@endsection
