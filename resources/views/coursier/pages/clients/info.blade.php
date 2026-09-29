@extends('coursier/templates/template')
@section('title')
    Informations sur le client {{$client->type__client->libelle}} {{$client->noms}}
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
                            <h2 class="content-header-title float-left mb-0">Infos sur {{$client->noms.' '.$client->Prenoms}}</h2>
                            <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item active">Infos sur {{$client->noms.' '.$client->Prenoms}}
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
                                        <div class="col-bg-6 col-sm-10 col-lg-6 mt-2 mt-xl-0 mx-auto d-flex flex-column justify-content-between border-container-lg">
                                            <div class="user-avatar-section">
                                                <div class="d-flex justify-content-start">
                                                    <img class="img-fluid rounded" src="{{asset('app-assets/images/avatars/user.png')}}" height="140" width="140" alt="User avatar" />
                                                    <div class="my-auto">
                                                        <div class="user-info mb-1">
                                                            <h4 class="mb-0 ">{{$client->noms.' '.$client->Prenoms}}</h4>
                                                            <span class="card-text ">{{'Client'}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-5 col-10 col-md-6 col-sm-8 col-md-6 col-lg-6 mt-2 mt-xl-0 mx-auto">
                                            <div class="user-info-wrapper">
                                                <div class="d-flex flex-lg-wrap">
                                                    <div class="user-info-title">
                                                        <i data-feather="user" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Noms</span>
                                                    </div>
                                                    <p class="card-text mb-0"><span class="d-none d-lg-inline-block">{{$client->noms}} </span> {{$client->Prenoms}}</p>
                                                </div>
                                                <div class="d-flex flex-lg-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="check" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Status</span>
                                                    </div>
                                                    <span class="badge badge-pill @if($client->statut == 0)badge-light-danger @else badge-light-success @endif">
                                                        @if($client->statut == 0)
                                                            Désactivé
                                                        @else 
                                                            Actif
                                                        @endif
                                                    </span>
                                                    
                                                </div>
                                                <div class="d-flex flex-lg-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="star" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Rôle</span>
                                                    </div>
                                                    <p class="card-text mb-0"><span class="d-none d-lg-inline-block">{{'Client'}}</span> ({{$client->Type__client->libelle}}) </p>
                                                </div>
                                                <div class="d-flex flex-lg-wrap my-50">
                                                    <div class="user-info-title">
                                                        <i data-feather="flag" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Pays</span>
                                                    </div>
                                                    <p class="card-text mb-0">{{'Cameroun'}}</p>
                                                </div>
                                                <div class="d-flex flex-lg-wrap">
                                                    <div class="user-info-title">
                                                        <i data-feather="phone" class="mr-1"></i>
                                                        <span class="card-text user-info-title font-weight-bold mb-0">Contacts</span>
                                                    </div>
                                                    <p class="card-text mb-0"><span class="d-none d-lg-inline-block">(+237)</span> {{phone2($client->telephone)}}</p>
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

    @if(strtoupper($client->type__client->libelle)==strtoupper('entreprise'))

                    <div class="row">

                        <!-- boutique Permissions Starts -->
                        <div class="col-md-12">
                            <!-- boutique Permissions -->
                            <div class="card">
                                <div class="col-lg-6 col-12">
                                    <div class="card-header row">
                                        <h4 class=" card-title">
                                             Boutiques ( {{$client->boutiques->count()}} )
                                        </h4>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-borderless">
                                        <thead class="thead-light">
                                            <tr class="text-center">
                                                <th>N°</th>
                                                <th>Libelle</th>
                                                <th>quartier</th>
                                                <th>status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $row=1;
                                            @endphp
                                            @foreach($client->boutiques as $boutique)
                                                <tr class="text-center">
                                                    <td>
                                                        {{$row}}
                                                    </td>
                                                    <td>
                                                        {{$boutique->libelle}}
                                                    </td>
                                                    <td>
                                                        {{$boutique->quartier_boutique!=null?$boutique->quartier_boutique->libelle:'Speedex'}}
                                                    </td>
                                                    <td>  
                                                        <span class="badge badge-pill @if($boutique->statut == 0)badge-light-danger @else badge-light-success @endif">
                                                            @if($boutique->statut == 0)
                                                                Désactivée
                                                            @else 
                                                                Active
                                                            @endif
                                                        </span>
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
                            <!-- /boutique Permissions -->
                        </div>
                        <!-- boutique Permissions Ends -->
                    </div>
    @endif
                </section>
            </div>
        </div>
    </div>
    <!-- END: Content-->
<div id="button_footer" hidden>
<button type="button" class="btn btn-danger round" data-dismiss="modal">Annuler</button>
    <form method="POST" class="destroy_form">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-success round">Confirmer</button>
    </form>
</div>
@endsection
@section('javascript')
    <script type="text/javascript">
    @if(strtoupper($client->type__client->libelle)==strtoupper('entreprise'))
        @if(session()->has('erreur'))
            document.querySelector('#new_building').click();
        @endif
    @endif
        document.querySelector('#Acceuil').classList.add('active');
    </script>
@endsection