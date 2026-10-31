{{-- Modale document réglementaire. Variables : $vehicule (ou null) et $vehicules (liste si $vehicule null) --}}
<div class="modal fade text-left" id="modal_document" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('vehicule_document.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="{{ old('_form') == '#modal_document' ? old('_method', 'POST') : 'POST' }}">
                <input type="hidden" name="_form" value="#modal_document">
                <input type="hidden" name="_action" value="{{ old('_form') == '#modal_document' ? old('_action') : '' }}">
                <div class="modal-header">
                    <h4 class="modal-title">Document du véhicule</h4>
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
                            <label>Type de document <span class="text-danger">*</span></label>
                            <select name="type" class="form-control" required>
                                @foreach(\App\Models\Parc\Vehicule_document::TYPES as $cle => $libelle)
                                    <option value="{{ $cle }}" {{ old('type') == $cle ? 'selected' : '' }}>{{ $libelle }}</option>
                                @endforeach
                            </select>
                            @error('type')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Libellé</label>
                            <input type="text" name="libelle" class="form-control" placeholder="exp : Assurance tous risques" value="{{ old('libelle') }}">
                            @error('libelle')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Numéro / Référence</label>
                            <input type="text" name="numero" class="form-control" placeholder="Numéro de police, de carte..." value="{{ old('numero') }}">
                            @error('numero')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Fournisseur / Organisme</label>
                            <input type="text" name="fournisseur" class="form-control" placeholder="exp : Activa Assurances" value="{{ old('fournisseur') }}">
                            @error('fournisseur')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Date de début</label>
                            <input type="date" name="date_debut" class="form-control" value="{{ old('date_debut') }}">
                            @error('date_debut')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Date d'expiration</label>
                            <input type="date" name="date_expiration" class="form-control" value="{{ old('date_expiration') }}">
                            @error('date_expiration')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Montant (FCFA)</label>
                            <input type="number" min="0" step="1" name="montant" class="form-control" value="{{ old('montant') }}">
                            @error('montant')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-12 form-group">
                            <label>Fichier (PDF ou image, 5 Mo max)</label>
                            <input type="file" name="fichier" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
                            @error('fichier')<small class="text-danger">{{ $message }}</small>@enderror
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
