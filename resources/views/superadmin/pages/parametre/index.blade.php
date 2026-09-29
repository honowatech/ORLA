@extends('superadmin/layout/template')
@section('menu')
    @include('superadmin/menu/menu')
@endsection
@section('title')
    {{'Paramètres'}}
@endsection
@section('contenu')
    <div class="content app-content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Paramètres</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('SuperAdmin.home')}}">Acceuil</a>
                                    </li>
                                    <li class="breadcrumb-item active"> 
                                        Paramètres
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <section class="mx-2">
                <h3 class="text-center mb-2">Configurer les Apis de paiement</h3>
                <div class="row">
                    @foreach($apis as $api)
                    <div class="card col-md-10 col-12 mx-auto">
                        <div class="my-1">
                            <h4 class="text-center">
                                {{$api->name}} 
                                <span data-toggle="tooltip" data-placement="right" title="" data-original-title=" @if($api->statut == 0) Indisponible @else Disponible @endif Chez le client" class="badge badge-pill badge-glow ml-1 @if($api->statut == 0) badge-danger @else badge-success @endif">
                                        @if($api->statut == 0)
                                            <i data-feather='eye-off' style="width: 14px; height: 14px;"></i>
                                        @else
                                            <i data-feather='eye' style="width: 14px; height: 14px;"></i>
                                        @endif
                                </span>
                            </h4>
                        </div>
                        <div class="row mx-1">
                            <a class="btn btn-primary btn-gradient-primary mx-auto mb-2" href="{{route('Sa-api.edit',$api->id)}}">
                                <i data-feather='tool' class="mr-50"></i> <span>configurer</span>
                            </a>
                            <button class="btn mx-auto mb-2 @if($api->statut == 1) btn-danger btn-gradient-danger @else btn-success btn-gradient-success @endif " data-toggle="modal" onclick="remplir('{{route('Sa-api.destroy',$api->id)}}',{{ Js::from(e(Sa_name($api->name))) }},@if($api->statut == 1) 'Désactiver' @else 'Activer' @endif ,'button_footer')" data-target="#danger">
                                @if($api->statut == 1)
                                <i data-feather='eye-off' class="mr-50"></i>
                                <span>
                                        Désactiver
                                </span>
                                @else
                                <i data-feather='eye' class="mr-50"></i>
                                <span>
                                        Activer
                                </span>
                                @endif
                            </button>
                        </div>
                        <div class="content-body">
                            <div class="row" id="table-hover-animation">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td class="bg-light-{{$api->key == null ? 'danger' : 'success'}}" style="max-width: 180px !important;min-width: 180px !important; width: 180px !important;">
                                                    <i data-feather="key" class="mr-50"></i> Clé Api
                                                </td>
                                                <td class="text-center">
                                                    <b>
                                                        {!!$api->key == null ? '<i data-feather="alert-triangle" class="text-danger"></i>' : e($api->key)!!}
                                                    </b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-light-{{$api->secret == null ? 'danger' : 'success'}}" style="max-width: 180px !important;min-width: 180px !important; width: 180px !important;">
                                                    <i data-feather="lock" class="mr-50"></i> Secret Api
                                                </td>
                                                <td class="text-center">
                                                    <b>
                                                        {!!$api->secret == null ? '<i data-feather="alert-triangle" class="text-danger"></i>' : e($api->secret)!!}
                                                    </b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-light-{{$api->user == null ? 'danger' : 'success'}}" style="max-width: 180px !important;min-width: 180px !important; width: 180px !important;">
                                                    <i data-feather="user" class="mr-50"></i> User Name
                                                </td>
                                                <td class="text-center">
                                                    <b>
                                                        {!!$api->user == null ? '<i data-feather="alert-triangle" class="text-danger"></i>' : e($api->user)!!}
                                                    </b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-light-{{$api->password == null ? 'danger' : 'success'}}" style="max-width: 180px !important;min-width: 180px !important; width: 180px !important;">
                                                    <i data-feather="more-horizontal" class="mr-50"></i> Mot de passe
                                                </td>
                                                <td class="text-center">
                                                    <b>
                                                        {!!$api->password == null ? '<i data-feather="alert-triangle" class="text-danger"></i>' : e(Sa_password($api->password))!!}
                                                    </b>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
    <div id="button_footer" hidden>
        <button type="button" class="mx-auto btn btn-gradient-danger btn-danger" data-dismiss="modal">
                <i data-feather="x"></i>
        </button>
        <form method="POST" class="destroy_form m-0 p-0 mx-auto">
            @csrf
            @method('DELETE')
                <button type="submit" class="btn btn-gradient-success btn-success">
                    <i data-feather="check"></i>
                </button>
        </form>
    </div>


@endsection
@section('javascript')
	<script type="text/javascript">
		document.querySelector('#parametre-index').classList.add('active');
	</script>
@endsection