@if($abonnements->count() == 0)
<div class="col-12 p-2 font-weight-bolder text-center">Aucun résultat</div>
@else
                <div>
                    <div class="row" id="table-hover-animation">
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <tr class="text-center">
                                        <th>N°</th>
                                        <th>Libellé</th>
                                        <th>Période</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $count = 10*$abonnements->currentPage() -9 ;
                                        $table = ['secondary','danger','warning','info','primary','dark'];
                                    @endphp
                                    @foreach($abonnements as $abonnement)
                                        <tr class="text-center">
                                            <td>
                                                {{$count}}
                                                @php $count++ @endphp
                                            </td>
                                            <td>
                                                <span class="font-weight-bolder">
                                                    {{Sa_name($abonnement->titre)}}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="font-weight-bolder text-truncate">
                                                    {{$abonnement->accumulateur == 1 ? '1 '.str_replace('(s)','',$types_periode[$abonnement->type_periode]) : $abonnement->accumulateur.' '.str_replace('(s)','s',$types_periode[$abonnement->type_periode])}}
                                                    {{$abonnement->periode_grace == 0 || $abonnement->periode_grace == null ? 'Sans période de grace' : ''}}
                                                    {{$abonnement->periode_grace == 1 ? '+ 01 '.'Jour (Grace)' : ''}}
                                                    {{$abonnement->periode_grace > 1 && $abonnement->periode_grace < 10 ? '+ 0'.$abonnement->periode_grace.' Jours (Grace)' : ''}}
                                                    {{$abonnement->periode_grace >= 10 ? '+ '.$abonnement->periode_grace.' Jours (Grace)' : ''}}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="font-weight-bolder text-truncate">
                                                    {{Sa_montant($abonnement->montant)}} FCFA
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-pill badge-glow @if($abonnement->statut == 0) badge-danger @else badge-success @endif">
                                                    @if($abonnement->statut == 0)
                                                        Désactivé
                                                    @else
                                                        Actif
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                        <i data-feather="more-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        {{-- <a class="dropdown-item" href="{{route('Sa-abonnement.show',$abonnement->id)}}">
                                                            <i data-feather='eye'></i>
                                                            <span class="ml-1">Détails</span>
                                                        </a> --}}
                                                        <a class="dropdown-item" href="{{route('Sa-abonnement.edit',$abonnement->id)}}">
                                                            <i data-feather='edit-2'></i>
                                                            <span class="ml-1">Editer</span>
                                                        </a>
                                                        <span class=" @if($abonnement->statut == 1) text-danger @else text-success @endif dropdown-item" data-toggle="modal" onclick="remplir('{{route('Sa-abonnement.destroy',$abonnement->id)}}','{{Sa_name($abonnement->titre)}}',@if($abonnement->statut == 1) 'Désactiver' @else 'Activer' @endif ,'button_footer')" data-target="#danger">
                                                            @if($abonnement->statut == 1)
                                                                <i data-feather='x'></i>
                                                                <span class="ml-1 text-danger" type="button">
                                                                    Désactiver
                                                                </span>
                                                            @else
                                                                <i data-feather='check'></i>
                                                                <span class="ml-1 text-success" type="button">
                                                                    Activer
                                                                </span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
@if ($abonnements->hasPages())
    <nav class="d-flex col-12 mt-1 justify-items-center justify-content-center">
        <div class="row mx-auto justify-content-center flex-fill d-md-none">
            <div class="mr-1 d-md-none">
                <p class="small text-muted">
                    {!! __('De') !!}
                    <span class="fw-semibold">{{ $abonnements->firstItem() }}</span>
                    {!! __('à') !!}
                    <span class="fw-semibold">{{ $abonnements->lastItem() }}</span>
                    {!! __('sur') !!}
                    <span class="fw-semibold">{{ $abonnements->total() }}</span>
                    {!! __('resultats') !!}
                </p>
            </div>
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($abonnements->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">Précédent</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" onclick="mettre({{ $abonnements->currentPage()-1 }})" rel="prev">Suivant</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($abonnements->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" onclick="mettre({{ $abonnements->currentPage()+1 }})" rel="next">@lang('pagination.next')</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">@lang('pagination.next')</span>
                    </li>
                @endif
            </ul>
        </div>

        <div class="d-none flex-md-fill d-md-flex align-items-md-center justify-content-md-center">
            <div class="mr-1">
                <p class="small text-muted">
                    {!! __('vu de ') !!}
                    <span class="fw-semibold">{{ $abonnements->firstItem() }}</span>
                    {!! __('à') !!}
                    <span class="fw-semibold">{{ $abonnements->lastItem() }}</span>
                    {!! __('sur') !!}
                    <span class="fw-semibold">{{ $abonnements->total() }}</span>
                    {!! __('resultats') !!}
                </p>
            </div>

            <div>
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($abonnements->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span class="page-link" aria-hidden="true">&lsaquo;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" onclick="mettre({{ $abonnements->currentPage()-1 }})" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($abonnements->links()->elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $abonnements->currentPage())
                                    <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" onclick="mettre({{ $page }})">{{ $page }}</a></li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($abonnements->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" onclick="mettre({{ $abonnements->currentPage()-1 }})" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="page-link" aria-hidden="true">&rsaquo;</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif
    </div>
@endif