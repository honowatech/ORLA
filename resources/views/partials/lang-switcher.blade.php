{{-- Sélecteur de langue FR / EN. Usage : @include('partials.lang-switcher') dans une barre de navigation (<ul class="nav navbar-nav">). --}}
@php($locales = config('app.available_locales', []))
<li class="nav-item dropdown dropdown-language">
    <a class="nav-link dropdown-toggle" id="dropdown-language" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="{{ __('Langue') }}">
        <i data-feather="globe"></i>
        <span class="selected-language font-weight-bolder ml-25">{{ strtoupper(app()->getLocale()) }}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-language">
        @foreach($locales as $code => $label)
            <a class="dropdown-item @if(app()->getLocale() === $code) active @endif" href="{{ route('lang.switch', $code) }}" hreflang="{{ $code }}" lang="{{ $code }}">
                <span class="font-weight-bolder mr-50">{{ strtoupper($code) }}</span> {{ $label }}
            </a>
        @endforeach
    </div>
</li>
