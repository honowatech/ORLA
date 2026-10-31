{{-- Modale entretien / réparation --}}
<div class="modal fade text-left" id="modal_maintenance" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('vehicule_maintenance.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="{{ old('_form') == '#modal_maintenance' ? old('_method', 'POST') : 'POST' }}">
                <input type="hidden" name="_form" value="#modal_maintenance">
                <input type="hidden" name="_action" value="{{ old('_form') == '#modal_maintenance' ? old('_action') : '' }}">
                <input type="hidden" name="retour" value="{{ url()->current() }}">
                <div class="modal-header">
                    <h4 class="modal-title">Entretien / Réparation</h4>
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
                                        <option value="{{ $v->id }}" {{ old('id_vehicule') == $v->id ? 'selected' : '' }}>{{ $v->immatriculation }} - {{ $v->marque }} {{ $v->modele }} ({{ formatMontant($v->kilometrage) }} km)</option>
                                    @endforeach
                                </select>
                                @error('id_vehicule')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        @endif
                        <div class="col-md-4 form-group">
                            <label>Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-control" required>
                                @foreach(\App\Models\Parc\Vehicule_maintenance::TYPES as $cle => $libelle)
                                    <option value="{{ $cle }}" {{ old('type') == $cle ? 'selected' : '' }}>{{ $libelle }}</option>
                                @endforeach
                            </select>
                            @error('type')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-5 form-group">
                            <label>Libellé <span class="text-danger">*</span></label>
                            <input type="text" name="libelle" class="form-control" placeholder="exp : Vidange + filtre à huile" value="{{ old('libelle') }}" required>
                            @error('libelle')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Statut <span class="text-danger">*</span></label>
                            <select name="statut" class="form-control" required>
                                @foreach(\App\Models\Parc\Vehicule_maintenance::STATUTS as $cle => $libelle)
                                    <option value="{{ $cle }}" {{ old('statut', 'planifie') == $cle ? 'selected' : '' }}>{{ $libelle }}</option>
                                @endforeach
                            </select>
                            @error('statut')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Date prévue</label>
                            <input type="date" name="date_prevue" class="form-control" value="{{ old('date_prevue') }}">
                            @error('date_prevue')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Date de réalisation <small class="text-muted">(si réalisé)</small></label>
                            <input type="date" name="date_realisation" class="form-control" value="{{ old('date_realisation') }}">
                            @error('date_realisation')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Kilométrage à l'intervention</label>
                            <input type="number" min="0" step="1" name="kilometrage" class="form-control" value="{{ old('kilometrage') }}">
                            @error('kilometrage')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Coût (FCFA)</label>
                            <input type="number" min="0" step="1" name="cout" class="form-control" value="{{ old('cout') }}">
                            @error('cout')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Prestataire / Garage</label>
                            <input type="text" name="prestataire" class="form-control" value="{{ old('prestataire') }}">
                            @error('prestataire')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-12"><hr><p class="text-muted mb-50"><i data-feather="repeat" class="mr-25"></i> Prochaine échéance (facultatif) : déclenche une alerte à la date ou au kilométrage indiqué.</p></div>
                        <div class="col-md-4 form-group">
                            <label>Prochaine date</label>
                            <input type="date" name="prochaine_date" class="form-control" value="{{ old('prochaine_date') }}">
                            @error('prochaine_date')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Prochain kilométrage</label>
                            <input type="number" min="0" step="1" name="prochain_kilometrage" class="form-control" value="{{ old('prochain_kilometrage') }}">
                            @error('prochain_kilometrage')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4 form-group d-flex align-items-end">
                            <div class="custom-control custom-checkbox mb-1">
                                <input type="checkbox" class="custom-control-input" id="planifier_suivant" name="planifier_suivant" value="1" {{ old('planifier_suivant') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="planifier_suivant">Planifier automatiquement le suivant</label>
                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            <label>Description / Pièces remplacées</label>
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
