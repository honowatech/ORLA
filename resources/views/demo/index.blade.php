<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Démo Speedex</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('app-assets/images/ico/favicon.ico') }}">
    <style>
        :root { --ink:#1f2433; --muted:#5e6475; --line:#e3e5ea; --brand:#7367f0; --bg:#f6f7fb; --card:#fff; --warn:#8a5a00; --warn-bg:#fff4dc; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: "Montserrat", system-ui, -apple-system, "Segoe UI", sans-serif; color:var(--ink); background:var(--bg); }
        main { max-width: 960px; margin: 0 auto; padding: 48px 16px 64px; }
        h1 { font-size: 1.9rem; margin: 0 0 8px; }
        .lead { color: var(--muted); margin: 0 0 32px; line-height: 1.5; max-width: 640px; }
        .notice { background: var(--warn-bg); color: var(--warn); border-radius: 8px; padding: 12px 16px; margin-bottom: 24px; }
        .grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; }
        .profile { all: unset; cursor: pointer; display:flex; flex-direction:column; gap:8px; background:var(--card); border:1px solid var(--line); border-radius: 10px; padding: 20px; transition: border-color .15s, box-shadow .15s; }
        .profile:hover, .profile:focus-visible { border-color: var(--brand); box-shadow: 0 4px 18px rgba(115,103,240,.15); }
        .profile strong { font-size: 1.05rem; }
        .profile span { color: var(--muted); font-size: .92rem; line-height: 1.45; }
        .profile em { font-style: normal; color: var(--brand); font-weight: 600; font-size: .9rem; margin-top: auto; }
        .fine { color: var(--muted); font-size: .85rem; margin-top: 28px; line-height: 1.5; }
        a { color: var(--brand); }
    </style>
</head>
<body>
<main>
    <h1>Essayer Speedex</h1>
    <p class="lead">
        Choisissez un profil : vous entrez dans votre propre agence de livraison à Douala,
        avec deux mois d'activité, sans mot de passe. Vous pouvez tout faire : saisir une
        commande, l'attribuer, la livrer. Personne d'autre ne voit votre démo.
    </p>

    @if (session('demo_message'))
        <p class="notice" role="status">{{ session('demo_message') }}</p>
    @elseif ($inDemo)
        <p class="notice">Une démo est déjà ouverte dans ce navigateur. <a href="{{ route('home') }}">La reprendre</a>.</p>
    @elseif (! $ready)
        <p class="notice">Les démos sont en préparation. Réessayez dans quelques minutes.</p>
    @endif

    @error('profile')
        <p class="notice" role="alert">Ce profil n'existe pas.</p>
    @enderror

    <form method="POST" action="{{ route('demo.enter') }}">
        @csrf
        <div class="grid">
            @foreach ($profiles as $slug => $profile)
                <button type="submit" name="profile" value="{{ $slug }}" class="profile">
                    <strong>{{ $profile['title'] }}</strong>
                    <span>{{ $profile['text'] }}</span>
                    <em>Entrer →</em>
                </button>
            @endforeach
        </div>
    </form>

    <p class="fine">
        Données fictives, effacées au bout de {{ config('demo.idle_minutes') / 60 }} h d'inactivité
        (au plus {{ config('demo.max_hours') }} h). N'y saisissez pas de vraies données :
        aucun SMS, e-mail ni paiement n'est envoyé depuis la démo.
        <a href="{{ route('login') }}">Vous avez un compte ? Connexion</a>.
    </p>
</main>
</body>
</html>
