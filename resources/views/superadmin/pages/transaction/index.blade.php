@extends('superadmin/layout/template')
@section('menu')
    @include('superadmin/menu/menu')
@endsection
@section('title')
    {{'Liste des Clients'}}
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
                            <h2 class="content-header-title float-left mb-0">Liste des Clients</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('SuperAdmin.home')}}">Acceuil</a>
                                    </li>
                                    <li class="breadcrumb-item active"> 
                                        Liste des Clients
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <div class="card px-1 mx-auto">

            <div class="content-body"> 
                <div class="row justify-content-between bg-light-secondary">
                    <div class="mx-auto mx-sm-0">
                        <a class="btn btn-primary btn-gradient-primary m-1" href="{{route('Sa-client.create')}}">
                            <i data-feather="plus"></i>
                        </a>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-10 mx-auto mx-sm-0 mt-1">
                        <div class="form-group">
                            <div class="input-group input-group-merge">
                                <div class="input-group-prepend ">
                                    <span class="input-group-text"><i data-feather='search'></i></span>
                                </div>
                                <input oninput="put_1()" type="search" id="search" class="form-control" name="recherche" value="" placeholder="Rechercher">
                            </div>
                        </div>
                        <input type="hidden" name="page" id="page" value="">
                    </div>
                </div>
            </div>
                <!-- Table without card start -->
            <div class="p-0" id="tableau">
            </div>
    </div>
</div>
</div>                         
</div>
</div>
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
        function mettre(number_page){
           document.querySelector('#page').value = number_page;
           search()
        }
        function put_1(){
           document.querySelector('#page').value = 1;
           search()
        }
        function search(){
            var valeur_search = document.querySelector('#search').value,
                number_page = document.querySelector('#page').value;
            response_ajax('tableau',number_page,valeur_search,'','{{route('Sa-client.index_ajax')}}','GET')
        }
        $(window).on('load', function() {
            document.querySelector('#page').value = 1;
            search()
        })
		document.querySelector('#client-index').classList.add('active');
	</script>
@endsection