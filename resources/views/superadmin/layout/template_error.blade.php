<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>@yield('title')</title>
    <link rel="shortcut icon" href="{{Sa_logo()}}">
    <link rel="apple-touch-icon" href="{{Sa_logo()}}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/vendor')}}s.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/boot')}}strap.css">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/bootstrap-ext')}}ended.css">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/c')}}olors.css">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/compo')}}nents.css">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/dark-l')}}ayout.css">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/bordered-l')}}ayout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/menu/menu-types/vertical')}}-menu.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <!-- END: Custom CSS-->
    @yield('css')

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
    <!-- BEGIN: Content-->
    @yield('content')
    <!-- END: Content-->
<!-- pop-up message succes -->
@if(session()->has('message'))
<div class="modal fade modal-danger text-left" id="modals-success" tabindex="-1" role="dialog" aria-labelledby="modals-success" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div></div>
                <h3 class="modal-title text-dark" id="myModalLabel120"> Informations... </h3>
                <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close">
                    <span class="text-dark" aria-hidden="true">&times;</span>
                </button>
            </div>
        <div class="modal-body">
            <p class="text-center">
                {!!session()->get('message')!!}
            </p>
        </div>
        <div class="modal-footer" style="justify-content: center;">
            <button type="button" class="btn round btn-secondary waves-effect waves-float waves-light" data-dismiss="modal">Ok</button>
        </div>
        </div>
    </div>
</div>
@endif
<!-- modale de confirmation -->
<div class="modal fade modal-danger text-left" id="danger" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title text-secondary" id="myModalLabel120">Confirmation!</h3>
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
<div id="button_deconnection" hidden>
    <button type="button" class="btn btn-danger " data-dismiss="modal">
        <i data-feather="x"></i>
    </button>
    <form method="POST" class="p-0 destroy_form">
        @csrf
        @method('POST')
        <button type="submit" class="btn btn-success ">
            <i data-feather="check"></i>
        </button>
    </form>
</div> 

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0">
            <span class="float-md-left d-block d-md-inline-block mt-25">
                COPYRIGHT &copy; 2024
                <a class="ml-25" href="https://honowa.com" target="_blank">Honowa Technologies</a>
                <span class="d-none d-sm-inline-block">, Tous les droits reservés</span>
            </span>
            <span class="float-md-right d-none d-md-block">
            </span>
        </p>
    </footer>
    <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
    <!-- END: Footer-->

    <!-- BEGIN: Vendor JS-->
    <script src="{{asset('app-assets/vendors/js/vendors.min.js')}}"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{asset('app-assets/js/core/app-menu.js')}}"></script>
    <script src="{{asset('app-assets/js/core/app.js')}}"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    @yield('javascript')
    <!-- END: Page JS-->

    <script>
    @if(session()->has('message'))
        function success(){
            $('#modals-success').modal();
        }
    @endif
    @if(session()->has('message'))
    $(window).on('load', function() {
     success()
    })
    @endif
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })
        function remplir(lien,libelle,action,id_remplir){
            var destroy_title = document.querySelector('#destroy_title'),
                destroy_form = document.querySelectorAll('.destroy_form'),
                noms = libelle == '' ? libelle : '" '+libelle+' "';
            for (var i = 0; i < destroy_form.length; i++) {
                destroy_form[i].action = lien;
            }
            destroy_title.innerHTML = 'Vous êtes sur de vouloir '+action+' ? <br> <br> '+noms+' ';
            document.querySelector('#confirm_footer').innerHTML = document.querySelector('#'+id_remplir).innerHTML
            $('#danger').modal();
        }
    </script>
</body>
<!-- END: Body-->

</html>