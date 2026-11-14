{{-- Liens FR / EN compacts pour les pages hors connexion (login, erreurs). --}}
<div class="text-center mt-1 lang-links">
    @foreach(config('app.available_locales', []) as $code => $label)
        @if(! $loop->first) <span class="text-muted mx-50">|</span> @endif
        @if(app()->getLocale() === $code)
            <span class="font-weight-bolder" lang="{{ $code }}">{{ $label }}</span>
        @else
            <a href="{{ route('lang.switch', $code) }}" hreflang="{{ $code }}" lang="{{ $code }}">{{ $label }}</a>
        @endif
    @endforeach
</div>
