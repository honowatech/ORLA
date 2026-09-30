<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <style>
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>
        @yield('title')
    </title>
    <link rel="shortcut icon" href="{{Sa_logo()}}">
    <link rel="apple-touch-icon" href="{{Sa_logo()}}">
    
    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/charts/apexcharts.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.min.css') }}">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
    <!-- END: Vendor CSS-->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- END: Vendor CSS-->


    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
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
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css ')}}">
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/spinner/jquery.bootstrap-touchspin.css')}}">
    <style type="text/css">
        .vertical-layout.vertical-menu-modern.menu-expanded .main-menu .navigation li.has-sub > a:after {
            margin-right: 1rem;
        }
    </style>
    @yield('css')

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="">

<!-- BEGIN: Header-->
<nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow">
    <div class="navbar-container d-flex content">
        <div class="bookmark-wrapper d-flex align-items-center">
            <ul class="nav navbar-nav d-xl-none">
                <li class="nav-item"><a class="nav-link menu-toggle" href="javascript:void(0);"><i class="ficon" data-feather="menu"></i></a></li>
            </ul>
        </div>
        <ul class="nav navbar-nav align-items-center ml-auto">
            <li class="nav-item dropdown dropdown-user">
                <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="user-nav d-sm-flex d-none">
                        <span class=" round user-name font-weight-bolder" alt="avatar" height="40" width="40"> {{ session()->get('SuperAdmin_infos')['infos']->name }} </span> 
                        <small class="text-muted">
                            Super Admin
                        </small> 
                    </div>
                    <div class="avatar shadow bg-secondary">
                        <div class="avatar-content">SA</div>
                        <span class="avatar-status-online" title="En ligne"></span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">
                    <a class="dropdown-item text-danger" onclick="remplir('{{route('SuperAdmin.disconnect')}}','', 'vous déconnecter' ,'button_deconnection')">
                        <i class="mr-50 text-danger" data-feather="log-out"></i>Déconnexion
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
                    </a>
                    <button hidden class="" id="modal_danger" data-toggle="modal" data-target="#danger"></button>
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- END: Header-->

    @yield('menu')

<!-- BEGIN: Content-->
    @yield('contenu')
<!-- END: Content-->

<div class="text-center" hidden id="spinner">
    <div class="col-12 text-center">
        <div class="spinner-border text-dark" role="status">
            <span class="sr-only"></span>
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
<div class="sidenav-overlay"></div>
<div class="drag-target"></div>

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
<script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Page JS-->
<script src="{{ asset('app-assets/js/scripts/forms/form-select2.js') }}"></script>
<!-- END: Page JS-->

<!-- BEGIN: Page Vendor JS-->
<script src="{{ asset('app-assets/vendors/js/charts/apexcharts.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
<script src="{{ asset('app-assets/js/core/app.js') }}"></script>
<!-- END: Theme JS-->
  <script src="{{asset('app-assets/vendors/js/forms/spinner/jquery.bootstrap-touchspin.js')}}"></script>
  <script src="{{asset('app-assets/js/scripts/forms/form-number-input.js')}}"></script>
<script>
    $(window).on('load', function() {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    })
    @if(session()->has('message'))
    $(window).on('load', function() {
     success()
    })
    @endif
        function success(){
            console.log('borel')
            $('#modals-success').modal();
        }
        function remplir_montant(id){
            var montant2 = document.querySelector('#'+id+'2')
                montant = document.querySelector('#'+id).value.replaceAll(',','');
            montant2.value = montant.replaceAll(',','')
        }
        function format_montant(id) {
            number = document.querySelector('#'+id).value.replaceAll(' ','');
            numero = 0
            number= number.replaceAll(',', "")
            number=parseInt(number);
            if (isNaN(number)) {
                document.getElementById(id).value = '';
            }else{
            number =  number.toLocaleString('fr-FR', {
                    groupSize: 3,
                    useGrouping: true,
                });
            document.getElementById(id).value =  number.replaceAll(/\s/g, ",")
            }
            remplir_montant(id);
        }
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
        function response_ajax(id,data1,data2,autre,lien,method,put){
            var result = document.querySelector('#'+id),
                spinner = document.querySelector('#spinner').innerHTML;
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
                data : { page : data1 , recherche : data2 , datas : autre , '_token' : "{{ csrf_token() }}" },
                success: function(response)
                {
                    result.innerHTML = response;
                    if (feather) {
                        feather.replace({
                            width: 14,
                            height: 14
                        });
                    }
                    if (document.querySelector('#tospin')) {
                        document.querySelector('#tospin').innerHTML = '';
                    }
                },
                error: function(){
                    if(put == undefined){
                        result.innerHTML = "<h4 class='mt-1 text-danger text-center'> Erreur !</h4><div class='mb-1 w-100 text-center'>Un problème est survenu veuillez atualiser la page !!! </div>";
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

        function recap_ajax(id,data,lien,method,put){
            var result = document.querySelector('#'+id),
                spinner = document.querySelector('#spinner').innerHTML;
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
                    result.innerHTML = response;
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
        function valider(id){
            document.querySelector('#'+id).submit()
        }
        function recapituler(type,identifiant){
            var topass = false,
                inputs = document.querySelectorAll("input"),
                selects = document.querySelectorAll("select"),
                textareas = document.querySelectorAll("textarea"),
                timer = 2500;
                datas = {};
                inputs.forEach((input) => {
                    if (input.value.trim() == '' && topass == false && input.required) {
                        if(input.classList.contains("flatpickr-human-friendly")){
                            input.nextElementSibling.focus()
                            input.nextElementSibling.click();
                        }else{
                            input.focus();
                        }
                        document.querySelector('#'+input.name+'_error').hidden = false;
                        setTimeout(function(){
                            document.querySelector('#'+input.name+'_error').hidden = true;
                        },timer)
                        topass = true;
                    }
                    datas[input.name] = input.value;
                });
                selects.forEach((select) => {
                    if (select.value.trim() == '' && topass == false && select.required) {
                        select.focus();
                        document.querySelector('#'+select.name+'_error').hidden = false;
                        setTimeout(function(){
                            document.querySelector('#'+select.name+'_error').hidden = true;
                        },timer)
                        topass = true;

                    }
                    if (select.required || select.classList.contains("optionnel")){
                        options = document.querySelectorAll('.'+select.name)
                        options.forEach((option) => {
                            if (option.selected) {
                                datas[select.name] = option.innerHTML;
                            }
                        });
                    }
                });
                textareas.forEach((textarea) => {
                    if (textarea.value.trim() == '' && topass == false && textarea.required) {
                        textarea.focus();
                        document.querySelector('#'+textarea.name+'_error').hidden = false;
                        setTimeout(function(){
                            document.querySelector('#'+textarea.name+'_error').hidden = true;
                        },timer)
                        topass = true;
                    }
                        datas[textarea.name] = textarea.value;
                });
            if (topass == false) {
                if (type == 'modal') {
                    $('#'+identifiant).modal()
                }else if (type == 'button') {
                    document.querySelector('#'+identifiant).click()
                }
            }
            return topass;
        }
        function remplir(lien,libelle,action,id_remplir){
            var destroy_title = document.querySelector('#destroy_title'),
                destroy_form = document.querySelectorAll('.destroy_form'),
                noms = libelle == '' ? libelle : '" '+libelle+' "';
            for (var i = 0; i < destroy_form.length; i++) {
                destroy_form[i].action = lien;
            }
            destroy_title.innerHTML = 'Vous êtes sur de vouloir '+action+' ? <br> <br> '+noms+' ';
            document.querySelector('#modal_danger').click();
            document.querySelector('#confirm_footer').innerHTML = document.querySelector('#'+id_remplir).innerHTML
            
        }
</script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
    <script src="{{asset('app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
    <script src="{{asset('app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
    @yield('javascript')
</body>
<!-- END: Body-->

</html>
