@foreach( $clients as $client )
    <a onclick='put_information(["phone_number","contact_colis","nom_client","id_client"],["{{$client->telephone}}","{{$client->telephone}}","{{$client->noms}} {{$client->Prenoms}}","{{$client->id}}"]);phone("phone_number");phone("contact_colis");disabled(["nom_client","fetelephone"]);choose()' class="num_client dropdown-item">
        <i class="mr-1 flag-icon flag-icon-cm"></i>+237
        <span class="ml-1">{{$client->telephone[0]}} {{$client->telephone[1].$client->telephone[2]}} {{$client->telephone[3].$client->telephone[4]}} {{$client->telephone[5].$client->telephone[6]}} {{$client->telephone[7].$client->telephone[8]}}</span>
         @if($client->type__client->libelle == 'Simple' )
         <b>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather ml-1 feather-at-sign"><circle cx="12" cy="12" r="4"></circle><path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"></path></svg>
        </b> {{$client->noms }}
         @else
         - {{$client->type__client->libelle }}
         @endif
    </a>
@endforeach