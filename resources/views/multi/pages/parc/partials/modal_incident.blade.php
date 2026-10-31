{{-- Modale incident / sinistre --}}
<div class="modal fade text-left" id="modal_incident" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('vehicule_incident.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="{{ old('_form') == '#modal_incident' ? old('_method', 'POST') : 'POST' }}">
                <input type="hidden" name="_form" value="#modal_incident">
                <input type="hidden" name="_action" value="{{ old('_form') == '#modal_incident' ? old('_action') : '' }}">
                <input type="hidden" name="retour" value="{{ url()->current() }}">
                <div class="modal-header">
                    <h4 class="modal-title">Incident / Sinistre</h4>
                    <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @if(isset($vehicule) && $vehicule)
                            <input type="hidden" name="id_vehicule" value="{{ $vehicule->id }}">
                        @else
                            <div class="col-md-12 form-group">
                                <label>Véhicule <span class="text-danger">*</span></label>
                                <select name="id_vehicule" class="form-control" required>
                                    <option value="">Choisir un véhicule</option>
                                    @foreach($vehicules as $v)
                                        <option value="{{ $v->id }}" {{ old('id_vehicule') == $v->id ? 'selected' : '' }}>{{ $v->immatriculation }} - {{ $v->marque }} {{ $v->modele }}</option>
                                    @endforeach
                                </select>
                                @error('id_vehicule')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        @endif
                        <div class="col-md-4 form-group">
                            <label>Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-control" required>
                                @foreach(\App\Models\Parc\Vehicule_incident::TYPES as $cle => $libelle)
                                    <option value="{{ $cle }}" {{ old('type') == $cle ? 'selected' : '' }}>{{ $libelle }}</option>
                                @endforeach
                            </select>
                            @error('type')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Date et heure <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="date" class="form-control" value="{{ old('date', date('Y-m-d\TH:i')) }}" required>
                            @error('date')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Coursier impliqué</label>
                            <select name="id_coursier" class="form-control">
                                <option value="">Coursier actuel du véhicule</option>
                                @foreach($coursiers ?? [] as $c)
                                    <option value="{{ $c->id }}" {{ old('id_coursier') == $c->id ? 'selected' : '' }}>{{ $c->noms }} {{ $c->prenoms }}</option>
                                @endforeach
                            </select>
                            @error('id_coursier')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-8 form-group">
                            <label>Lieu</label>
                            <input type="text" name="lieu" class="form-control" value="{{ old('lieu') }}">
                            @error('lieu')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Coût (FCFA)</label>
                            <input type="number" min="0" step="1" name="cout" class="form-control" value="{{ old('cout') }}">
                            @error('cout')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-12 form-group">
                            <label>Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
                            @error('description')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Statut <span class="text-danger">*</span></label>
                            <select name="statut" class="form-control" required>
                                @foreach(\App\Models\Parc\Vehicule_incident::STATUTS as $cle => $libelle)
                                    <option value="{{ $cle }}" {{ old('statut', 'ouvert') == $cle ? 'selected' : '' }}>{{ $libelle }}</option>
                                @endforeach
                            </select>
                            @error('statut')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Date de résolution <small class="text-muted">(si résolu)</small></label>
                            <input type="date" name="date_resolution" class="form-control" value="{{ old('date_resolution') }}">
                            @error('date_resolution')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4 form-group d-flex align-items-end">
                            <div class="custom-control custom-checkbox mb-1">
                                <input type="checkbox" class="custom-control-input" id="immobilise" name="immobilise" value="1" {{ old('immobilise') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="immobilise">Véhicule immobilisé</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <button type="submit" class="btn btn-success"><i data-feather="save" class="mr-50"></i> Enregistrer</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>
