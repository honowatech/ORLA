
@if(isset($all))
    <option>{{$ville == null ? 'Choisir une Ville puis un Quartier' : 'Choisir un Quartier dans '. $ville->libelle}}</option>
@endif
@foreach($quartiers as $quartier)
    <option value="{{$quartier->id}}" {{ $quartier->id == old('id_quartier_associe') ? 'selected' : ''}}>
        {{$ville->libelle.'-'.$quartier->libelle}}
    </option>
@endforeach