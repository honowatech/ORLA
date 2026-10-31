{{-- Champs de gestion de parc partagés par les formulaires de création et d'édition --}}
@php $v = $vehicule ?? null; @endphp
<div class="col-12 mt-1 mb-1">
    <h5 class="text-muted"><i data-feather="settings" class="mr-50"></i> Informations de parc <span class="text-info font-small-3">(optionnel)</span></h5>
    <hr class="mt-0">
</div>
<div class="col-md-6 col-12 m-auto">
    <div class="form-group">
        <label for="kilometrage">Kilométrage actuel (km)</label>
        <div class="input-group input-group-merge @error('kilometrage') is-invalid @enderror">
            <div class="input-group-prepend"><span class="input-group-text"><i data-feather='activity'></i></span></div>
            <input type="number" min="0" step="1" id="kilometrage" class="form-control @error('kilometrage') is-invalid @enderror" name="kilometrage" placeholder="exp : 25000" value="{{ old('kilometrage', $v ? $v->kilometrage : '') }}" />
        </div>
        @error('kilometrage')<small class="alert alert-danger">{{ $message }}</small>@enderror
    </div>
</div>
<div class="col-md-6 col-12 m-auto">
    <div class="form-group">
        <label for="etat">État opérationnel</label>
        <select class="form-control @error('etat') is-invalid @enderror" id="etat" name="etat">
            @foreach(\App\Models\Vehicule::ETATS as $cle => $libelle)
                <option value="{{$cle}}" {{ old('etat', $v ? $v->etat : 'disponible') == $cle ? 'selected' : '' }}>{{$libelle}}</option>
            @endforeach
        </select>
        @error('etat')<small class="alert alert-danger">{{ $message }}</small>@enderror
    </div>
</div>
<div class="col-md-6 col-12 m-auto">
    <div class="form-group">
        <label for="carburant">Carburant</label>
        <select class="form-control @error('carburant') is-invalid @enderror" id="carburant" name="carburant">
            <option value="">Non précisé</option>
            @foreach(\App\Models\Vehicule::CARBURANTS as $cle => $libelle)
                <option value="{{$cle}}" {{ old('carburant', $v ? $v->carburant : '') == $cle ? 'selected' : '' }}>{{$libelle}}</option>
            @endforeach
        </select>
        @error('carburant')<small class="alert alert-danger">{{ $message }}</small>@enderror
    </div>
</div>
<div class="col-md-6 col-12 m-auto">
    <div class="form-group">
        <label for="capacite_reservoir">Capacité du réservoir (L)</label>
        <div class="input-group input-group-merge @error('capacite_reservoir') is-invalid @enderror">
            <div class="input-group-prepend"><span class="input-group-text"><i data-feather='droplet'></i></span></div>
            <input type="number" min="0" step="0.1" id="capacite_reservoir" class="form-control @error('capacite_reservoir') is-invalid @enderror" name="capacite_reservoir" placeholder="exp : 12" value="{{ old('capacite_reservoir', $v ? $v->capacite_reservoir : '') }}" />
        </div>
        @error('capacite_reservoir')<small class="alert alert-danger">{{ $message }}</small>@enderror
    </div>
</div>
<div class="col-md-6 col-12 m-auto">
    <div class="form-group">
        <label for="numero_chassis">Numéro de châssis (VIN)</label>
        <div class="input-group input-group-merge @error('numero_chassis') is-invalid @enderror">
            <div class="input-group-prepend"><span class="input-group-text"><i data-feather='hash'></i></span></div>
            <input type="text" id="numero_chassis" class="form-control @error('numero_chassis') is-invalid @enderror" name="numero_chassis" placeholder="Numéro de châssis" value="{{ old('numero_chassis', $v ? $v->numero_chassis : '') }}" />
        </div>
        @error('numero_chassis')<small class="alert alert-danger">{{ $message }}</small>@enderror
    </div>
</div>
<div class="col-md-6 col-12 m-auto">
    <div class="form-group">
        <label for="annee">Année de mise en circulation</label>
        <div class="input-group input-group-merge @error('annee') is-invalid @enderror">
            <div class="input-group-prepend"><span class="input-group-text"><i data-feather='calendar'></i></span></div>
            <input type="number" min="1950" max="{{date('Y')+1}}" step="1" id="annee" class="form-control @error('annee') is-invalid @enderror" name="annee" placeholder="exp : {{date('Y')}}" value="{{ old('annee', $v ? $v->annee : '') }}" />
        </div>
        @error('annee')<small class="alert alert-danger">{{ $message }}</small>@enderror
    </div>
</div>
<div class="col-md-6 col-12 m-auto">
    <div class="form-group">
        <label for="date_acquisition">Date d'acquisition</label>
        <input type="date" id="date_acquisition" class="form-control @error('date_acquisition') is-invalid @enderror" name="date_acquisition" value="{{ old('date_acquisition', $v ? $v->date_acquisition : '') }}" />
        @error('date_acquisition')<small class="alert alert-danger">{{ $message }}</small>@enderror
    </div>
</div>
<div class="col-md-6 col-12 m-auto">
    <div class="form-group">
        <label for="prix_acquisition">Prix d'acquisition (FCFA)</label>
        <div class="input-group input-group-merge @error('prix_acquisition') is-invalid @enderror">
            <div class="input-group-prepend"><span class="input-group-text"><i data-feather='dollar-sign'></i></span></div>
            <input type="number" min="0" step="1" id="prix_acquisition" class="form-control @error('prix_acquisition') is-invalid @enderror" name="prix_acquisition" placeholder="exp : 850000" value="{{ old('prix_acquisition', $v ? $v->prix_acquisition : '') }}" />
        </div>
        @error('prix_acquisition')<small class="alert alert-danger">{{ $message }}</small>@enderror
    </div>
</div>
