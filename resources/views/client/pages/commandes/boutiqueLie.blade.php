
<option class="boutiques" data-phone="" data-lieu="" value="">{{ $client == null ? 'Choisir un client puis une Boutique' : 'Choisir une de vos Boutiques' }}</option>
@foreach( $boutiques as $boutique )
    <option value="{{$boutique->id}}" class="boutiques" data-id="{{$boutique->quartier_boutique->id}}" data-lieu="{{$boutique->id_quartier != null ? $boutique->quartier_boutique->libelle : 'Speedex'}}" data-description="{{$boutique->quartier}}"  {{ $boutique->id == session('id_boutique') ? 'selected' : '' }}>
        {{$boutique->libelle}} - {{$boutique->id_quartier != null ? $boutique->quartier_boutique->libelle :'Speedex'}}
    </option>
@endforeach
@php
    session()->forget('id_boutique');
@endphp