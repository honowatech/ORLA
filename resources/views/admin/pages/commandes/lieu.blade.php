@foreach( $quartiers as $quartier )
    <a onclick='put_information(["{{$id_quartier}}","{{$lieu}}"],["{{$quartier->id}}","{{$quartier->libelle}}"]);montant_commande()' class="num_client dropdown-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin mr-1"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg><span class="m-50">{{$quartier->libelle}}</span>
    </a>
@endforeach