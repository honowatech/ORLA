<!DOCTYPE html>
<html class="loading" lang="fr" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>Créer l'espace de mon entreprise - {{ config('app.name') }}</title>
    <link rel="apple-touch-icon" href="{{asset('app-assets/images/avatars/map-pin.png')}}>">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('app-assets/images/avatars/map-pin.png')}}">
    
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

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="blank-page" style="background-color: #c7e7fc;">
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
                        <div class="card mb-0">
                            <div class="card-body">
                                <a href="{{ route('login') }}" class="brand-logo">
                                    <i data-feather='truck' class="text-info" style="height :60px;width: 60px;"></i>
                                </a>

                                <h4 class="text-center card-title mb-1">Créez l'espace de votre entreprise</h4>
                                <p class="text-center card-text mb-2">Livreurs, véhicules, zones et commandes : tout au même endroit. {{ $essai_jours }} jours d'essai gratuit, toutes fonctionnalités incluses.</p>

                                <form class="mt-2" method="POST" action="{{ route('inscription.store') }}">
                                    @csrf
                                    <h6 class="text-muted text-uppercase small">Votre entreprise</h6>
                                    <div class="form-group">
                                        <label for="entreprise" class="form-label">Nom de l'entreprise</label>
                                        <input id="entreprise" type="text" class="form-control @error('entreprise') is-invalid @enderror" name="entreprise" value="{{ old('entreprise') }}" placeholder="Ex. : Express Livraison" required autofocus autocomplete="organization">
                                        @error('entreprise')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="telephone" class="form-label">Téléphone</label>
                                        <input id="telephone" type="tel" class="form-control @error('telephone') is-invalid @enderror" name="telephone" value="{{ old('telephone') }}" placeholder="6XXXXXXXX" required >
                                        @error('telephone')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="ville" class="form-label">Ville principale</label>
                                        <input id="ville" type="text" class="form-control @error('ville') is-invalid @enderror" name="ville" value="{{ old('ville') }}" placeholder="Ex. : Douala" required >
                                        @error('ville')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                    <h6 class="text-muted text-uppercase small mt-2">Votre compte administrateur</h6>
                                    <div class="form-group">
                                        <label for="noms" class="form-label">Vos noms</label>
                                        <input id="noms" type="text" class="form-control @error('noms') is-invalid @enderror" name="noms" value="{{ old('noms') }}" placeholder="" required autocomplete="name">
                                        @error('noms')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="email" class="form-label">E-mail</label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="vous@entreprise.cm" required autocomplete="email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="password" class="form-label">Mot de passe (8 caractères minimum)</label>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="password_confirmation" class="form-label">Confirmez le mot de passe</label>
                                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input @error('conditions') is-invalid @enderror" type="checkbox" name="conditions" id="conditions" value="1" {{ old('conditions') ? 'checked' : '' }} required>
                                            <label class="custom-control-label" for="conditions">J'accepte les conditions d'utilisation</label>
                                            @error('conditions')
                                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <button class="btn btn-info btn-block">Créer mon espace</button>
                                </form>

                                <p class="text-center mt-2">
                                    <span>Vous avez déjà un compte ?</span>
                                    <a href="{{ route('login') }}"><span>Se connecter</span></a>
                                </p>
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
