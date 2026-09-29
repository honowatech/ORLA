<div class="content-body pb-1"> 
    <div class="row" id="table-without-card">
        <div class="table-responsive col-lg-12">
                            <table class="table">
                                <thead>
                                    <tr class="text-center">
                                        <th> @if($titre) Description @endif Produit</th>
                                        <th>Livraison</th>
                                        <th>Total à percevoir</th>
                                        <th>Frais de livraison</th>
                                        <th>A reverser au client</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($commandes as $commande)
                                        <tr class="text-center">
                                            <td style="overflow-x: auto; max-width: 250px;">
                                                <span style="white-space: nowrap;">
                                                @php
                                                    $row = 0;
                                                    $img = [
                                                        'mobilemoney' => 'app-assets/images/logo/mtn.svg',
                                                        'orangemoney' => 'app-assets/images/logo/orange.png'
                                                    ];
                                                @endphp
                                                @foreach($commande->details_commande as $detail)
                                                    @php
                                                        $row ++;
                                                    @endphp
                                                    {{$detail->produit->noms}}
                                                    @if($row != $commande->details_commande->count())
                                                        ;
                                                    @endif 
                                                @endforeach 
                                                @if($titre)
                                                    {{$commande->description}}
                                                @endif
                                                </span>
                                            </td>
                                            <td>
                                                <span>
                                                    {{explode('*/*',$commande->adresse_livraison)[0]}} || {{$commande->quartier_livraison->libelle}}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="font-weight-bolder">
                                                    @if($commande->montant_recuperer == null)
                                                        0
                                                    @else
                                                        {{$commande->montant_recuperer}}
                                                    @endif
                                                </span> FCFA
                                            </td>
                                            <td>
                                                <span class="font-weight-bolder">
                                                    @if($commande->montant_livraison == null)
                                                        0
                                                    @else
                                                        {{$commande->montant_livraison}}
                                                    @endif
                                                </span> FCFA
                                            </td>
                                            <td>
                                                <span>
                                                    @if($commande->montant_recuperer - $commande->montant_livraison > 0)
                                                        <span class="text-success">
                                                            <span class="font-weight-bolder">
                                                                {{$commande->montant_recuperer - $commande->montant_livraison}}
                                                            </span>
                                                    @else
                                                        <span class="text-danger">
                                                            <span class="font-weight-bolder">
                                                                {{$commande->montant_recuperer - $commande->montant_livraison}}
                                                            </span>
                                                    @endif FCFA
                                                        </span>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach                               
                                </tbody>
                            </table>
                        </div>
        </div>
    </div>
    <hr class="p-0 m-0">
    <div class="pt-1 d-flex justify-content-end">
        <div class="px-2 my-auto">
            <div style="font-size: 1rem;">
                @if($argent_du > 0 )
                    Speedex doit 
                    <span class="font-weight-bolder">
                        {{$argent_du}}
                    </span>
                    FCFA
                @elseif($argent_du < 0)
                    <span class="d-none d-md-inline-block"> 
                        {{ucfirst($client->noms)}} {{ucfirst($client->Prenoms)}} Vous doit 
                    </span> 
                    <span class="font-weight-bolder">
                        {{(-1)*$argent_du}}
                    </span>
                    FCFA
                @elseif($argent_du == 0)
                    <span> 
                        C'est
                    </span>
                    <span class="font-weight-bolder text-success"> Ok
                    </span>!
                @endif
            </div>
        </div>
        @if($argent_du != 0)
                <button class="btn btn-success btn-gradient-success my-auto" @if( $argent_du > 0 ) onfocus="phone('telephone'); @endif " data-toggle="modal" data-target="#paiement" title="Cliquer pour régler la facture">
                    <span>Régler</span>
                </button>
        @endif
        @if($paiements->count() > 0)
            <a data-toggle="modal" data-target="#tarnsactions" class="btn btn-icon rounded-circle btn-info waves-effect my-auto ml-2" title="Voir les paiements"> 
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign text-white"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            </a>
        @endif
    </div>               
</div>

@if($argent_du != 0)
    <div class="modal fade" id="paiement" tabindex="-1" aria-labelledby="paiementTitle" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paiementTitle"> Régler la facture </h5>
                    <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-10 py-2 mx-auto" id="info_paiement">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="montant" id="montant" value="{{$argent_du > 0 ? $argent_du : (-1)*$argent_du}}">
                                <input type="hidden" name="date" id="date" value="{{$date}}">
                                <input type="hidden" name="id_client" id="id_client" value="{{$client->id}}">
                                @if( $argent_du > 0 )
                                    <input type="hidden" name="qui_paie" id="qui_paie" value="speedex">
                                @elseif( $argent_du < 0 )
                                    <input type="hidden" name="qui_paie" id="qui_paie" value="client">
                                @endif
                                <div class="row">
                                    <span class="text-center col-12 mb-1">
                                        Choisir un mode de paiement
                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                    </span>
                                    <div class="col-12 mb-1">
                                        <div class="form-group d-flex">
                                            @foreach($img as $index => $src)
                                            <div class="col-3 mx-auto">
                                                <div class="form-group">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="{{$index}}" name="mode_paiement" class="mode_paiement m-auto custom-control-input" @if($index=='mobilemoney') checked @endif value="{{$index}}">
                                                        <label class="custom-control-label" for="{{$index}}">
                                                            <img src="{{asset($src)}}" alt="avatar" style="position: absolute; bottom: -50%; top: -50%; height: 40px; width: 50px;">
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <span class="text-center col-12 mb-1">
                                        Numéro de téléphone
                                        <span class="text-danger cursor-pointer" title="Obligatoire">*</span>
                                    </span>
                                    <div class="col-12 mb-1">
                                        <div class="form-group">
                                            @if( $argent_du > 0 )
                                            <div class="input-group input-group-merge @error('telephone')  is-invalid @enderror">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="mr-1 flag-icon flag-icon-cm"></i>+237</span>
                                                </div>
                                                <input type="text" class=" @error('telephone')  is-invalid @enderror form-control" placeholder="6 12 34 56 78" id="telephone" onkeyup="phone('telephone');" name="telephone" value="{{old('telephone') == null ? $client->telephone : old('telephone')}}" required/>
                                            </div>
                                                <input type="hidden" id="telephone2" name="telephone" />
                                            @elseif( $argent_du < 0 )
                                                <input type="text" class="form-control text-center" placeholder="Speedex" id="telephone" name="telephone" value="Speedex" required readonly />
                                                <input type="hidden" id="telephone2" name="telephone" value="Speedex"/>
                                            @endif
                                            @error('telephone') 
                                                <small class="alert alert-danger"> {{$message}} </small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mt-2 d-md-flex col-12">
                                        <div class="col-md-6 col-12 mx-auto">
                                            <div class="form-group">
                                                <button type="reset" class="btn btn-danger col-12" data-dismiss="modal" aria-label="Close" id="close_modal">
                                                    Annuler
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12 mx-auto">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-success col-12" onclick="save_transaction()"  id="boutton_paiement">
                                                    Payer
                                                </button>
                                                <div class="text-center p-50" id="tospin">
                                                    tospin
                                                </div>
                                            </div>
                                        </div>
                                    <div>                              
                                </div> 
                        </div>
                        <div class="mx-auto" id="error_paie">
                        <div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if($paiements->count() > 0)
    <div class="modal fade" id="tarnsactions" tabindex="-1" aria-labelledby="tarnsactionsTitle" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tarnsactionsTitle"> {{ucfirst($client->noms)}} {{ucfirst($client->Prenoms)}} (Transactions)</h5>
                    <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body pt-5">
                    <div class="row">
                        <div class="col-10 mx-auto" id="info_tarnsactions">
                            <h4 class="text-center col-12 mb-1">Transactions du {{date('d-M-Y',strtoTime($date))}}</h4>
                            <hr>
                            <div class="row" id="table-without-card">
                                <div class="table-responsive col-lg-12">
                                    <table class="table">
                                        <thead>
                                            <tr class="text-center">
                                                <th>Type</th>
                                                <th>Montant</th>
                                                <th>mode paiement</th>
                                                <th>Contact Recepteur</th>
                                                <th>Agent</th>
                                                <th>Client</th>
                                            </tr>
                                        </thead>
                                        @php
                                            $type = [
                                                    'client' => 'Entrée',
                                                    'speedex' => 'Sortie'
                                                ];
                                        @endphp
                                        <tbody>
                                            @foreach($paiements as $paiement)
                                                <tr class="text-center">
                                                    <td>
                                                        <span>
                                                            {{$type[$paiement->qui_paie]}}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span>
                                                            {{$paiement->montant}} FCFA
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="avatar">
                                                            <img src="{{asset($img[$paiement->mode_paiement])}}" alt="avatar" style="height: 40px; width: 50px;">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span>
                                                            {{$paiement->telephone}}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span>
                                                            {{ucfirst($paiement->saver->noms)}}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span>
                                                            {{ucfirst($paiement->client->noms)}} {{ucfirst($paiement->client->Prenoms)}}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach                               
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
@endif
</div>