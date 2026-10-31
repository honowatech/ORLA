@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')

@section('title')
{{'Modifier un tronçon de route'}}
@endsection
@section('contenu')
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-12 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Modifier le tronçon {{ $troncon->quartier_depart->libelle ?? '' }} → {{ $troncon->quartier_arrivee->libelle ?? '' }}</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a></li>
                                <li class="breadcrumb-item"><a href="{{route('troncon.index')}}">Tronçons</a></li>
                                <li class="breadcrumb-item active">Modifier</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="multiple-column-form">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Formulaire</h4>
                            </div>
                            <div class="card-body">
                                <form class="form" action="{{route('troncon.update', $troncon->id)}}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    @include('multi.pages.troncon.form', ['troncon' => $troncon])
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
