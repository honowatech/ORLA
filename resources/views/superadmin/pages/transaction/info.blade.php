@extends('superadmin/layout/template_error')
@section('title')
{{'Transaction'}} {{$statuts_valeurs[$transaction->statut]}}e
@endsection
@section('content')

<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-12 col-12 m-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                            <h2 class="content-header-title mb-0"> Transaction {{$statuts_valeurs[$transaction->statut]}}e </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body m-2">
            <section id="multiple-column-form">
                <h3 class="text-center mb-2"> 
                    <i data-feather="{{$statuts_icon[$transaction->statut]}}" class="text-{{$statuts[$transaction->statut]}}" style="height: 80px; width: 80px;"></i>
                </h3>
                <div class="row">
                        <div class="col-lg-8 col-sm-10 col-12 mx-auto">
                            <div class="card">
                                <div class="card-header">
                                    <h2 class="mx-auto">
                                        Informations de la transaction
                                    </h2>
                                </div>
                                <div class="card-body">
                                    <div>
                                        @if($transaction->infos->count()==0)
                                        <div class="row justify-content-center mx-1 mb-2">
                                            <div>
                                                <div class="text-center"><i data-feather='alert-triangle' class="mr-50 text-danger"></i></div>
                                                <b> Aucune information disponible</b>
                                            </div>
                                        </div>
                                        @endif
                                    @foreach($transaction->infos as $information)
                                        <div class="row justify-content-between mx-1 mb-2">
                                            <div>
                                                    <b> {{Sa_name(str_replace('_',' ',$information->name))}}</b>
                                            </div>
                                            <div>
                                                <span class="text-truncate">
                                                    @if($information->name == 'phone')
                                                        {{Sa_phone($information->value)}}
                                                    @elseif($information->name == 'statut')
                                                        <span class="badge-{{$statuts[$information->value]}} badge">
                                                            {{$statuts_valeurs[$information->value]}}
                                                        </span>
                                                    @else
                                                        {{$information->value}}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                        <div class="text-center">
                                            <a class="btn btn-primary btn-gradient-primary" href="{{Sa_site_login()}}">
                                                <i data-feather='home' class="mr-50"></i>
                                                @if(check_superadmin() != 'true') 
                                                Accéder au site
                                                @else
                                                Voir le site
                                                @endif
                                            </a>
                                        </div>
                                        @if(check_superadmin() != 'true') 
                                        <hr>
                                        <div class="row col-md-8 col-lg-6 mx-auto" style="gap: 2rem;">
                                            <div class="mx-auto">
                                                <button type="submit" class="btn btn-outline-danger" onclick="remplir('{{route('logout')}}','', 'vous déconnecter' ,'button_deconnection')">
                                                    <i data-feather="power" class="mr-50"></i>Se Déconnecter
                                                </button>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </section>
       </div>
    </div>
</div>
@endsection

@section('javascript')
    <script type="text/javascript">
    </script>
@endsection