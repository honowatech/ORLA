{{-- Formulaire partagé création / édition d'un tronçon. $troncon peut être null. --}}
@php
    $t = $troncon ?? null;
    $interdits = collect(old('types_vehicule_interdits', $t->types_vehicule_interdits ?? []))->map(fn ($v) => (int) $v)->all();
@endphp
<div class="row col-lg-10 col-md-12 mx-auto">
    <div class="col-md-4 col-12">
        <div class="form-group">
            <label for="id_ville">Ville</label> <span class="text-danger" title="Obligatoire">*</span>
            <select class="select2 form-control @error('id_ville') is-invalid @enderror" id="id_ville" name="id_ville" required onchange="filtrerQuartiers()">
                <option value="">Choisir une ville</option>
                @foreach($villes as $ville)
                    <option value="{{ $ville->id }}" {{ (int) old('id_ville', $t->id_ville ?? ($villes->count() == 1 ? $ville->id : '')) === (int) $ville->id ? 'selected' : '' }}>{{ $ville->libelle }}</option>
                @endforeach
            </select>
            @error('id_ville') <small class="alert alert-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="form-group">
            <label for="id_quartier_depart">Quartier de départ</label> <span class="text-danger" title="Obligatoire">*</span>
            <select class="select2 form-control quartier-select @error('id_quartier_depart') is-invalid @enderror" id="id_quartier_depart" name="id_quartier_depart" required>
                <option value="">Choisir un quartier</option>
                @foreach($quartiers as $q)
                    <option value="{{ $q->id }}" data-ville="{{ $q->id_ville }}" {{ (int) old('id_quartier_depart', $t->id_quartier_depart ?? 0) === (int) $q->id ? 'selected' : '' }}>{{ $q->libelle }}</option>
                @endforeach
            </select>
            @error('id_quartier_depart') <small class="alert alert-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="form-group">
            <label for="id_quartier_arrivee">Quartier d'arrivée</label> <span class="text-danger" title="Obligatoire">*</span>
            <select class="select2 form-control quartier-select @error('id_quartier_arrivee') is-invalid @enderror" id="id_quartier_arrivee" name="id_quartier_arrivee" required>
                <option value="">Choisir un quartier</option>
                @foreach($quartiers as $q)
                    <option value="{{ $q->id }}" data-ville="{{ $q->id_ville }}" {{ (int) old('id_quartier_arrivee', $t->id_quartier_arrivee ?? 0) === (int) $q->id ? 'selected' : '' }}>{{ $q->libelle }}</option>
                @endforeach
            </select>
            @error('id_quartier_arrivee') <small class="alert alert-danger">{{ $message }}</small> @enderror
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="form-group">
            <label for="distance_km">Distance (km)</label> <span class="text-danger" title="Obligatoire">*</span>
            <input type="number" step="0.01" min="0.01" id="distance_km" name="distance_km" class="form-control @error('distance_km') is-invalid @enderror" value="{{ old('distance_km', $t->distance_km ?? '') }}" required>
            @error('distance_km') <small class="alert alert-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="form-group">
            <label for="duree_minutes">Durée habituelle (min)</label>
            <input type="number" step="1" min="0" id="duree_minutes" name="duree_minutes" class="form-control @error('duree_minutes') is-invalid @enderror" placeholder="auto (vitesse véhicule)" value="{{ old('duree_minutes', $t->duree_minutes ?? '') }}">
            @error('duree_minutes') <small class="alert alert-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="form-group">
            <label for="etat_route">État de la route</label> <span class="text-danger" title="Obligatoire">*</span>
            <select class="form-control @error('etat_route') is-invalid @enderror" id="etat_route" name="etat_route" required>
                @foreach($etats as $cle => $lib)
                    <option value="{{ $cle }}" {{ old('etat_route', $t->etat_route ?? 'bon') === $cle ? 'selected' : '' }}>{{ $lib }}</option>
                @endforeach
            </select>
            @error('etat_route') <small class="alert alert-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="form-group">
            <label for="bidirectionnel">Sens de circulation</label>
            <select class="form-control" id="bidirectionnel" name="bidirectionnel">
                <option value="1" {{ (string) old('bidirectionnel', isset($t) ? (int) $t->bidirectionnel : 1) === '1' ? 'selected' : '' }}>Double sens</option>
                <option value="0" {{ (string) old('bidirectionnel', isset($t) ? (int) $t->bidirectionnel : 1) === '0' ? 'selected' : '' }}>Sens unique (départ → arrivée)</option>
            </select>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="form-group">
            <label for="heure_debut_fermeture">Fermé de</label>
            <input type="time" id="heure_debut_fermeture" name="heure_debut_fermeture" class="form-control @error('heure_debut_fermeture') is-invalid @enderror" value="{{ old('heure_debut_fermeture', isset($t->heure_debut_fermeture) ? substr($t->heure_debut_fermeture, 0, 5) : '') }}">
            @error('heure_debut_fermeture') <small class="alert alert-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="form-group">
            <label for="heure_fin_fermeture">à</label>
            <input type="time" id="heure_fin_fermeture" name="heure_fin_fermeture" class="form-control @error('heure_fin_fermeture') is-invalid @enderror" value="{{ old('heure_fin_fermeture', isset($t->heure_fin_fermeture) ? substr($t->heure_fin_fermeture, 0, 5) : '') }}">
            <small class="text-muted">Plage de fermeture (marché, travaux…). Vide = toujours ouvert.</small>
            @error('heure_fin_fermeture') <small class="alert alert-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-6 col-12">
        <div class="form-group">
            <label for="types_vehicule_interdits">Types de véhicule interdits sur ce tronçon</label>
            <select class="select2 form-control" id="types_vehicule_interdits" name="types_vehicule_interdits[]" multiple>
                @foreach($types_vehicule as $tv)
                    <option value="{{ $tv->id }}" {{ in_array((int) $tv->id, $interdits, true) ? 'selected' : '' }}>{{ $tv->libelle }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="2" class="form-control @error('description') is-invalid @enderror" placeholder="ex : route principale, nids de poule après le carrefour…">{{ old('description', $t->description ?? '') }}</textarea>
            @error('description') <small class="alert alert-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-12 text-center">
        <a href="{{ route('troncon.index') }}" class="btn btn-outline-secondary mt-1 mr-md-2"><i class="mr-50" data-feather='arrow-left'></i> Retour</a>
        <button type="submit" class="btn btn-success mt-1"><i class="mr-50" data-feather='download'></i> Enregistrer</button>
    </div>
</div>
@section('javascript')
<script>
    function filtrerQuartiers() {
        var ville = document.getElementById('id_ville').value;
        document.querySelectorAll('.quartier-select option[data-ville]').forEach(function (opt) {
            var visible = ville === '' || opt.dataset.ville === ville;
            opt.hidden = !visible;
            if (!visible && opt.selected) { opt.selected = false; }
        });
    }
    document.addEventListener('DOMContentLoaded', filtrerQuartiers);
</script>
@endsection
