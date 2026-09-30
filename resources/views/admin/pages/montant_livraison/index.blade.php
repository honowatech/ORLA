@extends('admin/templates/template')

@section('title')
{{'Liste des Prix '}}
@endsection
@section('contenu')
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
            <div class="content-header row">
    	 <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Liste des Prix_livraison</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a>
                            </li>
                            <li class="breadcrumb-item active">Liste des Prix_livraison
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
            </div>
    <div class="card pl-1 pr-1">
            <div class="content-body">
                <div class="row justify-content-between bg-light-secondary" >
                    <div></div>
                        <form action="{{ route('montant_livraison.index') }}" class="col-lg-3 mt-1" id="search" method="GET">
                            @csrf
                            <div class="form-group">
                                <div class="input-group input-group-merge">
                                    <div class="input-group-prepend ">
                                        <span class="input-group-text"><i data-feather='search'></i></span>
                                    </div>
                                    <input type="search" class="form-control" name="recherche" value="{{ session()->get('recherche')}}" placeholder="Rechercher une zone ou un prix">
                                </div>
                            </div>
                            <input type="hidden" name="page" id="page" value="">
                        </form>
                    </div>
                </div>
                <!-- Table without card start -->
                <div class="row" id="table-without-card">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr class="text-center">
                                        <th>Zone depart</th>
                                        <th>Zone Arrivée</th>
                                        <th class=" col-4">Montant</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	@foreach($montant_livraisons as $montant_livraison)
	                                    <tr class="text-center">
	                                        <td>
	                                            {{$montant_livraison->zone_depart->libelle}}
                                            </td>
	                                        <td>
	                                        	{{$montant_livraison->zone_arrivee->libelle}}
	                                        </td>
	                                        <td>
                                                <div class="d-flex justify-content-center col-lg-10 col-xl-8 col-xxl-4 mx-auto ">
                                                    <span class="cursor-pointer my-auto" title="Double-cliquer pour modifier" ondblclick="edit('{{$montant_livraison->id}}')" id="montant_{{$montant_livraison->id}}">{{$montant_livraison->montant}}</span>
                                                    <input onblur="change('{{$montant_livraison->id}}')" onkeyup="touche_clavier('{{$montant_livraison->id}}')" id="input_{{$montant_livraison->id}}" value="{{$montant_livraison->montant}}" hidden type="number" style="max-width: 90px; min-width: 90px; height:30px;" class="form-control text-center" name="montant"> 
                                                    <div class="spinner-border text-dark" hidden role="status" id="spinner{{$montant_livraison->id}}" style="max-width: 30px; min-width: 30px; height:30px;"></div>
                                                    <span class="my-auto" style="margin-left: 0.5rem;">FCFA</span>
                                                </div>
                                            </td>
	                                    </tr>
	                                @endforeach                               
                                    </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="border-top mt-1">
                        <div class="d-flex col-md-10 mx-auto" style=" padding:1rem; overflow: auto; border-radius: 4px;">
                            <ul class="pagination"  style="margin-bottom:  0rem">
                            {{-- Previous Page Link --}}
                                @if ($montant_livraisons->onFirstPage())
                                    <li class="page-item" style="opacity: 0.6; cursor: no-drop;">
                                        <span disabled class="page-link">Précédent</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" onclick="mettre({{ $montant_livraisons->currentPage()-1 }})" rel="prev" aria-label="@lang('pagination.previous')">Précédent</a>
                                    </li>
                                @endif
                                @for($i=1;$i<=$montant_livraisons->lastPage();$i++)
                                    <li class="page-item @if($i==$montant_livraisons->currentPage()) active @endif" >
                                        <a class="page-link" onclick="@if($i!=$montant_livraisons->currentPage()) mettre({{$i}}) @endif" rel="prev" aria-label="@lang('pagination.previous')">{{$i}}</a>
                                    </li>
                                @endfor
                                {{-- Next Page Link --}}
                                @if ($montant_livraisons->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" onclick="mettre({{ $montant_livraisons->currentPage()+1 }})" rel="next" aria-label="@lang('pagination.next')">Suivant</a>
                                    </li>
                                @else
                                    <li class="page-item" style="opacity: 0.6; cursor: no-drop;">
                                        <span disabled class="page-link">Suivant</span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
</div>
</div>                         
</div>
</div>
</div>
</div>
<div id="button_footer" hidden>
    <form method="POST" class="destroy_form">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-gradient-success btn-success round btn-lg">Confirmer</button>
    </form>
<button type="button" class="btn btn-gradient-danger btn-danger round btn-lg" data-dismiss="modal">Annuler</button>
</div>

<div class="modal fade modal-danger text-left" id="message-success" tabindex="-1" role="dialog" aria-labelledby="message-success" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div></div>
                <h3 class="modal-title text-dark" id="myModalLabel120"> Information </h3>
                <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close" autofocus>
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <div class="modal-body">
            <p class="text-center" id="success_message">
            </p>
        </div>
        <div class="modal-footer" style="justify-content: center;">
            <button type="button" class="btn btn-gradient-info btn-info round waves-effect waves-float waves-light" data-dismiss="modal">Terminer</button>
        </div>
        </div>
    </div>
</div>
<button type="button" class="display btn btn-info waves-effect waves-float waves-light" href="#" data-target="#message-success" data-toggle="modal" hidden id="success"></button>

@endsection
@section('javascript')
	<script type="text/javascript">
        function edit(id_table){
          var span = document.querySelector('#montant_'+id_table),
           input = document.querySelector('#input_'+id_table);
           span.hidden = true; 
           input.hidden = false;
           input.value = span.innerHTML;
           input.focus();
        }
        function touche_clavier(id_table) {
          // Récupérer la touche du clavier entrée
          const keyCode = event.keyCode;

          // Vérifier si la touche entrée a été relâchée
          if (keyCode === 13) {
            // Effectuer une action
            change(id_table)
          }
        }
        function change(id_table){
            var span = document.querySelector('#montant_'+id_table),
                success = document.getElementById('success'),
                success_message = document.getElementById('success_message'),
                input = document.querySelector('#input_'+id_table),
                spinner = document.querySelector('#spinner'+id_table),
                montant = input.value < 0 ? 0 : input.value ;
            success_message.innerHTML = 'Chargement du message...';
            span.hidden = true;
            input.hidden = true;
            spinner.hidden = false;
            jQuery.ajax({
                    url: "{{route('montant_livraison.store')}}",
                    type : 'POST',
                    data : { id_montant : id_table , montant : montant , '_token' : "{{ csrf_token() }}" },
                    success: function(response)
                    {
                        spinner.hidden = true;
                        span.hidden  = false;
                        success_message.innerHTML = response;
                        span.innerHTML = montant;
                    },
                    error: function(){
                        input.value = span.innerHTML;
                        success_message.innerHTML = '<h3 class="text-danger">Erreur</h3>! <br> Demande rejétée'; 
                    }
            }); 
                        success.click(); 
        }
        function mettre(number_page){
           document.querySelector('#page').value = number_page;
           document.querySelector('#search').submit() 
        }
		document.querySelector('#montant_livraisonAjouter')?.classList.add('active');
	</script>
@endsection