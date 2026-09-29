<div class="mb-1"> 
    <div class="divider mt-1 mb-3">
        <div class="divider-text text-uppercase font-weight-bolder">
            {{ $produit->noms }}
        </div>
    </div>
    <h5 class="text-uppercase font-weight-bolder text-center">Description : </h5>  
            @if ( textBreack($produit->description, 60) === true )
        <p class=" text-center">{{$produit->description}}</p>  
            @else 
        <p>{{textBreack($produit->description, 60)}}</p>
            @endif
</div>
