{{-- Modale d'attribution d'un coursier (fiche véhicule) --}}
<div class="modal fade text-left" id="modal_attribution" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('vehicule.attribuer', $vehicule->id) }}" method="POST">
                @csrf
                <input type="hidden" name="_form" value="#modal_attribution">
                <div class="modal-header">
                    <h4 class="modal-title">Attribuer le véhicule</h4>
                    <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    @if($vehicule->coursier)
                        <div class="alert alert-warning p-1">Ce véhicule est actuellement attribué à <b>{{ $vehicule->coursier->noms }} {{ $vehicule->coursier->prenoms }}</b>. L'attribution en cours sera clôturée.</div>
                    @endif
                    <div class="form-group">
                        <label>Coursier <span class="text-danger">*</span></label>
                        <select name="id_coursier" class="form-control select2" required>
                            <option value="">Choisir un coursier</option>
                            @foreach($coursiers as $c)
                                <option value="{{ $c->id }}" {{ old('id_coursier') == $c->id ? 'selected' : '' }}>{{ $c->noms }} {{ $c->prenoms }} ({{ $c->vehicules->count() }} véhicule(s))</option>
                            @endforeach
                        </select>
                        @error('id_coursier')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="form-group">
                        <label>Kilométrage au moment de la remise</label>
                        <input type="number" min="0" step="1" name="kilometrage" class="form-control" value="{{ old('kilometrage', $vehicule->kilometrage) }}">
                        @error('kilometrage')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <button type="submit" class="btn btn-success"><i data-feather="user-check" class="mr-50"></i> Attribuer</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="modal_restitution" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('vehicule.destroy', $vehicule->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="attribuate" value="0">
                <div class="modal-header">
                    <h4 class="modal-title">Restitution du véhicule</h4>
                    <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Retirer le véhicule à <b>{{ $vehicule->coursier ? $vehicule->coursier->noms.' '.$vehicule->coursier->prenoms : '' }}</b> ?</p>
                    <div class="form-group">
                        <label>Kilométrage à la restitution</label>
                        <input type="number" min="0" step="1" name="kilometrage" class="form-control" value="{{ $vehicule->kilometrage }}">
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <button type="submit" class="btn btn-warning"><i data-feather="user-x" class="mr-50"></i> Désattribuer</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="modal_etat" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('vehicule.etat', $vehicule->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h4 class="modal-title">Changer l'état opérationnel</h4>
                    <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nouvel état</label>
                        <select name="etat" class="form-control" required>
                            @foreach(\App\Models\Vehicule::ETATS as $cle => $libelle)
                                <option value="{{ $cle }}" {{ $vehicule->etat == $cle ? 'selected' : '' }}>{{ $libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <small class="text-muted">« En maintenance » et « Immobilisé » sont aussi positionnés automatiquement par les entretiens en cours et les incidents immobilisants.</small>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <button type="submit" class="btn btn-success"><i data-feather="check" class="mr-50"></i> Valider</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>
