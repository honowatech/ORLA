@extends('admin/templates/template')
@section('title')
    {{'Livraisons réussies'}}
@endsection
@section('css')
<style type="text/css">
    .spinner{
        margin: 2rem;
        width: 4rem;
        height: 4rem;
    }
</style>
@endsection
@section('contenu')
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Liste des commandes</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                                    </li>
                                    <li class="breadcrumb-item active"> Liste des commandes
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card pl-1 pr-1">
                <div class="content-body"> 
                    <div class="row pt-1" >
                        <div class="col-lg-2 col-sm-5 col-12 ">
                            <div class="form-group">
                                <select class="form-control select2 id_type_client" onchange="give_client()">
                                    @foreach($types_client as $type_client)
                                    <option value="{{$type_client->id}}">
                                        {{$type_client->libelle}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-7 col-12">
                            <div class="form-group">
                                <select class="form-control select2 id_client" id="id_client" onchange="give_results()">
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-12 mx-auto">
                            <div class="form-group">
                                <input type="text" id="date" class="form-control flatpickr-human-friendly input" onchange="give_results()" name="date">
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="card px-2  py-2" id="ajax_result">
            </div>                         
        </div>
    </div>
</div>
</div>
@endsection
@section('javascript')
	<script type="text/javascript">

        document.querySelector('#date').value = formatDate(new Date());
        give_client();
        give_results();
        function mettre(number_page){
           document.querySelector('#page').value = number_page;
           document.querySelector('#search').submit() 
        }
        function formatDate(date) {
            const day = date.getDate();
            const month = date.getMonth() + 1;
            const year = date.getFullYear();
            return `${year}-${month}-${day}`;
        }
		document.querySelector('#Details_commande').classList.add('active');
        function give_client(){
            var id_type = document.querySelector('.id_type_client').value,
            lien = '{{route('details_commande.create')}}';
            responses_ajax('id_client',[id_type],lien,'GET',true);
        }
        function give_results(){
            var id_client = document.querySelector('#id_client').value,
                date = document.querySelector('#date').value,
                lien = '{{route('details_commande.store')}}';
            responses_ajax('ajax_result',[id_client,date],lien,'POST');
        }
        function save_transaction(){
            var lien = '{{route('details_commande.update',0)}}';
            ajax_paie('to_spin',['id_client','date'],lien,'PATCH',true);
        }
        function save_transaction(){
            var lien = '{{route('details_commande.update',0)}}',
                montant = document.querySelector('#montant').value,
                date = document.querySelector('#date').value,
                id_client = document.querySelector('#id_client').value,
                qui_paie = document.querySelector('#qui_paie').value,
                modes_paiement = document.querySelectorAll('.mode_paiement'),
                mode_paiement = '',
                telephone2 = document.querySelector('#telephone2').value;
                modes_paiement.forEach((mode) => {
                    if (mode.checked ) {
                        mode_paiement = mode.value;
                    }
                });
                data = {
                        'montant' : montant,
                        'date' : date,
                        'id_client' : id_client,
                        'qui_paie' : qui_paie,
                        'mode_paiement' : mode_paiement,
                        'telephone2' : telephone2
                    }
            ajax_paie('to_spin',data,lien,'PATCH',true);
        }
        function ajax_paie(id,data,lien,method,encode){
            var result = document.querySelector('#'+id),
                close_modal = document.querySelector('#close_modal'),
                boutton_paiement = document.querySelector('#boutton_paiement');
                document.querySelector('#tospin').innerHTML = 'Paiement ncours...';
                boutton_paiement.hidden=true;
            jQuery.ajax({
                url: lien,
                type : method,
                data : { data : data , '_token' : "{{ csrf_token() }}" },
                success: function(response)
                {
                        // console.log(response);
                        document.querySelector('#tospin').innerHTML = response['message'];
                        if (response['statut']) {
                        document.querySelector('#error_paie').innerHTML = "<div class='w-100 text-center'>Un instant...</div>";
                        setTimeout(function(){
                            document.querySelector('#tospin').innerHTML = '';
                            give_results()
                            close_modal.click();
                            
                        },1500);
                        }else{
                            setTimeout(function(){
                                document.querySelector('#tospin').innerHTML = '';
                                boutton_paiement.hidden=false;
                                
                            },2500);
                        }
                },
                error: function(){
                            document.querySelector('#tospin').innerHTML = "<span class='text-danger text-center'> Erreur !</span>";
                            document.querySelector('#error_paie').innerHTML = "<div class='w-100 text-center'>Un problème est survenu veuillez atualiser la page !!! </div>";
                        setTimeout(function(){
                            document.querySelector('#tospin').innerHTML = '';
                            document.querySelector('#error_paie').innerHTML ='';
                                boutton_paiement.hidden=false;
                        },2500);
                }
            });

        }
	</script>
@endsection