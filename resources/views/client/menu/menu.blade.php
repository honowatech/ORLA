
    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto"><a class="navbar-brand" href="{{route('home')}}"><span class="brand-logo">
                                    <i data-feather='truck' class="text-dark" style="height :30px;width: 30px;"></i></span>
                        <h2 class="brand-text text-dark">Speedex</h2>
                    </a></li>
                <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0 d-none" id="boutton_menu" data-toggle="collapse"><i class="d-block d-xl-none text-dark toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i></a></li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main mt-1" id="main-menu-navigation" data-menu="menu-navigation">
                <li id="Acceuil"><a class="d-flex align-items-center" href="{{route('home')}}"><i data-feather="home"></i><span class="menu-item" data-i18n="eCommerce">Acceuil</span></a>
                </li>
                <li id="Commandes"><a class="d-flex align-items-center my-1" href="{{route('Clientcommandes.index')}}"><i data-feather="shopping-bag"></i><span class="menu-item" data-i18n="eCommerce">Commandes</span></a>
                </li>
                <li class="mt-1 nav-item"><a class="d-flex align-items-center"><i data-feather='user'></i><span class="menu-title text-truncate" data-i18n="user">Compte</span></a>
                    <ul class="menu-content">
                        <li id="Profil"><a class="d-flex align-items-center" href="{{route('Clientusers.show',Auth::user()->id)}}"><i data-feather="calendar"></i><span class="menu-item">Profil</span></a>
                        </li>
                        <li id="Editer"><a class="d-flex align-items-center" href="{{route('Clientusers.edit',Auth::user()->id)}}"><i data-feather="edit-2"></i><span class="menu-item">Editer</span></a>
                        </li>
                        <li id="password"><a class="d-flex align-items-center" href="{{route('Clientpassword.index')}}"><i data-feather='lock'></i><span class="menu-item">Mot de passe</span></a>
                        </li>
                    </ul>
                <li class=" navigation-header"><i data-feather="more-horizontal"></i></li>
            </ul>
        </div>
    </div>
    <!-- END: Main Menu-->