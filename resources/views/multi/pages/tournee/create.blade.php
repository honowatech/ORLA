@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')

@section('title')
{{'Planifier une tournée optimisée'}}
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
                        <h2 class="content-header-title float-left mb-0">Planifier une tournée optimisée</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a></li>
                                <li class="breadcrumb-item"><a href="{{route('tournee.index')}}">Tournées</a></li>
                                <li class="breadcrumb-item active">Planifier</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            @if($errors->any())
                <div class="alert alert-danger p-1">
                    @foreach($errors->all() as $erreur) <div>{{ $erreur }}</div> @endforeach
                </div>
            @endif
            <form class="form" id="form_tournee" action="{{ route('tournee.store') }}" method="POST">
                @csrf
                <div class="row">
                    {{-- Paramètres --}}
                    <div class="col-lg-4 col-12">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title"><i data-feather="sliders" class="mr-50"></i> Paramètres</h4></div>
                            <div class="card-body">
                                @if($villes->count() > 1)
                                <div class="form-group">
                                    <label for="id_ville">Ville</label>
                                    <select class="form-control" id="id_ville" name="id_ville" onchange="window.location = '{{ route('tournee.create') }}?id_ville=' + this.value">
                                        @foreach($villes as $ville)
                                            <option value="{{ $ville->id }}" {{ $ville->id == $id_ville ? 'selected' : '' }}>{{ $ville->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @else
                                    <input type="hidden" name="id_ville" value="{{ $id_ville }}">
                                @endif

                                <div class="form-group">
                                    <label for="id_coursier">Livreur</label> <span class="text-danger">*</span>
                                    <select class="select2 form-control" id="id_coursier" name="id_coursier" required onchange="choisirVehiculeDuLivreur()">
                                        <option value="">Choisir un livreur</option>
                                        @foreach($coursiers as $c)
                                            <option value="{{ $c->id }}" {{ old('id_coursier') == $c->id ? 'selected' : '' }}>{{ $c->noms }} {{ $c->prenoms }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="id_vehicule">Véhicule</label>
                                    <select class="select2 form-control" id="id_vehicule" name="id_vehicule">
                                        <option value="">Aucun / non précisé (vitesse par défaut)</option>
                                        @foreach($vehicules as $v)
                                            <option value="{{ $v->id }}" data-coursier="{{ $v->id_coursier }}" {{ old('id_vehicule') == $v->id ? 'selected' : '' }}>
                                                {{ $v->immatriculation }} — {{ $v->type->libelle ?? '' }}
                                                @if($v->type && $v->type->heure_debut_autorisee) ({{ substr($v->type->heure_debut_autorisee,0,5) }}→{{ substr($v->type->heure_fin_autorisee,0,5) }}) @endif
                                                @if($v->coursier) · {{ $v->coursier->noms }} @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Le type de véhicule détermine la vitesse, les coûts, la plage horaire autorisée et les tronçons interdits.</small>
                                </div>

                                <div class="form-group">
                                    <label for="id_quartier_depart">Quartier de départ (dépôt / agence)</label> <span class="text-danger">*</span>
                                    <select class="select2 form-control" id="id_quartier_depart" name="id_quartier_depart" required>
                                        <option value="">Choisir un quartier</option>
                                        @foreach($quartiers as $q)
                                            <option value="{{ $q->id }}" {{ old('id_quartier_depart') == $q->id ? 'selected' : '' }}>{{ $q->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="date_depart">Date et heure de départ</label> <span class="text-danger">*</span>
                                    <input type="datetime-local" class="form-control" id="date_depart" name="date_depart" required value="{{ old('date_depart', now()->addHour()->format('Y-m-d\TH:00')) }}">
                                    <small class="text-muted">L'heure influence le trafic, les fermetures de tronçons et les plages horaires autorisées.</small>
                                </div>

                                <div class="custom-control custom-checkbox mb-1">
                                    <input type="checkbox" class="custom-control-input" id="avec_enlevement" name="avec_enlevement" value="1" {{ old('avec_enlevement', '1') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="avec_enlevement">Inclure l'enlèvement des colis (passage au quartier d'origine avant livraison)</label>
                                </div>

                                <button type="button" class="btn btn-primary btn-block" onclick="simuler()"><i data-feather="cpu" class="mr-50"></i> Calculer l'itinéraire optimal</button>
                                <button type="submit" class="btn btn-success btn-block" id="btn_enregistrer" disabled><i data-feather="save" class="mr-50"></i> Enregistrer la tournée et attribuer au livreur</button>
                            </div>
                        </div>
                    </div>

                    {{-- Commandes --}}
                    <div class="col-lg-8 col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title"><i data-feather="package" class="mr-50"></i> Commandes à planifier <span class="badge badge-light-primary" id="nb_selection">0</span></h4>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="toutCocher(true)">Tout</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toutCocher(false)">Aucun</button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 480px; overflow-y:auto;">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th></th>
                                                <th>N°</th>
                                                <th>Client</th>
                                                <th>Enlèvement</th>
                                                <th>Livraison</th>
                                                <th>À livrer avant</th>
                                                <th>Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($commandes as $cmd)
                                            <tr>
                                                <td><input type="checkbox" class="cmd-check" name="commandes[]" value="{{ $cmd->id }}" {{ in_array($cmd->id, old('commandes', [])) ? 'checked' : '' }} onchange="compter()"></td>
                                                <td class="font-weight-bolder">#{{ $cmd->id }}</td>
                                                <td>{{ $cmd->nom_client ?: ($cmd->client->noms ?? '') }}<br><small class="text-muted">{{ $cmd->telephone }}</small></td>
                                                <td>{{ $cmd->quartier_colis->libelle ?? '—' }}<br><small class="text-muted">{{ \Illuminate\Support\Str::limit($cmd->adresse_colis, 30) }}</small></td>
                                                <td>{{ $cmd->quartier_livraison->libelle ?? '—' }}<br><small class="text-muted">{{ \Illuminate\Support\Str::limit($cmd->adresse_livraison, 30) }}</small></td>
                                                <td>{{ $cmd->date_livraison ? date('d/m H:i', strtotime($cmd->date_livraison)) : '—' }}</td>
                                                <td><span class="badge badge-pill badge-light-{{ $cmd->statut == 'attente' ? 'warning' : 'info' }}">{{ $cmd->statut == 'attente' ? 'En attente' : 'Attribuée' }}</span></td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="7" class="text-center text-muted p-2">Aucune commande planifiable dans cette ville (en attente ou attribuée, hors tournée en cours).</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card" id="carte_resultat" style="display:none;">
                            <div class="card-header"><h4 class="card-title"><i data-feather="navigation" class="mr-50"></i> Résultat de l'optimisation</h4></div>
                            <div class="card-body" id="resultat"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="speedexspinner" hidden><div class="text-center p-2"><div class="spinner-border text-primary" role="status"></div><div class="mt-1">Calcul de l'itinéraire optimal…</div></div></div>
@endsection
@section('javascript')
<script>
    function compter() {
        var n = document.querySelectorAll('.cmd-check:checked').length;
        document.getElementById('nb_selection').textContent = n;
        document.getElementById('btn_enregistrer').disabled = true;
    }
    function toutCocher(etat) {
        document.querySelectorAll('.cmd-check').forEach(function (c) { c.checked = etat; });
        compter();
    }
    function choisirVehiculeDuLivreur() {
        var idCoursier = document.getElementById('id_coursier').value;
        var select = document.getElementById('id_vehicule');
        var trouve = Array.from(select.options).find(function (o) { return o.dataset.coursier === idCoursier && idCoursier !== ''; });
        if (trouve) { select.value = trouve.value; if (window.jQuery) { jQuery(select).trigger('change'); } }
    }
    function simuler() {
        var form = document.getElementById('form_tournee');
        if (document.querySelectorAll('.cmd-check:checked').length === 0) { alert('Sélectionnez au moins une commande.'); return; }
        if (!form.id_quartier_depart.value || !form.date_depart.value) { alert('Renseignez le quartier et la date de départ.'); return; }
        var carte = document.getElementById('carte_resultat'), zone = document.getElementById('resultat');
        carte.style.display = 'block';
        zone.innerHTML = document.getElementById('speedexspinner').innerHTML;
        var data = new FormData(form);
        fetch("{{ route('tournee.simuler') }}", { method: 'POST', body: data, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.text().then(function (html) { return { ok: r.ok, html: html }; }); })
            .then(function (res) {
                zone.innerHTML = res.ok ? res.html : "<div class='alert alert-danger p-1'>Erreur lors du calcul : vérifiez les paramètres (livreur non requis pour la simulation).</div>";
                if (res.ok) { document.getElementById('btn_enregistrer').disabled = false; }
                if (window.feather) { feather.replace(); }
                carte.scrollIntoView({ behavior: 'smooth' });
            })
            .catch(function () { zone.innerHTML = "<div class='alert alert-danger p-1'>Un problème est survenu, veuillez réessayer.</div>"; });
    }
    document.addEventListener('DOMContentLoaded', compter);
</script>
@endsection
