{{-- Modale relevé de kilométrage (fiche véhicule) --}}
<div class="modal fade text-left" id="modal_kilometrage" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('vehicule_kilometrage.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_form" value="#modal_kilometrage">
                <input type="hidden" name="id_vehicule" value="{{ $vehicule->id }}">
                <div class="modal-header">
                    <h4 class="modal-title">Relevé de kilométrage</h4>
                    <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Compteur actuel : <b>{{ formatMontant($vehicule->kilometrage) }} km</b></p>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                            @error('date')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Kilométrage (km) <span class="text-danger">*</span></label>
                            <input type="number" min="0" step="1" name="kilometrage" class="form-control" value="{{ old('kilometrage') }}" required>
                            @error('kilometrage')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-12 form-group">
                            <label>Remarques</label>
                            <input type="text" name="description" class="form-control" value="{{ old('description') }}">
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
