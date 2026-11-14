@if($clients->count() > 0)
    <option value=""> {{ __('Choisir un client') }} </option>
@else
    <option value=""> {{ __('Aucun client') }} </option>
@endif
@foreach($clients as $client)
    <option value="{{$client->id}}"> {{$client->noms}} {{$client->Prenoms}} </option>
@endforeach