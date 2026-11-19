<!DOCTYPE html>
<html class="loading" lang="fr" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Créer un compte Speedex</title>
    <link rel="apple-touch-icon" href="{{ asset('app-assets/images/avatars/map-pin.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('app-assets/images/avatars/map-pin.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/forms/form-validation.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/page-auth.css') }}">
</head>

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static" data-open="click" data-menu="vertical-menu-modern" data-col="blank-page" style="background-color: #c7e7fc;">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row"></div>
            <div class="content-body">
                <div class="auth-wrapper auth-v1 px-2">
                    <div class="auth-inner py-2">
                        <div class="card mb-0">
                            <div class="card-body">
                                <a href="{{ route('login') }}" class="brand-logo">
                                    <i data-feather="truck" class="text-info" style="height: 60px; width: 60px;"></i>
                                </a>

                                <h4 class="text-center card-title mb-1">Créer votre compte</h4>
                                <p class="text-center card-text mb-2">Faites livrer vos colis et suivez vos commandes.</p>

                                <form class="auth-register-form mt-2" method="POST" action="{{ route('register') }}">
                                    @csrf

                                    <div class="form-group">
                                        <label for="register-noms" class="form-label">Nom</label>
                                        <input id="register-noms" type="text" class="form-control @error('noms') is-invalid @enderror" name="noms" value="{{ old('noms') }}" required maxlength="255" autocomplete="family-name" autofocus>
                                        @error('noms')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="register-prenoms" class="form-label">Prénom</label>
                                        <input id="register-prenoms" type="text" class="form-control @error('prenoms') is-invalid @enderror" name="prenoms" value="{{ old('prenoms') }}" maxlength="255" autocomplete="given-name">
                                        @error('prenoms')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="register-telephone" class="form-label">Téléphone</label>
                                        <input id="register-telephone" type="tel" class="form-control @error('telephone') is-invalid @enderror" name="telephone" value="{{ old('telephone') }}" required inputmode="numeric" pattern="[62][0-9]{8}" placeholder="6XXXXXXXX" autocomplete="tel-national">
                                        @error('telephone')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="register-email" class="form-label">E-mail</label>
                                        <input id="register-email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="register-password" class="form-label">Mot de passe</label>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input id="register-password" type="password" class="form-control form-control-merge @error('password') is-invalid @enderror" name="password" required minlength="8" autocomplete="new-password">
                                            <div class="input-group-append">
                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                            </div>
                                            @error('password')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                        <small class="text-muted">8 caractères au moins.</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="register-password-confirm" class="form-label">Confirmer le mot de passe</label>
                                        <input id="register-password-confirm" type="password" class="form-control" name="password_confirmation" required minlength="8" autocomplete="new-password">
                                    </div>

                                    <button type="submit" class="btn btn-info btn-block">Créer mon compte</button>
                                </form>

                                <p class="text-center mt-2">
                                    <span>Déjà un compte ?</span>
                                    <a href="{{ route('login') }}"><span>Se connecter</span></a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('app-assets/js/core/app.js') }}"></script>
    <script>
        $(window).on('load', function () {
            if (feather) {
                feather.replace({ width: 14, height: 14 });
            }
        });
    </script>
</body>
</html>
