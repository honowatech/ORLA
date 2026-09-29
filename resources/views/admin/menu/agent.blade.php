
    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto"><a class="navbar-brand" href="{{route('home')}}"><span class="brand-logo">
                                    <i data-feather='truck' class="text-info" style="height :30px;width: 30px;"></i></span>
                        <h2 class="brand-text text-info">Speedex</h2>
                    </a></li>
                <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i></a></li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                    <li class=" nav-item"><a class="d-flex align-items-center"><i data-feather="home"></i><span class="menu-title text-truncate" data-i18n="user">Tableau de bord</span></a>
                        <ul class="menu-content">
                            <li id="Acceuil"><a class="d-flex align-items-center" href="{{route('home')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="eCommerce">Statistiques</span></a>
                            </li>
                            {{-- <li id="Notifications"><a class="d-flex align-items-center" href="{{route('notification.index')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="eCommerce">Notifications</span></a>
                            </li> --}}
                        </ul>
                    </li>
                </li>
                <li class=" navigation-header">Utilitaires<i data-feather="more-horizontal"></i>
                </li>
                <li id="CommandesListe" class="nav-item"><a href="{{route('commandes.index')}}" class="d-flex align-items-center"><i data-feather='shopping-cart'></i><span class="menu-title text-truncate" data-i18n="user">Commandes</span></a>
                </li>
                <li class=" navigation-header"><i data-feather="more-horizontal"></i>
            </ul>
        </div>
    </div>
    <!-- END: Main Menu-->