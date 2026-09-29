@extends('superadmin/layout/template_error')
@section('title')
{{'Abonnements disponibles'}}
@endsection
@section('content')

<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-12 col-12 m-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                            <h2 class="content-header-title mb-0"> S'abonner </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	    <div class="content-body m-2">
            <section id="multiple-column-form">
                <h3 class="text-center mb-2"> 
                    <i data-feather="shopping-cart" class="text-primary" style="height: 80px; width: 80px;"></i>
                </h3>
                    @if($abonnements->count() == 0)
                    <div class="row text-center">
                        <div class="col-lg-6 card p-2 col-md-8 col-sm-10 mx-auto mt-2">
                            <h4 class="text-center mb-3">
                                <i data-feather="alert-triangle" class="text-danger" style="height: 20px; width: 20px;"></i><br><br>
                                Pas d'abonnement disponible.
                            </h4>
                            <p>
                                Pour avoir des abonnements veuillez contacter  <a href="https://honowa.com/contact">Honowa Technologies </a> via :
                            </p>
                            <div class="text-center col-md-10 mx-auto">
                                <p>
                                    <i data-feather="phone" class="mr-50"></i> Téléphones : {{Sa_phone(explode('/',$phones->value)[0])}} / {{Sa_phone(explode('/',$phones->value)[1])}}.
                                </p>
                                <p>
                                    <i data-feather="mail" class="mr-50"></i> Email : {{$email->value}}.
                                </p>
                                <a class="btn btn-primary btn-gradient-primary" href="{{url()->current()}}">
                                    <i data-feather="loader" class="mr-50"></i>Actualiser
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                <div class="row">
                    @foreach($abonnements as $abonnement)
                        <div class="col-lg-4 col-sm-6 col-12 @if($abonnements->count() < 2) mx-auto @endif">
                            <div class="card">
                                <div class="card-header">
                                    <h2 class="mx-auto">
                                        <b>{{$abonnement->titre}}</b>
                                        @if($abonnement->id == $client->id)
                                            <smal class="text-muted" style="font-size: 10px;">
                                               (Préféré)
                                            </smal>
                                        @endif
                                    </h2>
                                </div>
                                <div class="card-body">
                                    <div>
                                        <div class="row justify-content-between mx-1 mb-2">
                                            <div>
                                                    <b> Périodicité :</b>
                                            </div>
                                            <div>
                                                <span class="text-truncate">
                                                    {{$abonnement->accumulateur == 1 ? '1 '.str_replace('(s)','',$types_periode[$abonnement->type_periode]) : $abonnement->accumulateur.' '.str_replace('(s)','s',$types_periode[$abonnement->type_periode])}}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row justify-content-between mx-1 my-2">
                                            <div>
                                                    <b> Montant :</b>
                                            </div>
                                            <div>
                                                <span class="text-truncate">
                                                    {{Sa_montant($abonnement->montant)}} XAF
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            @if($api == null)
                                                <span class="badge badge-danger">
                                                    <i data-feather="alert-triangle" class="mr-50"></i> Paiement indisponible
                                                </span>
                                            @else
                                            <form method="POST" action="{{route('Sa-transaction.store')}}">
                                                @csrf
                                                <input type="hidden" name="id_abonnement" value="{{$abonnement->id}}">
                                                <input type="hidden" name="methode" value="mobile">
                                                <button class="btn btn-success btn-gradient-success">
                                                    @if($abonnement->id == $client->id_abonnement)
                                                    <i data-feather='rotate-cw' class="mr-50"></i> Renouveller
                                                    @else
                                                    <i data-feather='shopping-cart' class="mr-50"></i> Souscrire
                                                    @endif
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if(check_superadmin() != 'true')
                    <div class="row col-md-8 col-lg-6 mx-auto" style="gap: 2rem;">
                        <div class="mx-auto">
                            <button type="submit" class="btn btn-outline-danger" onclick="remplir('{{route('logout')}}','', 'vous déconnecter' ,'button_deconnection')">
                                <i data-feather="power" class="mr-50"></i>Se Déconnecter
                            </button>
                        </div>
                    </div>
                @endif
            </section>
	   </div>
    </div>
</div>
@endsection

@section('javascript')
	<script type="text/javascript">
	</script>
@endsection