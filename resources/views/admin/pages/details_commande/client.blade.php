@if($clients->count() > 0)
    <option value=""> Choisir un client </option>
@else
    <option value=""> Aucun client </option>
@endif
@foreach($clients as $client)
    <option value="{{$client->id}}"> {{$client->noms}} {{$client->Prenoms}} </option>
@endforeach