@extends('superadmin/layout/template')
@section('menu')
    @include('superadmin/menu/menu')
@endsection
@section('title')
{{'Super Admin Dashboard'}}
@endsection

@section('css')

    <link rel="stylesheet" type="text/css" href="<?= asset('app-assets/css/core/menu/menu-types/vertical-menu.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset('app-assets/css/plugins/charts/chart-apex.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset('app-assets/css/core/menu/menu-types/vertical-menu.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset('app-assets/css/pages/dashboard-ecommerce.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset('app-assets/css/plugins/charts/chart-apex.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset('app-assets/css/plugins/extensions/ext-component-toastr.css') ?>">
@endsection
@section('contenu')
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">{{ __('Acceuil') }}</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active"> 
                                        {{ __('Acceuil') }}
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Dashboard Ecommerce Starts -->
                <section id="dashboard-ecommerce">
                    <div class="row match-height">
                    </div>
                    <div class="row match-height">
                        <!-- Company Table Card -->
                        <div class="col-lg-8 col-12">
                            <div class="card card-company-table">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Company') }}</th>
                                                    <th>{{ __('Category') }}</th>
                                                    <th>{{ __('Views') }}</th>
                                                    <th>{{ __('Revenue') }}</th>
                                                    <th>{{ __('Sales') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar rounded">
                                                                <div class="avatar-content">
                                                                    <img src="{{asset('app-assets/images/icons/toolbox.svg')}}" alt="{{ __('Toolbar svg') }}" />
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="font-weight-bolder">{{ __('Dixons') }}</div>
                                                                <div class="font-small-2 text-muted">meguc@ruj.io</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar bg-light-primary mr-1">
                                                                <div class="avatar-content">
                                                                    <i data-feather="monitor" class="font-medium-3"></i>
                                                                </div>
                                                            </div>
                                                            <span>{{ __('Technology') }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-nowrap">
                                                        <div class="d-flex flex-column">
                                                            <span class="font-weight-bolder mb-25">23.4k</span>
                                                            <span class="font-small-2 text-muted">{{ __('in 24 hours') }}</span>
                                                        </div>
                                                    </td>
                                                    <td>$891.2</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="font-weight-bolder mr-1">68%</span>
                                                            <i data-feather="trending-down" class="text-danger font-medium-1"></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar rounded">
                                                                <div class="avatar-content">
                                                                    <img src="{{asset('app-assets/images/icons/parachute.svg')}}" alt="{{ __('Parachute svg') }}" />
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="font-weight-bolder">{{ __('Motels') }}</div>
                                                                <div class="font-small-2 text-muted">vecav@hodzi.co.uk</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar bg-light-success mr-1">
                                                                <div class="avatar-content">
                                                                    <i data-feather="coffee" class="font-medium-3"></i>
                                                                </div>
                                                            </div>
                                                            <span>{{ __('Grocery') }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-nowrap">
                                                        <div class="d-flex flex-column">
                                                            <span class="font-weight-bolder mb-25">78k</span>
                                                            <span class="font-small-2 text-muted">{{ __('in 2 days') }}</span>
                                                        </div>
                                                    </td>
                                                    <td>$668.51</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="font-weight-bolder mr-1">97%</span>
                                                            <i data-feather="trending-up" class="text-success font-medium-1"></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar rounded">
                                                                <div class="avatar-content">
                                                                    <img src="{{asset('app-assets/images/icons/brush.svg')}}" alt="{{ __('Brush svg') }}" />
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="font-weight-bolder">{{ __('Zipcar') }}</div>
                                                                <div class="font-small-2 text-muted">davcilse@is.gov</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar bg-light-warning mr-1">
                                                                <div class="avatar-content">
                                                                    <i data-feather="watch" class="font-medium-3"></i>
                                                                </div>
                                                            </div>
                                                            <span>{{ __('Fashion') }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-nowrap">
                                                        <div class="d-flex flex-column">
                                                            <span class="font-weight-bolder mb-25">162</span>
                                                            <span class="font-small-2 text-muted">{{ __('in 5 days') }}</span>
                                                        </div>
                                                    </td>
                                                    <td>$522.29</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="font-weight-bolder mr-1">62%</span>
                                                            <i data-feather="trending-up" class="text-success font-medium-1"></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar rounded">
                                                                <div class="avatar-content">
                                                                    <img src="{{asset('app-assets/images/icons/star.svg')}}" alt="{{ __('Star svg') }}" />
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="font-weight-bolder">{{ __('Owning') }}</div>
                                                                <div class="font-small-2 text-muted">us@cuhil.gov</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar bg-light-primary mr-1">
                                                                <div class="avatar-content">
                                                                    <i data-feather="monitor" class="font-medium-3"></i>
                                                                </div>
                                                            </div>
                                                            <span>{{ __('Technology') }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-nowrap">
                                                        <div class="d-flex flex-column">
                                                            <span class="font-weight-bolder mb-25">214</span>
                                                            <span class="font-small-2 text-muted">{{ __('in 24 hours') }}</span>
                                                        </div>
                                                    </td>
                                                    <td>$291.01</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="font-weight-bolder mr-1">88%</span>
                                                            <i data-feather="trending-up" class="text-success font-medium-1"></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar rounded">
                                                                <div class="avatar-content">
                                                                    <img src="{{asset('app-assets/images/icons/book.svg')}}" alt="{{ __('Book svg') }}" />
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="font-weight-bolder">{{ __('Cafés') }}</div>
                                                                <div class="font-small-2 text-muted">pudais@jife.com</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar bg-light-success mr-1">
                                                                <div class="avatar-content">
                                                                    <i data-feather="coffee" class="font-medium-3"></i>
                                                                </div>
                                                            </div>
                                                            <span>{{ __('Grocery') }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-nowrap">
                                                        <div class="d-flex flex-column">
                                                            <span class="font-weight-bolder mb-25">208</span>
                                                            <span class="font-small-2 text-muted">{{ __('in 1 week') }}</span>
                                                        </div>
                                                    </td>
                                                    <td>$783.93</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="font-weight-bolder mr-1">16%</span>
                                                            <i data-feather="trending-down" class="text-danger font-medium-1"></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar rounded">
                                                                <div class="avatar-content">
                                                                    <img src="{{asset('app-assets/images/icons/rocket.svg')}}" alt="{{ __('Rocket svg') }}" />
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="font-weight-bolder">{{ __('Kmart') }}</div>
                                                                <div class="font-small-2 text-muted">bipri@cawiw.com</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar bg-light-warning mr-1">
                                                                <div class="avatar-content">
                                                                    <i data-feather="watch" class="font-medium-3"></i>
                                                                </div>
                                                            </div>
                                                            <span>{{ __('Fashion') }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-nowrap">
                                                        <div class="d-flex flex-column">
                                                            <span class="font-weight-bolder mb-25">990</span>
                                                            <span class="font-small-2 text-muted">{{ __('in 1 month') }}</span>
                                                        </div>
                                                    </td>
                                                    <td>$780.05</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="font-weight-bolder mr-1">78%</span>
                                                            <i data-feather="trending-up" class="text-success font-medium-1"></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar rounded">
                                                                <div class="avatar-content">
                                                                    <img src="{{asset('app-assets/images/icons/speaker.svg')}}" alt="{{ __('Speaker svg') }}" />
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="font-weight-bolder">{{ __('Payers') }}</div>
                                                                <div class="font-small-2 text-muted">luk@izug.io</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar bg-light-warning mr-1">
                                                                <div class="avatar-content">
                                                                    <i data-feather="watch" class="font-medium-3"></i>
                                                                </div>
                                                            </div>
                                                            <span>{{ __('Fashion') }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-nowrap">
                                                        <div class="d-flex flex-column">
                                                            <span class="font-weight-bolder mb-25">12.9k</span>
                                                            <span class="font-small-2 text-muted">{{ __('in 12 hours') }}</span>
                                                        </div>
                                                    </td>
                                                    <td>$531.49</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="font-weight-bolder mr-1">42%</span>
                                                            <i data-feather="trending-up" class="text-success font-medium-1"></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ Company Table Card -->

                        <!-- Developer Meetup Card -->
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card card-developer-meetup">
                                <div class="meetup-img-wrapper rounded-top text-center">
                                    <img src="{{asset('app-assets/images/illustration/email.svg')}}" alt="{{ __('Meeting Pic') }}" height="170" />
                                </div>
                                <div class="card-body">
                                    <div class="meetup-header d-flex align-items-center">
                                        <div class="meetup-day">
                                            <h6 class="mb-0">{{ __('THU') }}</h6>
                                            <h3 class="mb-0">24</h3>
                                        </div>
                                        <div class="my-auto">
                                            <h4 class="card-title mb-25">{{ __('Developer Meetup') }}</h4>
                                            <p class="card-text mb-0">{{ __('Meet world popular developers') }}</p>
                                        </div>
                                    </div>
                                    <div class="media">
                                        <div class="avatar bg-light-primary rounded mr-1">
                                            <div class="avatar-content">
                                                <i data-feather="calendar" class="avatar-icon font-medium-3"></i>
                                            </div>
                                        </div>
                                        <div class="media-body">
                                            <h6 class="mb-0">{{ __('Sat, May 25, 2020') }}</h6>
                                            <small>{{ __('10:AM to 6:PM') }}</small>
                                        </div>
                                    </div>
                                    <div class="media mt-2">
                                        <div class="avatar bg-light-primary rounded mr-1">
                                            <div class="avatar-content">
                                                <i data-feather="map-pin" class="avatar-icon font-medium-3"></i>
                                            </div>
                                        </div>
                                        <div class="media-body">
                                            <h6 class="mb-0">{{ __('Central Park') }}</h6>
                                            <small>{{ __('Manhattan, New york City') }}</small>
                                        </div>
                                    </div>
                                    <div class="avatar-group">
                                        <div data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="{{ __('Billy Hopkins') }}" class="avatar pull-up">
                                            <img src="{{asset('app-assets/images/portrait/small/avatar-s-9.jpg')}}" alt="{{ __('Avatar') }}" width="33" height="33" />
                                        </div>
                                        <div data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="{{ __('Amy Carson') }}" class="avatar pull-up">
                                            <img src="{{asset('app-assets/images/portrait/small/avatar-s-6.jpg')}}" alt="{{ __('Avatar') }}" width="33" height="33" />
                                        </div>
                                        <div data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="{{ __('Brandon Miles') }}" class="avatar pull-up">
                                            <img src="{{asset('app-assets/images/portrait/small/avatar-s-8.jpg')}}" alt="{{ __('Avatar') }}" width="33" height="33" />
                                        </div>
                                        <div data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="{{ __('Daisy Weber') }}" class="avatar pull-up">
                                            <img src="{{asset('app-assets/images/portrait/small/avatar-s-20.jpg')}}" alt="{{ __('Avatar') }}" width="33" height="33" />
                                        </div>
                                        <div data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="{{ __('Jenny Looper') }}" class="avatar pull-up">
                                            <img src="{{asset('app-assets/images/portrait/small/avatar-s-20.jpg')}}" alt="{{ __('Avatar') }}" width="33" height="33" />
                                        </div>
                                        <h6 class="align-self-center cursor-pointer ml-50 mb-0">+42</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ Developer Meetup Card -->

                        <!-- Browser States Card -->
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card card-browser-states">
                                <div class="card-header">
                                    <div>
                                        <h4 class="card-title">{{ __('Browser States') }}</h4>
                                        <p class="card-text font-small-2">{{ __('Counter August 2020') }}</p>
                                    </div>
                                    <div class="dropdown chart-dropdown">
                                        <i data-feather="more-vertical" class="font-medium-3 cursor-pointer" data-toggle="dropdown"></i>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="javascript:void(0);">{{ __('Last 28 Days') }}</a>
                                            <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Month') }}</a>
                                            <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Year') }}</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="browser-states">
                                        <div class="media">
                                            <img src="{{asset('app-assets/images/icons/google-chrome.png')}}" class="rounded mr-1" height="30" alt="{{ __('Google Chrome') }}" />
                                            <h6 class="align-self-center mb-0">{{ __('Google Chrome') }}</h6>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="font-weight-bold text-body-heading mr-1">54.4%</div>
                                            <div id="browser-state-chart-primary"></div>
                                        </div>
                                    </div>
                                    <div class="browser-states">
                                        <div class="media">
                                            <img src="{{asset('app-assets/images/icons/mozila-firefox.png')}}" class="rounded mr-1" height="30" alt="{{ __('Mozila Firefox') }}" />
                                            <h6 class="align-self-center mb-0">{{ __('Mozila Firefox') }}</h6>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="font-weight-bold text-body-heading mr-1">6.1%</div>
                                            <div id="browser-state-chart-warning"></div>
                                        </div>
                                    </div>
                                    <div class="browser-states">
                                        <div class="media">
                                            <img src="{{asset('app-assets/images/icons/apple-safari.png')}}" class="rounded mr-1" height="30" alt="{{ __('Apple Safari') }}" />
                                            <h6 class="align-self-center mb-0">{{ __('Apple Safari') }}</h6>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="font-weight-bold text-body-heading mr-1">14.6%</div>
                                            <div id="browser-state-chart-secondary"></div>
                                        </div>
                                    </div>
                                    <div class="browser-states">
                                        <div class="media">
                                            <img src="{{asset('app-assets/images/icons/internet-explorer.png')}}" class="rounded mr-1" height="30" alt="{{ __('Internet Explorer') }}" />
                                            <h6 class="align-self-center mb-0">{{ __('Internet Explorer') }}</h6>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="font-weight-bold text-body-heading mr-1">4.2%</div>
                                            <div id="browser-state-chart-info"></div>
                                        </div>
                                    </div>
                                    <div class="browser-states">
                                        <div class="media">
                                            <img src="{{asset('app-assets/images/icons/opera.png')}}" class="rounded mr-1" height="30" alt="{{ __('Opera Mini') }}" />
                                            <h6 class="align-self-center mb-0">{{ __('Opera Mini') }}</h6>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="font-weight-bold text-body-heading mr-1">8.4%</div>
                                            <div id="browser-state-chart-danger"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ Browser States Card -->

                        <!-- Goal Overview Card -->
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">{{ __('Goal Overview') }}</h4>
                                    <i data-feather="help-circle" class="font-medium-3 text-muted cursor-pointer"></i>
                                </div>
                                <div class="card-body p-0">
                                    <div id="goal-overview-radial-bar-chart" class="my-2"></div>
                                    <div class="row border-top text-center mx-0">
                                        <div class="col-6 border-right py-1">
                                            <p class="card-text text-muted mb-0">{{ __('Completed') }}</p>
                                            <h3 class="font-weight-bolder mb-0">786,617</h3>
                                        </div>
                                        <div class="col-6 py-1">
                                            <p class="card-text text-muted mb-0">{{ __('In Progress') }}</p>
                                            <h3 class="font-weight-bolder mb-0">13,561</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ Goal Overview Card -->

                        <!-- Transaction Card -->
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="card card-transaction">
                                <div class="card-header">
                                    <h4 class="card-title">{{ __('Transactions') }}</h4>
                                    <div class="dropdown chart-dropdown">
                                        <i data-feather="more-vertical" class="font-medium-3 cursor-pointer" data-toggle="dropdown"></i>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="javascript:void(0);">{{ __('Last 28 Days') }}</a>
                                            <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Month') }}</a>
                                            <a class="dropdown-item" href="javascript:void(0);">{{ __('Last Year') }}</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="transaction-item">
                                        <div class="media">
                                            <div class="avatar bg-light-primary rounded">
                                                <div class="avatar-content">
                                                    <i data-feather="pocket" class="avatar-icon font-medium-3"></i>
                                                </div>
                                            </div>
                                            <div class="media-body">
                                                <h6 class="transaction-title">{{ __('Wallet') }}</h6>
                                                <small>{{ __('Starbucks') }}</small>
                                            </div>
                                        </div>
                                        <div class="font-weight-bolder text-danger">- $74</div>
                                    </div>
                                    <div class="transaction-item">
                                        <div class="media">
                                            <div class="avatar bg-light-success rounded">
                                                <div class="avatar-content">
                                                    <i data-feather="check" class="avatar-icon font-medium-3"></i>
                                                </div>
                                            </div>
                                            <div class="media-body">
                                                <h6 class="transaction-title">{{ __('Bank Transfer') }}</h6>
                                                <small>{{ __('Add Money') }}</small>
                                            </div>
                                        </div>
                                        <div class="font-weight-bolder text-success">+ $480</div>
                                    </div>
                                    <div class="transaction-item">
                                        <div class="media">
                                            <div class="avatar bg-light-danger rounded">
                                                <div class="avatar-content">
                                                    <i data-feather="dollar-sign" class="avatar-icon font-medium-3"></i>
                                                </div>
                                            </div>
                                            <div class="media-body">
                                                <h6 class="transaction-title">{{ __('Paypal') }}</h6>
                                                <small>{{ __('Add Money') }}</small>
                                            </div>
                                        </div>
                                        <div class="font-weight-bolder text-success">+ $590</div>
                                    </div>
                                    <div class="transaction-item">
                                        <div class="media">
                                            <div class="avatar bg-light-warning rounded">
                                                <div class="avatar-content">
                                                    <i data-feather="credit-card" class="avatar-icon font-medium-3"></i>
                                                </div>
                                            </div>
                                            <div class="media-body">
                                                <h6 class="transaction-title">{{ __('Mastercard') }}</h6>
                                                <small>{{ __('Ordered Food') }}</small>
                                            </div>
                                        </div>
                                        <div class="font-weight-bolder text-danger">- $23</div>
                                    </div>
                                    <div class="transaction-item">
                                        <div class="media">
                                            <div class="avatar bg-light-info rounded">
                                                <div class="avatar-content">
                                                    <i data-feather="trending-up" class="avatar-icon font-medium-3"></i>
                                                </div>
                                            </div>
                                            <div class="media-body">
                                                <h6 class="transaction-title">{{ __('Transfer') }}</h6>
                                                <small>{{ __('Refund') }}</small>
                                            </div>
                                        </div>
                                        <div class="font-weight-bolder text-success">+ $98</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ Transaction Card -->
                    </div> 
                </section>
                <!-- Dashboard Ecommerce ends -->

            </div>
        </div>
    </div>
    <!-- END: Content-->
@endsection

@section('javascript')

    <!-- BEGIN: Page JS-->
    <script type="text/javascript">
        document.getElementById('home').className += 'active'
    </script>
    <!-- END: Page JS-->
@endsection
