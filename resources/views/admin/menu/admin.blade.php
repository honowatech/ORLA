
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
                <li class=" navigation-header">Administration<i data-feather="more-horizontal"></i>
                <li class=" nav-item"><a class="d-flex align-items-center"><i data-feather="user"></i><span class="menu-title text-truncate" data-i18n="user">Utilisateurs</span></a>
                    <ul class="menu-content">
                        <li id="UserListe"><a class="d-flex align-items-center" href="{{route('users.index')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="eCommerce">Liste</span></a>
                        </li>
                        <li id="UserAjouter"><a class="d-flex align-items-center" href="{{route('users.create')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="Analytics">Ajouter</span></a>
                        </li>
                    </ul>
                </li>
                <li class=" nav-item"><a class="d-flex align-items-center"><i data-feather='unlock'></i><span class="menu-title text-truncate" data-i18n="user">Agents</span></a>
                    <ul class="menu-content">
                        <li id="AgentsListe"><a class="d-flex align-items-center" href="{{route('agents.index')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="eCommerce">Liste</span></a>
                        </li>
                        <li id="AgentsAjouter"><a class="d-flex align-items-center" href="{{route('agents.create')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="Analytics">Ajouter</span></a>
                        </li>
                    </ul>
                </li>
                <li class=" navigation-header">Personnel<i data-feather="more-horizontal"></i>
                </li>
                <li class=" nav-item"><a class="d-flex align-items-center"><i data-feather='zap'></i><span class="menu-title text-truncate" data-i18n="user">Coursiers</span></a>
                    <ul class="menu-content">
                        <li id="CoursierListe"><a class="d-flex align-items-center" href="{{route('coursiers.index')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="eCommerce">Liste</span></a>
                        </li>
                        <li id="CoursierAjouter"><a class="d-flex align-items-center" href="{{route('coursiers.create')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="Analytics">Ajouter</span></a>
                        </li>
                    </ul>
                </li>
                <li class=" navigation-header">Matériel<i data-feather="more-horizontal"></i>
                </li>
                <li class=" nav-item"><a class="d-flex align-items-center"><i data-feather='truck'></i><span class="menu-title text-truncate" data-i18n="user">Vecteurs</span></a>
                    <ul class="menu-content">
                        <li class=" nav-item"><a class="d-flex align-items-center"><i data-feather='share-2'></i><span class="menu-title text-truncate" data-i18n="user">Type vecteurs</span></a>
                            <ul class="menu-content">
                                <li id="Type_vehiculeListe"><a class="d-flex align-items-center" href="{{route('type_vehicule.index')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="eCommerce">Liste</span></a>
                                </li>
                                <li id="Type_vehiculeAjouter"><a class="d-flex align-items-center" href="{{route('type_vehicule.create')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="Analytics">Ajouter</span></a>
                                </li>
                            </ul>
                        </li>
                        <li class=" nav-item"><a class="d-flex align-items-center"><i data-feather='sliders'></i><span class="menu-title text-truncate" data-i18n="user">Véhicule</span></a>
                            <ul class="menu-content">
                                <li id="VehiculeListe"><a class="d-flex align-items-center" href="{{route('vehicule.index')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="eCommerce">Liste</span></a>
                                </li>
                                <li id="VehiculeAjouter"><a class="d-flex align-items-center" href="{{route('vehicule.create')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="Analytics">Ajouter</span></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class=" navigation-header">Clientelle<i data-feather="more-horizontal"></i>
                <li class=" nav-item"><a class="d-flex align-items-center"><i data-feather='users'></i><span class="menu-title text-truncate" data-i18n="user">Clients</span></a>
                    <ul class="menu-content">
                        <li id="ClientsListe"><a class="d-flex align-items-center" href="{{route('clients.index')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="eCommerce">Liste</span></a>
                        </li>
                        <li id="ClientsAjouter"><a class="d-flex align-items-center" href="{{route('clients.create')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="Analytics">Ajouter</span></a>
                        </li>
                    </ul>
                </li>
                <li class=" navigation-header">Utilitaires<i data-feather="more-horizontal"></i>
                </li>
                <li class=" nav-item"><a class="d-flex align-items-center"><i data-feather='shopping-cart'></i><span class="menu-title text-truncate" data-i18n="user">Commandes</span></a>
                    <ul class="menu-content">
                        <li id="CommandesListe"><a class="d-flex align-items-center" href="{{route('commandes.index')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="eCommerce">Liste</span></a>
                        </li>
                        <li id="CommandesAjouter"><a class="d-flex align-items-center" href="{{route('commandes.create')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="Analytics">Ajouter</span></a>
                        </li>
                    </ul>
                </li>
                <li class=" nav-item"><a class="d-flex align-items-center"><i data-feather='shopping-bag'></i><span class="menu-title text-truncate" data-i18n="user">Produits</span></a>
                    <ul class="menu-content">
                        <li id="ProduitsListe"><a class="d-flex align-items-center" href="{{route('produits.index')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="eCommerce">Liste</span></a>
                        </li>
                        <li id="ProduitsAjouter"><a class="d-flex align-items-center" href="{{route('produits.create')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="Analytics">Ajouter</span></a>
                        </li>
                    </ul>
                </li>
                <li class="navigation-header">Rapport<i data-feather="more-horizontal"></i>
                <li class="nav-item" id="Details_commande"><a class="d-flex align-items-center" href="{{route('details_commande.index')}}">
                    <i data-feather="clipboard"></i><span class="menu-title text-truncate" data-i18n="user">Livraison</span></a>
                </li>
                <li class=" navigation-header">Localisation<i data-feather="more-horizontal"></i>
                </li>
                <li class=" nav-item"><a class="d-flex align-items-center"><i data-feather='filter'></i><span class="menu-title text-truncate" data-i18n="user">Quartiers</span></a>
                    <ul class="menu-content">
                        <li id="quartierListe"><a class="d-flex align-items-center" href="{{route('quartier.index')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="eCommerce">Liste</span></a>
                        </li>
                        <li z><a class="d-flex align-items-center" href="{{route('quartier.create')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="Analytics">Ajouter</span></a>
                        </li>
                    </ul>
                </li>
                <li class=" nav-item"><a class="d-flex align-items-center"><i data-feather='map-pin'></i><span class="menu-title text-truncate" data-i18n="user">Villes</span></a>
                    <ul class="menu-content">
                        <li id="villeListe"><a class="d-flex align-items-center" href="{{route('ville.index')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="eCommerce">Liste</span></a>
                        </li>
                        <li id="villeAjouter"><a class="d-flex align-items-center" href="{{route('ville.create')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="Analytics">Ajouter</span></a>
                        </li>
                    </ul>
                </li>
                @if(auth()->user()->type_utilisateur->libelle == 'Super Admin')
                <li class=" nav-item"><a class="d-flex align-items-center"><i data-feather='pie-chart'></i><span class="menu-title text-truncate" data-i18n="user">Zones</span></a>
                    <ul class="menu-content">
                        <li id="zoneListe"><a class="d-flex align-items-center" href="{{route('zone.index')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="eCommerce">Liste</span></a>
                        </li>
                        <li id="zoneAjouter"><a class="d-flex align-items-center" href="{{route('zone.create')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="Analytics">Ajouter</span></a>
                        </li>
                    </ul>
                </li>
                <li id="montant_livraisonAjouter" class=" nav-item"><a class="d-flex align-items-center" href="{{route('montant_livraison.index')}}"><i data-feather='shuffle'></i><span class="menu-title text-truncate" data-i18n="user">montant_livraison</span></a>
                </li>
                @endif
                <li class=" navigation-header"><i data-feather="more-horizontal"></i>
            </ul>
        </div>
    </div>
    <!-- END: Main Menu-->