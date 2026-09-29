<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href="{{route('SuperAdmin.home')}}">
                <span class="brand-logo">
                        <img width="50px" height="50px" src="{{Sa_logo()}}">
                </span>
                    <h1 class="brand-text">Speedex</h1>
                </a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

            <li class=" navigation-header">
                Dashboard
            </li>
            {{-- #d87e46 --}}
            <li id="home"><a class="d-flex align-items-center" href="{{route('SuperAdmin.home') }}"><i data-feather='home'></i><span class="menu-item" data-i18n="eCommerce">Acceuil</span></a>
            </li>
            <li class=" navigation-header">
                Dashboard
            </li>
            {{-- #d87e46 --}}
            <li><a class="d-flex align-items-center my-1"><i data-feather='user'></i><span class="menu-item" data-i18n="eCommerce">Client</span></a>
                <ul>
                    <li id="client-index"><a class="d-flex align-items-center" href="{{route('Sa-client.index') }}"><i data-feather='circle'></i><span class="menu-item" data-i18n="eCommerce">Liste</span></a>
                    </li>
                    <li id="client-create"><a class="d-flex align-items-center" href="{{route('Sa-client.create') }}"><i data-feather='circle'></i><span class="menu-item" data-i18n="eCommerce">Ajouter</span></a>
                    </li>
                </ul>
            </li>
            <li><a class="d-flex align-items-center my-1"><i data-feather='shopping-cart'></i><span class="menu-item" data-i18n="eCommerce">Abonnement</span></a>
                <ul>
                    <li id="abonnement-index"><a class="d-flex align-items-center" href="{{route('Sa-abonnement.index') }}"><i data-feather='circle'></i><span class="menu-item" data-i18n="eCommerce">Liste</span></a>
                    </li>
                    <li id="abonnement-create"><a class="d-flex align-items-center" href="{{route('Sa-abonnement.create') }}"><i data-feather='circle'></i><span class="menu-item" data-i18n="eCommerce">Ajouter</span></a>
                    </li>
                </ul>
            </li>
            <li id="parametre-index"><a class="d-flex align-items-center my-1" href="{{route('Sa-parametre.index') }}"><i data-feather='settings'></i><span class="menu-item" data-i18n="eCommerce">Paramètres</span></a>
            <li class=" navigation-header">
            </li>

           
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
