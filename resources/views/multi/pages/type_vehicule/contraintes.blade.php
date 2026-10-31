{{-- Contraintes d'exploitation utilisées par l'optimiseur de tournées. $type_vehicule peut être null (création). --}}
@php $tv = $type_vehicule ?? null; @endphp
<div class="col-12 mt-1">
    <h5 class="text-primary"><i data-feather='settings' class="mr-50"></i> Contraintes d'exploitation (optimisation des tournées)</h5>
    <small class="text-muted">Ces paramètres servent à calculer les itinéraires, les durées et les coûts des tournées.</small>
    <hr>
</div>
<div class="col-md-4 col-12">
    <div class="form-group">
        <label for="vitesse_moyenne">Vitesse moyenne (km/h)</label>
        <input type="number" step="1" min="1" max="200" id="vitesse_moyenne" class="form-control @error('vitesse_moyenne') is-invalid @enderror" name="vitesse_moyenne" placeholder="30" value="{{ old('vitesse_moyenne', $tv->vitesse_moyenne ?? '') }}">
        @error('vitesse_moyenne') <small class="alert alert-danger">{{ $message }}</small> @enderror
    </div>
</div>
<div class="col-md-4 col-12">
    <div class="form-group">
        <label for="cout_km">Coût par km (FCFA)</label>
        <input type="number" step="0.01" min="0" id="cout_km" class="form-control @error('cout_km') is-invalid @enderror" name="cout_km" placeholder="ex : 75" value="{{ old('cout_km', $tv->cout_km ?? '') }}">
        @error('cout_km') <small class="alert alert-danger">{{ $message }}</small> @enderror
    </div>
</div>
<div class="col-md-4 col-12">
    <div class="form-group">
        <label for="cout_heure">Coût horaire (FCFA)</label>
        <input type="number" step="0.01" min="0" id="cout_heure" class="form-control @error('cout_heure') is-invalid @enderror" name="cout_heure" placeholder="ex : 1500" value="{{ old('cout_heure', $tv->cout_heure ?? '') }}">
        @error('cout_heure') <small class="alert alert-danger">{{ $message }}</small> @enderror
    </div>
</div>
<div class="col-md-4 col-12">
    <div class="form-group">
        <label for="capacite_colis">Capacité (nb de commandes par tournée)</label>
        <input type="number" step="1" min="1" id="capacite_colis" class="form-control @error('capacite_colis') is-invalid @enderror" name="capacite_colis" placeholder="illimitée" value="{{ old('capacite_colis', $tv->capacite_colis ?? '') }}">
        @error('capacite_colis') <small class="alert alert-danger">{{ $message }}</small> @enderror
    </div>
</div>
<div class="col-md-4 col-12">
    <div class="form-group">
        <label for="heure_debut_autorisee">Circulation autorisée à partir de</label>
        <input type="time" id="heure_debut_autorisee" class="form-control @error('heure_debut_autorisee') is-invalid @enderror" name="heure_debut_autorisee" value="{{ old('heure_debut_autorisee', isset($tv->heure_debut_autorisee) ? substr($tv->heure_debut_autorisee, 0, 5) : '') }}">
        @error('heure_debut_autorisee') <small class="alert alert-danger">{{ $message }}</small> @enderror
    </div>
</div>
<div class="col-md-4 col-12">
    <div class="form-group">
        <label for="heure_fin_autorisee">jusqu'à</label>
        <input type="time" id="heure_fin_autorisee" class="form-control @error('heure_fin_autorisee') is-invalid @enderror" name="heure_fin_autorisee" value="{{ old('heure_fin_autorisee', isset($tv->heure_fin_autorisee) ? substr($tv->heure_fin_autorisee, 0, 5) : '') }}">
        <small class="text-muted">Laisser vide si aucune restriction horaire (ex : camions interdits en centre-ville la journée).</small>
        @error('heure_fin_autorisee') <small class="alert alert-danger">{{ $message }}</small> @enderror
    </div>
</div>
