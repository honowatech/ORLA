@if (\App\Demo\Demo::current())
    @php
        $demoEnds = \App\Demo\Demo::current()->expires_at->copy()->setTimezone(config('demo.timezone'));
    @endphp
    <style>
        .demo-banner { position: sticky; top: 0; z-index: 1100; display: flex; flex-wrap: wrap; align-items: center; gap: 6px 14px;
            padding: 8px 16px; background: #fff4dc; color: #6b4600; font-size: .9rem; border-bottom: 1px solid #f0d9a8; }
        .demo-banner strong { color: #4d3200; }
        .demo-banner form { display: inline; margin: 0; }
        .demo-banner button, .demo-banner summary { all: unset; cursor: pointer; color: #5b4bd6; font-weight: 600; }
        .demo-banner button:hover, .demo-banner summary:hover { text-decoration: underline; }
        .demo-banner details { position: relative; }
        .demo-banner details ul { position: absolute; left: 0; top: 1.8em; list-style: none; margin: 0; padding: 6px 0; min-width: 190px;
            background: #fff; border: 1px solid #e3e5ea; border-radius: 6px; box-shadow: 0 6px 20px rgba(0,0,0,.12); }
        .demo-banner details li button { display: block; padding: 6px 14px; width: 100%; box-sizing: border-box; color: #1f2433; font-weight: 400; }
        .demo-banner .demo-sep { color: #c9a868; }
    </style>
    <div class="demo-banner" role="region" aria-label="Démo">
        <span><strong>Démo :</strong> données fictives, effacées à {{ $demoEnds->format('H:i') }}. N'y saisissez pas de vraies données.</span>
        <details>
            <summary>Changer de profil</summary>
            <ul>
                @foreach (config('demo.profiles') as $slug => $profile)
                    <li>
                        <form method="POST" action="{{ route('demo.profile') }}">
                            @csrf
                            <button type="submit" name="profile" value="{{ $slug }}">{{ $profile['title'] }}</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </details>
        <span class="demo-sep">·</span>
        <form method="POST" action="{{ route('demo.end') }}">
            @csrf
            <button type="submit">Terminer</button>
        </form>
        @if (config('demo.signup_url'))
            <span class="demo-sep">·</span>
            <form method="POST" action="{{ route('demo.end') }}">
                @csrf
                <input type="hidden" name="signup" value="1">
                <button type="submit">Créer mon compte</button>
            </form>
        @endif
    </div>
@endif
