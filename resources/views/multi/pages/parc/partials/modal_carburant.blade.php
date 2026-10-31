{{-- Modale plein de carburant --}}
<div class="modal fade text-left" id="modal_carburant" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('vehicule_carburant.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="{{ old('_form') == '#modal_carburant' ? old('_method', 'POST') : 'POST' }}">
                <input type="hidden" name="_form" value="#modal_carburant">
                <input type="hidden" name="_action" value="{{ old('_form') == '#modal_carburant' ? old('_action') : '' }}">
                <input type="hidden" name="retour" value="{{ url()->current() }}">
                <div class="modal-header">
                    <h4 class="modal-title">Plein de carburant</h4>
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
                        <div class="col-md-6 form-group">
                            <label>Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                            @error('date')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Coursier</label>
                            <select name="id_coursier" class="form-control">
                                <option value="">Coursier actuel du véhicule</option>
                                @foreach($coursiers ?? [] as $c)
                                    <option value="{{ $c->id }}" {{ old('id_coursier') == $c->id ? 'selected' : '' }}>{{ $c->noms }} {{ $c->prenoms }}</option>
                                @endforeach
                            </select>
                            @error('id_coursier')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Litres <span class="text-danger">*</span></label>
                            <input type="number" min="0.1" step="0.01" name="litres" class="form-control" value="{{ old('litres') }}" required>
                            @error('litres')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Montant (FCFA) <span class="text-danger">*</span></label>
                            <input type="number" min="0" step="1" name="montant" class="form-control" value="{{ old('montant') }}" required>
                            @error('montant')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Kilométrage</label>
                            <input type="number" min="0" step="1" name="kilometrage" class="form-control" value="{{ old('kilometrage') }}">
                            @error('kilometrage')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-7 form-group">
                            <label>Station</label>
                            <input type="text" name="station" class="form-control" placeholder="exp : Total Bonabéri" value="{{ old('station') }}">
                            @error('station')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-5 form-group d-flex align-items-end">
                            <div class="custom-control custom-checkbox mb-1">
                                <input type="checkbox" class="custom-control-input" id="plein_complet" name="plein_complet" value="1" {{ old('_form') == '#modal_carburant' ? (old('plein_complet') ? 'checked' : '') : 'checked' }}>
                                <label class="custom-control-label" for="plein_complet">Plein complet</label>
                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            <label>Remarques</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                            @error('description')<small class="text-danger">{{ $message }}</small>@enderror
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
