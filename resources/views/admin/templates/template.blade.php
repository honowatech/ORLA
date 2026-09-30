@php
use App\Models\Users\Users;
use Illuminate\Support\Facades\Auth;
@endphp
<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <style>
        .redable{
            color: #ea5455 !important;
        }
        .flatpickr-time{
            height: 2.714rem !important;
        }
        .nav-item .active{
            box-shadow:0 0 10px 1px #87c4cc !important;
        }
        .menu-light .navigation .active a {
            background : #00CFE8 !important; 
            border-radius: 4px !important;
        }
        light .navigation > li.open:not(.menu-item-closing) > a, .main-menu.menu-light .navigation > li.sidebar-group-active > a{
            background: #dcf7ff !important;
        }
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title> @yield('title') </title>
    <link rel="apple-touch-icon" href="{{asset('app-assets/images/avatars/map-pin.png')}}>">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('app-assets/images/avatars/map-pin.png')}}">
    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.min.css') }}">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
    <!-- END: Vendor CSS-->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- END: Vendor CSS-->


    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/dark-layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/bordered-layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/menu/menu-types/vertical-menu.css') }}">



    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/forms/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/app-user.css')}}">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css ')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css ')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css ')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/rowGroup.bootstrap4.min.css ')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css ')}}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.css ')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap-extended.css ')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/colors.css ')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.css ')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/dark-layout.css ')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/bordered-layout.css ')}}">

    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">

    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
    <style type="text/css">
        .btn span ,.main-menu.menu-light .navigation .active a,.main-menu.menu-light .navigation .active a span,.badge{
            color: #ffffff !important;
        }
        .main-menu.menu-light .navigation li a{
            color: #000000 !important;
        }
        label .btn{
            margin-bottom :initial !important;
        }
        .btn{
            margin-bottom :1rem !important;
        }
        #ouverture_modal{
            opacity: 0;
        }

    </style>
    @yield('css')

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="" @if(session()->has('message')) onload="success()" @endif>

<!-- BEGIN: Header-->
<nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow">
    <div class="navbar-container d-flex content">
        <ul class="nav navbar-nav align-items-center ml-auto">  
            <li class="nav-item dropdown dropdown-user pr-1">
                <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="user-nav d-sm-flex d-none">
                        <span class="user-name font-weight-bolder"> {{ Auth::user()->noms }} </span>
                        @php
                            $utilisteur = Users::with('type_utilisateur')
                            ->findOrFail(Auth::user()->id);
                        @endphp
                        <span class="user-status">{{$utilisteur->type_utilisateur->libelle}}</span>
                    </div>
                        <div class="avatar bg-light-info avatar-bg">
                            @php
                                $table = explode(' ', Auth::user()->noms);
                                count($table) > 1 ? $avatar = $table[0][0].$table[1][0] : $avatar = $table[0][0].$table[0][1];
                            @endphp
                            <span class="avatar-content">{{$avatar}}</span>
                        </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">
                    <a class="dropdown-item" href="{{route('home')}}">
                        <i class="mr-50" data-feather="settings"></i> Settings
                    </a>
                    <a class="dropdown-item" href="{{route('home')}}">
                        <i class="mr-50" data-feather="help-circle"></i> FAQ
                    </a>

                </div>
            </li>
            <li>
                <div id="button_deconnection" hidden>
                    <form method="POST" class="destroy_form">
                        @csrf
                        @method('POST')
                        <button type="submit" class="btn btn-gradient-success btn-success round btn-lg">Confirmer</button>
                    </form>
                    <button type="button" class="btn btn-gradient-danger btn-danger round btn-lg" data-dismiss="modal">Annuler</button>
                </div>      
                    <button class=" btn  btn-icon " title="Déconnexion"  style="margin-bottom:0px !important;" type="submit"onclick="remplir('{{route('logout')}}','', 'vous déconnecter' ,'button_deconnection')">
                        <i class="redable" data-feather="power" style="width: 25px; height: 25px"></i>
                    </button>
                    
                    <button hidden class="" id="modal_danger" data-toggle="modal" data-target="#danger"></button>
            </li>
        </ul>
    </div>
<div  class="text-center" hidden id="speedexspinner">
    <div class="col-12 text-center">
        <div class="spinner-border spinner text-dark" role="status">
            <span class="sr-only"></span>
       </div>
   </div>
</div>
</nav>
<ul class="main-search-list-defaultlist-other-list d-none">
    <li class="auto-suggestion justify-content-between"><a class="d-flex align-items-center justify-content-between w-100 py-50">
            <div class="d-flex justify-content-start"><span class="mr-75" data-feather="alert-circle"></span><span>No results found.</span></div>
        </a></li>
</ul>
<!-- END: Header-->
@if( strtoupper(Auth()->user()->type_utilisateur->libelle) == strtoupper('agent'))
    @include('admin/menu/agent')
@else
    @include('admin/menu/admin')
@endif

<!-- BEGIN: Content-->
    @yield('contenu')
<!-- END: Content-->

<div class="sidenav-overlay"></div>
<div class="drag-target"></div>
<!-- modale de confirmation -->
<div class="modal fade modal-danger text-left" id="danger" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div></div>
                <h3 class="modal-title text-dark" id="myModalLabel120">Confirmation! </h3>
                <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h3 id="destroy_title" class="text-bolder text-center m-1">
                </h3>
            </div>
            <div class="modal-footer" id="confirm_footer" style="justify-content: space-around;">
            </div>
        </div>
    </div>
</div>
<!-- pop-up message succes -->
@if(session()->has('message'))
<div class="modal fade modal-danger text-left" id="modals-success" tabindex="-1" role="dialog" aria-labelledby="modals-success" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div></div>
                <h3 class="modal-title text-dark" id="myModalLabel120"> Information </h3>
                <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close" autofocus>
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <div class="modal-body">
            <p class="text-center">
                {!!session()->get('message')!!}
            </p>
        </div>
        <div class="modal-footer" style="justify-content: center;">
            <button type="button" class="btn btn-gradient-info btn-info round waves-effect waves-float waves-light" data-dismiss="modal">Terminer</button>
        </div>
        </div>
    </div>
</div>
<script type="text/javascript">
                    function success(){

                    button = document.getElementById('succes_button');
                    button.click();

                    }    
</script>
@endif
<button type="button" class="display btn btn-info waves-effect waves-float waves-light" href="#" data-target="#modals-success" data-toggle="modal" hidden id="succes_button"></button>
<!-- pop-up message succes fin -->
<!-- BEGIN: Footer-->
<footer class="footer footer-static footer-light">
    <p class="clearfix mb-0">
        <span class="float-md-left d-block d-md-inline-block mt-25">
            <span class="d-none d-sm-inline-block">COPYRIGHT</span> &copy; 2023
            <a class="ml-25" href="https://honowa.com" target="_blank">Honowa Technologie</a>
            <span class="d-none d-sm-inline-block">, Tous les droits reservés</span>
        </span>
    </p>
</footer>
<button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
<!-- END: Footer-->


<!-- BEGIN: Vendor JS-->
<script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Page JS-->
<script src="{{ asset('app-assets/js/scripts/forms/form-select2.js') }}"></script>
<!-- END: Page JS-->

<!-- BEGIN: Page Vendor JS-->
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
<script src="{{ asset('app-assets/js/core/app.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/forms/cleave/cleave.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/cleave/addons/cleave-phone.us.js') }}"></script>

<script src="{{ asset('app-assets/js/scripts/forms/form-input-mask.js') }}"></script>
<!-- END: Theme JS-->
<script>
        function remplir(lien,libelle,action,id_remplir,cliquer=1){
            var destroy_title = document.querySelector('#destroy_title'),
                destroy_form = document.querySelectorAll('.destroy_form'),
                noms = libelle == '' ? libelle : '" '+libelle+' "';
            for (var i = 0; i < destroy_form.length; i++) {
                destroy_form[i].action = lien;
            }
            destroy_title.innerHTML = 'Êtes-vous certain de vouloir'+action+' ? <br> <br> '+noms+' ';
            if(cliquer == 1){
                document.querySelector('#modal_danger').click();
            }else{
                document.querySelector('#confirm_footer'+cliquer).innerHTML = document.querySelector('#'+id_remplir).innerHTML
            }
            document.querySelector('#confirm_footer').innerHTML = document.querySelector('#'+id_remplir).innerHTML
            
        }
        function responses_ajax(id,data,lien,method,put=undefined,encode=undefined){
            var result = document.querySelector('#'+id),
                spinner = document.querySelector('#speedexspinner').innerHTML;
                if(put == undefined){
                    result.innerHTML = spinner;
                }else{
                    if (document.querySelector('#tospin')) {
                        document.querySelector('#tospin').innerHTML = spinner;
                    }else{
                        result.innerHTML = "<option value=''>Chargement...</option>"
                    }
                }
            jQuery.ajax({
                url: lien,
                type : method,
                data : { table_data : data , '_token' : "{{ csrf_token() }}" },
                success: function(response)
                {
                    if(encode == undefined){
                        result.innerHTML = response;
                    }else{
                        return  response
                    }
                    if (document.querySelector('#tospin')) {
                        document.querySelector('#tospin').innerHTML = '';
                    }
                },
                error: function(){
                    if(put == undefined){
                        result.innerHTML = "<h4 class='text-danger text-center'> Erreur !</h4><div class='w-100 text-center'>Un problème est survenu veuillez atualiser la page !!! </div>";
                    }else{
                        if (document.querySelector('#tospin')) {
                            document.querySelector('#tospin').innerHTML = "<h4 class='text-danger text-center'> Erreur !</h4><div class='w-100 text-center'>Un problème est survenu veuillez atualiser la page !!! </div>";
                        }else{
                            result.innerHTML = "<option value=''>Erreur! Veuillez actualisez la page...</option>"
                        }
                    }
                }
            });

        }
    $(window).on('load', function() {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    })

    $('#select_code_boutique_new_contrat').change(function(){
        var id_boutique = $(this).val();
        $('#loyer_new_contrat').val(
            $('#option-building-'+id_boutique).attr('title')
        );
    });

    $('#new_locataire_name').keyup(function(){
        if($(this).val() != ""){
            $("#id_locataire").val(0);
            $("#select2-id_locataire-container").html("choisir");
        }
    });
    
        function  phone(id){
            var phone_number2 = document.querySelector('#'+id+'2')
                phone_number = document.querySelector('#'+id).value.replaceAll(' ','');

            if (isNaN(phone_number)) {
                phone_number2.value = '';
                document.querySelector('#'+id).value = isNaN(parseInt(phone_number)) ? '' : parseInt(phone_number);
                phone(id);
            }else{
                if(parseInt(phone_number).toString().length == 9 && phone_number[0] == '6'){
                    document.querySelector('#'+id).value = phone_number[0]+' '+phone_number[1]+''+phone_number[2]+' '+phone_number[3]+''+phone_number[4]+' '+phone_number[5]+''+phone_number[6]+' '+phone_number[7]+''+phone_number[8]
                }else{
                    document.querySelector('#'+id).value = phone_number.replaceAll(' ','')
                }
                phone_number2.value = document.querySelector('#'+id).value.replaceAll(' ','');
            }
        }
</script>
@yield('javascript')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
    <script src="{{asset('app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
    <script src="{{asset('app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
</body>
<!-- END: Body-->

</html>
