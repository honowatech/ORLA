@extends('client/templates/template')

@section('title')
{{' Notifications '}}
@endsection
@section('contenu')
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
            <div class="content-header row">
    	 <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">{{ __('Notifications') }}</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">{{ __('Acceuil') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('Notifications') }}
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
            </div>
    <div class="card pl-1 pr-1">
            <div class="content-body">
                <ul class=" card dropdown-menu-media dropdown-menu-right show">
                        <li class="dropdown-menu-header">
                            <div class="dropdown-header d-flex">
                                <h4 class="notification-title mb-0 mr-auto">{{ __('Notifications') }}</h4>
                                <div class="badge badge-pill badge-light-primary">{{ __('6 New') }}</div>
                            </div>
                        </li>
                        <li class="scrollable-container media-list ps ps--active-y"><a class="d-flex" href="javascript:void(0)">
                                <div class="media d-flex align-items-start">
                                    <div class="media-left">
                                        <div class="avatar"><img src="../../../app-assets/images/portrait/small/avatar-s-15.jpg" alt="{{ __('avatar') }}" width="32" height="32"></div>
                                    </div>
                                    <div class="media-body">
                                        <p class="media-heading"><span class="font-weight-bolder">{{ __('Congratulation Sam 🎉') }}</span>{{ __('winner!') }}</p><small class="notification-text"> {{ __('Won the monthly best seller badge.') }}</small>
                                    </div>
                                </div>
                            </a><a class="d-flex" href="javascript:void(0)">
                                <div class="media d-flex align-items-start">
                                    <div class="media-left">
                                        <div class="avatar"><img src="../../../app-assets/images/portrait/small/avatar-s-3.jpg" alt="{{ __('avatar') }}" width="32" height="32"></div>
                                    </div>
                                    <div class="media-body">
                                        <p class="media-heading"><span class="font-weight-bolder">{{ __('New message') }}</span>{{ __('received') }}</p><small class="notification-text"> {{ __('You have 10 unread messages') }}</small>
                                    </div>
                                </div>
                            </a><a class="d-flex" href="javascript:void(0)">
                                <div class="media d-flex align-items-start">
                                    <div class="media-left">
                                        <div class="avatar bg-light-danger">
                                            <div class="avatar-content">{{ __('MD') }}</div>
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <p class="media-heading"><span class="font-weight-bolder">{{ __('Revised Order 👋') }}</span>{{ __('checkout') }}</p><small class="notification-text"> {{ __('MD Inc. order updated') }}</small>
                                    </div>
                                </div>
                            </a>
                            <div class="media d-flex align-items-center">
                                <h6 class="font-weight-bolder mr-auto mb-0">{{ __('System Notifications') }}</h6>
                                <div class="custom-control custom-control-primary custom-switch">
                                    <input class="custom-control-input" id="systemNotification" type="checkbox" checked="">
                                    <label class="custom-control-label" for="systemNotification"></label>
                                </div>
                            </div><a class="d-flex" href="javascript:void(0)">
                                <div class="media d-flex align-items-start">
                                    <div class="media-left">
                                        <div class="avatar bg-light-danger">
                                            <div class="avatar-content"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x avatar-icon"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></div>
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <p class="media-heading"><span class="font-weight-bolder">{{ __('Server down') }}</span>{{ __('registered') }}</p><small class="notification-text"> {{ __('USA Server is down due to hight CPU usage') }}</small>
                                    </div>
                                </div>
                            </a><a class="d-flex" href="javascript:void(0)">
                                <div class="media d-flex align-items-start">
                                    <div class="media-left">
                                        <div class="avatar bg-light-success">
                                            <div class="avatar-content"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check avatar-icon"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <p class="media-heading"><span class="font-weight-bolder">{{ __('Sales report') }}</span>{{ __('generated') }}</p><small class="notification-text"> {{ __('Last month sales report generated') }}</small>
                                    </div>
                                </div>
                            </a><a class="d-flex" href="javascript:void(0)">
                                <div class="media d-flex align-items-start">
                                    <div class="media-left">
                                        <div class="avatar bg-light-warning">
                                            <div class="avatar-content"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-triangle avatar-icon"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div>
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <p class="media-heading"><span class="font-weight-bolder">{{ __('High memory') }}</span>{{ __('usage') }}</p><small class="notification-text"> {{ __('BLR Server using high memory') }}</small>
                                    </div>
                                </div>
                            </a>
                        <div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; right: 0px; height: 350px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 283px;"></div></div></li>
                        <li class="dropdown-menu-footer"><a class="btn btn-primary btn-block waves-effect waves-float waves-light" href="javascript:void(0)">{{ __('Read all notifications') }}</a></li>
                    </ul>
            </div>
        </div>                         
    </div>
    </div>
</div>
</div>

@endsection
@section('javascript')
	<script type="text/javascript">
		document.querySelector('#Notifications').classList.add('active');
	</script>
@endsection