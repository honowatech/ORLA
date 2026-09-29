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
    <title>Se Connecter</title>
    <link rel="shortcut icon" href="{{Sa_logo()}}">
    <link rel="apple-touch-icon" href="{{Sa_logo()}}">
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet ?>">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="<?= asset('app-assets/vendors/css/vendors.min.css') ?>">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="<?= asset('app-assets/css/bootstrap.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset('app-assets/css/bootstrap-extended.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset('app-assets/css/colors.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset('app-assets/css/components.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset('app-assets/css/themes/dark-layout.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset('app-assets/css/themes/bordered-layout.css') ?>">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="<?= asset('app-assets/css/core/menu/menu-types/vertical-menu.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset('app-assets/css/plugins/forms/form-validation.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset('app-assets/css/pages/page-auth.css') ?>">

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="blank-page" style="background-color: #d7d7d7;">
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="auth-wrapper auth-v1 px-2">
                    <div class="auth-inner py-2">
                        <!-- Login v1 -->
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="col-12 mt-1 mb-2 text-center">
                                    <a class="navbar-brand" href="{{route('SuperAdmin.home')}}">
                                        <img class="d-none d-lg-block" src="{{Sa_logo()}}" alt="avatar" height="100" width="150">
                                        <img  class="d-block d-lg-none" src="{{Sa_logo()}}" alt="avatar" height="70" width="100">
                                    </a>
                                </div>
                                <h4 class="text-center card-title mb-1">Votre Espace <b>Super Admin</b></h4>
                                <p class="text-center card-text mb-2">Connectez vous pour accèder à l'application</p>

                                <form class="auth-login-form" method="POST" action="{{ route('SuperAdmin.connect') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="login-email" class="form-label">E-mail</label>
                                        <input id="login-email" aria-describedby="login-email" type="email" class="form-control @if(session()->has('email')) is-invalid @endif" name="email" placeholder="azerty@email.com" value="{{ isset(session()->get('email')['value']) ? session()->get('email')['value'] : '' }}" required autofocus>
                                @if(isset(session()->get('email')['error']))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ session()->get('email')['error'] }}</strong>
                                    </span>
                                @enderror
                                    </div>
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-end">
                                            <label for="login-password">Mot de Passe</label>
                                            {{-- @if (Route::has('password.request'))
                                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                                    <small>{{ __('Mot de passe Oublié?') }}</small>
                                                </a>
                                            @endif --}}
                                        </div>
                                        <div class="input-group input-group-merge @if(session()->has('email')) is-invalid @endif form-password-toggle">

                                            <input id="login-password" type="password" tabindex="2" class="form-control-merge form-control @if(session()->has('email')) is-invalid @endif" name="password" required placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="login-password">
                                            <div class="input-group-append">
                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn col-8 btn-primary btn-gradient-primary mt-5 mx-auto btn-block" tabindex="4">{{ __('Se Connecter') }}</button>
                                </form>

                                <div class="divider">
                                </div>
                            </div>
                        </div>
                        <!-- /Login v1 -->
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- END: Content-->


    <!-- BEGIN: Vendor JS-->
    <script src="<?= asset('app-assets/vendors/js/vendors.min.js') ?>"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="<?= asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') ?>"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="<?= asset('app-assets/js/core/app-menu.js') ?>"></script>
    <script src="<?= asset('app-assets/js/core/app.js') ?>"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="<?= asset('app-assets/js/scripts/pages/page-auth-login.js') ?>"></script>
    <!-- END: Page JS-->

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })
    </script>
</body>
<!-- END: Body-->

</html>
