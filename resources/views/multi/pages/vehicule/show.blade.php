@extends(Dossier(auth()->user()->type_utilisateur->libelle).'/templates/template')

@section('title')
{{'Véhicule '}} {{$vehicule->immatriculation}}
@endsection
@section('css')
<style>
    .parc-kpi .card-body { padding: 1rem; }
    .parc-kpi h3 { margin-bottom: 0; }
    .nav-tabs .nav-link .badge { margin-left: .35rem; }
    .parc-fiche dt { font-weight: 500; color: #6e6b7b; }
    .parc-fiche dd { font-weight: 600; }
</style>
@endsection
@section('contenu')
@php
    $onglet = request('onglet', 'documents');
    $onglets_valides = ['documents','entretiens','carburant','incidents','kilometrage','attributions'];
    if (!in_array($onglet, $onglets_valides)) { $onglet = 'documents'; }
    $retour = url()->current();
@endphp
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-12 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Fiche véhicule</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('home')}}">Acceuil</a></li>
                                <li class="breadcrumb-item"><a href="{{route('parc.index')}}">Parc automobile</a></li>
                                <li class="breadcrumb-item"><a href="{{route('vehicule.index')}}">Véhicules</a></li>
                                <li class="breadcrumb-item active">{{$vehicule->immatriculation}}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="row">
                {{-- Identité du véhicule --}}
                <div class="col-xl-4 col-lg-5 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start flex-wrap">
                                <div>
                                    <h3 class="font-weight-bolder mb-25">{{$vehicule->immatriculation}}</h3>
                                    <p class="mb-50 text-muted">{{$vehicule->marque}} {{$vehicule->modele}} @if($vehicule->annee) ({{$vehicule->annee}}) @endif</p>
                                </div>
                                <div class="avatar bg-light-primary p-50 m-0"><div class="avatar-content"><i data-feather="truck" class="font-medium-5"></i></div></div>
                            </div>
                            <div class="mb-1">
                                <span class="badge badge-glow {{$vehicule->etat_badge}}">{{$vehicule->etat_libelle}}</span>
                                <span class="badge badge-glow @if($vehicule->statut == 0) badge-danger @else badge-success @endif">@if($vehicule->statut == 0) Désactivé @else Actif @endif</span>
                                @if($vehicule->type)<span class="badge badge-light-secondary">{{$vehicule->type->libelle}}</span>@endif
                            </div>
                            <dl class="row parc-fiche mb-0">
                                <dt class="col-5">Kilométrage</dt><dd class="col-7">{{formatMontant($vehicule->kilometrage)}} km</dd>
                                <dt class="col-5">Coursier</dt>
                                <dd class="col-7">
                                    @if($vehicule->coursier)
                                        <a href="{{route('coursiers.show',$vehicule->coursier->id)}}">{{$vehicule->coursier->noms}} {{$vehicule->coursier->prenoms}}</a>
                                    @else
                                        <span class="text-muted">Non attribué</span>
                                    @endif
                                </dd>
                                <dt class="col-5">Ville</dt><dd class="col-7">{{$vehicule->ville ? $vehicule->ville->libelle : '-'}}</dd>
                                <dt class="col-5">Carburant</dt><dd class="col-7">{{$vehicule->carburant_libelle ?: '-'}} @if($vehicule->capacite_reservoir) · {{$vehicule->capacite_reservoir}} L @endif</dd>
                                <dt class="col-5">Couleur</dt><dd class="col-7">{{$vehicule->couleur ?: '-'}}</dd>
                                <dt class="col-5">N° châssis</dt><dd class="col-7 text-break">{{$vehicule->numero_chassis ?: '-'}}</dd>
                                <dt class="col-5">Acquisition</dt>
                                <dd class="col-7">
                                    {{$vehicule->date_acquisition ? Ladate($vehicule->date_acquisition) : '-'}}
                                    @if($vehicule->prix_acquisition)<br><small>{{formatMontant($vehicule->prix_acquisition)}} FCFA</small>@endif
                                </dd>
                                @if($vehicule->description)
                                <dt class="col-5">Description</dt><dd class="col-7">{{$vehicule->description}}</dd>
                                @endif
                            </dl>
                            <hr>
                            <div class="d-flex flex-wrap" style="gap:.5rem">
                                <a href="{{route('vehicule.edit',$vehicule->id)}}" class="btn btn-sm btn-outline-info"><i data-feather="edit-2" class="mr-25"></i> Éditer</a>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#modal_etat"><i data-feather="toggle-right" class="mr-25"></i> État</button>
                                <button type="button" class="btn btn-sm btn-outline-dark" data-toggle="modal" data-target="#modal_kilometrage"><i data-feather="activity" class="mr-25"></i> Relevé km</button>
                                @if($vehicule->coursier)
                                    <button type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#modal_restitution"><i data-feather="user-x" class="mr-25"></i> Désattribuer</button>
                                @endif
                                @if($vehicule->statut == 1 && $vehicule->etat != 'hors_service')
                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal_attribution"><i data-feather="user-check" class="mr-25"></i> {{$vehicule->coursier ? 'Réattribuer' : 'Attribuer'}}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Indicateurs --}}
                <div class="col-xl-8 col-lg-7 col-md-12">
                    <div class="row match-height parc-kpi">
                        <div class="col-md-4 col-sm-6">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="avatar bg-light-primary p-50 mb-50"><div class="avatar-content"><i data-feather="dollar-sign" class="font-medium-4"></i></div></div>
                                    <h3 class="font-weight-bolder">{{formatMontant($indicateurs['cout_total'])}}</h3>
                                    <p class="card-text mb-0">Coût total (FCFA)</p>
                                    <small class="text-muted">Ce mois : {{formatMontant($indicateurs['cout_mois'])}}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="avatar bg-light-warning p-50 mb-50"><div class="avatar-content"><i data-feather="droplet" class="font-medium-4"></i></div></div>
                                    <h3 class="font-weight-bolder">{{$indicateurs['consommation'] !== null ? $indicateurs['consommation'].' L' : '-'}}</h3>
                                    <p class="card-text mb-0">Consommation /100 km</p>
                                    <small class="text-muted">{{formatMontant($indicateurs['litres_total'])}} L · {{formatMontant($indicateurs['cout_carburant'])}} FCFA</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="avatar bg-light-info p-50 mb-50"><div class="avatar-content"><i data-feather="trending-up" class="font-medium-4"></i></div></div>
                                    <h3 class="font-weight-bolder">{{$indicateurs['cout_km'] !== null ? $indicateurs['cout_km'] : '-'}}</h3>
                                    <p class="card-text mb-0">Coût au km (FCFA)</p>
                                    <small class="text-muted">Entretien : {{formatMontant($indicateurs['cout_maintenance'])}} FCFA</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="avatar {{$indicateurs['documents_echeance'] > 0 ? 'bg-light-danger' : 'bg-light-success'}} p-50 mb-50"><div class="avatar-content"><i data-feather="file-text" class="font-medium-4"></i></div></div>
                                    <h3 class="font-weight-bolder">{{$indicateurs['documents_echeance']}}</h3>
                                    <p class="card-text mb-0">Document(s) à renouveler</p>
                                    <small class="text-muted">{{$documents->count()}} document(s) au total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="avatar {{$indicateurs['maintenances_dues'] > 0 ? 'bg-light-warning' : 'bg-light-success'}} p-50 mb-50"><div class="avatar-content"><i data-feather="tool" class="font-medium-4"></i></div></div>
                                    <h3 class="font-weight-bolder">{{$indicateurs['maintenances_dues']}}</h3>
                                    <p class="card-text mb-0">Entretien(s) à faire</p>
                                    <small class="text-muted">{{$maintenances->where('statut','realise')->count()}} réalisé(s)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="avatar {{$indicateurs['incidents_ouverts'] > 0 ? 'bg-light-danger' : 'bg-light-success'}} p-50 mb-50"><div class="avatar-content"><i data-feather="alert-triangle" class="font-medium-4"></i></div></div>
                                    <h3 class="font-weight-bolder">{{$indicateurs['incidents_ouverts']}}</h3>
                                    <p class="card-text mb-0">Incident(s) ouvert(s)</p>
                                    <small class="text-muted">{{$incidents->count()}} au total · {{formatMontant($indicateurs['cout_incidents'])}} FCFA</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($alertes->count() > 0)
                    <div class="card">
                        <div class="card-header pb-50"><h4 class="card-title"><i data-feather="bell" class="text-warning mr-50"></i> Alertes ({{$alertes->count()}})</h4></div>
                        <div class="card-body pt-0">
                            <ul class="list-unstyled mb-0">
                                @foreach($alertes as $alerte)
                                <li class="d-flex align-items-center py-50 border-bottom">
                                    <span class="badge badge-light-{{$alerte['niveau']}} mr-1"><i data-feather="{{$alerte['icone']}}"></i></span>
                                    <div class="flex-grow-1">
                                        <a href="{{route('vehicule.show',[$vehicule->id,'onglet'=>$alerte['onglet']])}}" class="font-weight-bolder text-body">{{$alerte['titre']}}</a>
                                        <small class="d-block text-muted">{{$alerte['message']}}</small>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Onglets --}}
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link {{$onglet=='documents'?'active':''}}" data-toggle="tab" href="#onglet_documents" role="tab"><i data-feather="file-text"></i> Documents <span class="badge badge-pill badge-light-primary">{{$documents->count()}}</span></a></li>
                        <li class="nav-item"><a class="nav-link {{$onglet=='entretiens'?'active':''}}" data-toggle="tab" href="#onglet_entretiens" role="tab"><i data-feather="tool"></i> Entretiens <span class="badge badge-pill badge-light-primary">{{$maintenances->count()}}</span></a></li>
                        <li class="nav-item"><a class="nav-link {{$onglet=='carburant'?'active':''}}" data-toggle="tab" href="#onglet_carburant" role="tab"><i data-feather="droplet"></i> Carburant <span class="badge badge-pill badge-light-primary">{{$carburants->count()}}</span></a></li>
                        <li class="nav-item"><a class="nav-link {{$onglet=='incidents'?'active':''}}" data-toggle="tab" href="#onglet_incidents" role="tab"><i data-feather="alert-triangle"></i> Incidents <span class="badge badge-pill badge-light-primary">{{$incidents->count()}}</span></a></li>
                        <li class="nav-item"><a class="nav-link {{$onglet=='kilometrage'?'active':''}}" data-toggle="tab" href="#onglet_kilometrage" role="tab"><i data-feather="activity"></i> Kilométrage <span class="badge badge-pill badge-light-primary">{{$releves->count()}}</span></a></li>
                        <li class="nav-item"><a class="nav-link {{$onglet=='attributions'?'active':''}}" data-toggle="tab" href="#onglet_attributions" role="tab"><i data-feather="users"></i> Attributions <span class="badge badge-pill badge-light-primary">{{$attributions->count()}}</span></a></li>
                    </ul>
                    <div class="tab-content pt-1">

                        {{-- DOCUMENTS --}}
                        <div class="tab-pane {{$onglet=='documents'?'active':''}}" id="onglet_documents" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h5 class="mb-0">Documents réglementaires</h5>
                                <button type="button" class="btn btn-sm btn-primary" data-modal="#modal_document" data-action="{{route('vehicule_document.store')}}" data-method="POST" data-titre="Nouveau document" onclick="parcOuvrir(this)"><i data-feather="plus"></i> Ajouter</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead><tr><th>Type</th><th>Libellé / N°</th><th>Fournisseur</th><th>Validité</th><th>Montant</th><th>État</th><th class="text-center">Actions</th></tr></thead>
                                    <tbody>
                                    @forelse($documents as $doc)
                                        <tr>
                                            <td><span class="font-weight-bolder">{{$doc->type_libelle}}</span></td>
                                            <td>{{$doc->libelle}}@if($doc->numero)<br><small class="text-muted">{{$doc->numero}}</small>@endif</td>
                                            <td>{{$doc->fournisseur ?: '-'}}</td>
                                            <td>
                                                <small>{{$doc->date_debut ? Ladate($doc->date_debut) : '…'}} <i data-feather="arrow-right" style="width:12px"></i> {{$doc->date_expiration ? Ladate($doc->date_expiration) : 'sans échéance'}}</small>
                                            </td>
                                            <td>{{$doc->montant ? formatMontant($doc->montant).' FCFA' : '-'}}</td>
                                            <td>
                                                <span class="badge badge-glow {{$doc->etat_badge}}">{{$doc->etat_libelle}}</span>
                                                @if($doc->jours_restants !== null)<br><small class="text-muted">{{$doc->jours_restants < 0 ? 'depuis '.abs($doc->jours_restants).' j' : 'dans '.$doc->jours_restants.' j'}}</small>@endif
                                            </td>
                                            <td class="text-center text-nowrap">
                                                @if($doc->fichier)<a href="{{route('vehicule_document.telecharger',$doc->id)}}" class="btn btn-sm btn-icon btn-flat-dark" title="Télécharger"><i data-feather="download"></i></a>@endif
                                                <button type="button" class="btn btn-sm btn-icon btn-flat-info" title="Modifier" data-modal="#modal_document" data-action="{{route('vehicule_document.update',$doc->id)}}" data-method="PUT" data-titre="Modifier le document" data-json='{{ json_encode($doc->only(['type','libelle','numero','fournisseur','date_debut','date_expiration','montant','description'])) }}' onclick="parcOuvrir(this)"><i data-feather="edit-2"></i></button>
                                                <button type="button" class="btn btn-sm btn-icon btn-flat-danger" title="Supprimer" onclick="remplir('{{route('vehicule_document.destroy',$doc->id)}}','{{$doc->libelle}}',' supprimer ce document ','button_delete_parc')"><i data-feather="trash-2"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted py-2">Aucun document enregistré. Ajoutez l'assurance, la visite technique, la carte grise…</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- ENTRETIENS --}}
                        <div class="tab-pane {{$onglet=='entretiens'?'active':''}}" id="onglet_entretiens" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h5 class="mb-0">Entretiens et réparations</h5>
                                <button type="button" class="btn btn-sm btn-primary" data-modal="#modal_maintenance" data-action="{{route('vehicule_maintenance.store')}}" data-method="POST" data-titre="Nouvel entretien" data-json='{{ json_encode(['kilometrage' => $vehicule->kilometrage]) }}' onclick="parcOuvrir(this)"><i data-feather="plus"></i> Ajouter</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead><tr><th>Entretien</th><th>Prévu</th><th>Réalisé</th><th>Km</th><th>Coût</th><th>Prestataire</th><th>Prochaine échéance</th><th>Statut</th><th class="text-center">Actions</th></tr></thead>
                                    <tbody>
                                    @forelse($maintenances as $m)
                                        <tr class="{{$m->en_retard ? 'table-danger' : ''}}">
                                            <td><span class="font-weight-bolder">{{$m->libelle}}</span><br><small class="text-muted">{{$m->type_libelle}}</small></td>
                                            <td>{{$m->date_prevue ? Ladate($m->date_prevue) : '-'}}</td>
                                            <td>{{$m->date_realisation ? Ladate($m->date_realisation) : '-'}}</td>
                                            <td>{{$m->kilometrage ? formatMontant($m->kilometrage) : '-'}}</td>
                                            <td>{{$m->cout ? formatMontant($m->cout).' FCFA' : '-'}}</td>
                                            <td>{{$m->prestataire ?: '-'}}</td>
                                            <td><small>@if($m->prochaine_date){{Ladate($m->prochaine_date)}}@endif @if($m->prochain_kilometrage)<br>{{formatMontant($m->prochain_kilometrage)}} km @endif @if(!$m->prochaine_date && !$m->prochain_kilometrage)-@endif</small></td>
                                            <td><span class="badge badge-glow {{$m->statut_badge}}">{{$m->statut_libelle}}</span>@if($m->en_retard)<br><small class="text-danger">en retard</small>@endif</td>
                                            <td class="text-center text-nowrap">
                                                @if(in_array($m->statut,['planifie','en_cours']))
                                                <button type="button" class="btn btn-sm btn-icon btn-flat-success" title="Marquer réalisé" data-modal="#modal_maintenance" data-action="{{route('vehicule_maintenance.update',$m->id)}}" data-method="PUT" data-titre="Clôturer l'entretien" data-json='{{ json_encode(array_merge($m->only(['type','libelle','description','date_prevue','kilometrage','cout','prestataire','prochaine_date','prochain_kilometrage']), ['statut' => 'realise', 'date_realisation' => date('Y-m-d'), 'kilometrage' => $m->kilometrage ?: $vehicule->kilometrage, 'planifier_suivant' => 1])) }}' onclick="parcOuvrir(this)"><i data-feather="check-circle"></i></button>
                                                @endif
                                                <button type="button" class="btn btn-sm btn-icon btn-flat-info" title="Modifier" data-modal="#modal_maintenance" data-action="{{route('vehicule_maintenance.update',$m->id)}}" data-method="PUT" data-titre="Modifier l'entretien" data-json='{{ json_encode($m->only(['type','libelle','description','statut','date_prevue','date_realisation','kilometrage','cout','prestataire','prochaine_date','prochain_kilometrage'])) }}' onclick="parcOuvrir(this)"><i data-feather="edit-2"></i></button>
                                                <button type="button" class="btn btn-sm btn-icon btn-flat-danger" title="Supprimer" onclick="remplir('{{route('vehicule_maintenance.destroy',$m->id)}}','{{$m->libelle}}',' supprimer cet entretien ','button_delete_parc')"><i data-feather="trash-2"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center text-muted py-2">Aucun entretien enregistré. Planifiez la prochaine vidange ou révision.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- CARBURANT --}}
                        <div class="tab-pane {{$onglet=='carburant'?'active':''}}" id="onglet_carburant" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h5 class="mb-0">Journal de carburant</h5>
                                <button type="button" class="btn btn-sm btn-primary" data-modal="#modal_carburant" data-action="{{route('vehicule_carburant.store')}}" data-method="POST" data-titre="Nouveau plein" data-json='{{ json_encode(['date' => date('Y-m-d'), 'plein_complet' => 1]) }}' onclick="parcOuvrir(this)"><i data-feather="plus"></i> Ajouter</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead><tr><th>Date</th><th>Litres</th><th>Montant</th><th>Prix / L</th><th>Km</th><th>Station</th><th>Coursier</th><th>Plein</th><th class="text-center">Actions</th></tr></thead>
                                    <tbody>
                                    @forelse($carburants as $plein)
                                        <tr>
                                            <td>{{Ladate($plein->date)}}</td>
                                            <td class="font-weight-bolder">{{$plein->litres}} L</td>
                                            <td>{{formatMontant($plein->montant)}} FCFA</td>
                                            <td>{{$plein->prix_litre ?: '-'}}</td>
                                            <td>{{$plein->kilometrage ? formatMontant($plein->kilometrage) : '-'}}</td>
                                            <td>{{$plein->station ?: '-'}}</td>
                                            <td>{{$plein->coursier ? $plein->coursier->noms.' '.$plein->coursier->prenoms : '-'}}</td>
                                            <td>@if($plein->plein_complet)<span class="badge badge-light-success">Complet</span>@else<span class="badge badge-light-secondary">Partiel</span>@endif</td>
                                            <td class="text-center text-nowrap">
                                                <button type="button" class="btn btn-sm btn-icon btn-flat-info" title="Modifier" data-modal="#modal_carburant" data-action="{{route('vehicule_carburant.update',$plein->id)}}" data-method="PUT" data-titre="Modifier le plein" data-json='{{ json_encode($plein->only(['id_coursier','date','litres','montant','kilometrage','plein_complet','station','description'])) }}' onclick="parcOuvrir(this)"><i data-feather="edit-2"></i></button>
                                                <button type="button" class="btn btn-sm btn-icon btn-flat-danger" title="Supprimer" onclick="remplir('{{route('vehicule_carburant.destroy',$plein->id)}}','{{$plein->litres}} L du {{Ladate($plein->date)}}',' supprimer ce plein ','button_delete_parc')"><i data-feather="trash-2"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center text-muted py-2">Aucun plein enregistré. Renseignez le kilométrage à chaque plein pour obtenir la consommation moyenne.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- INCIDENTS --}}
                        <div class="tab-pane {{$onglet=='incidents'?'active':''}}" id="onglet_incidents" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h5 class="mb-0">Incidents, pannes et sinistres</h5>
                                <button type="button" class="btn btn-sm btn-primary" data-modal="#modal_incident" data-action="{{route('vehicule_incident.store')}}" data-method="POST" data-titre="Nouvel incident" data-json='{{ json_encode(['date' => date('Y-m-d\TH:i'), 'statut' => 'ouvert']) }}' onclick="parcOuvrir(this)"><i data-feather="plus"></i> Déclarer</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead><tr><th>Date</th><th>Type</th><th>Description</th><th>Lieu</th><th>Coursier</th><th>Coût</th><th>Statut</th><th class="text-center">Actions</th></tr></thead>
                                    <tbody>
                                    @forelse($incidents as $inc)
                                        <tr>
                                            <td class="text-nowrap">{{Ladate($inc->date)}}<br><small class="text-muted">{{Heure($inc->date)}}</small></td>
                                            <td><span class="font-weight-bolder">{{$inc->type_libelle}}</span>@if($inc->immobilise)<br><span class="badge badge-light-danger">Immobilisé</span>@endif</td>
                                            <td style="max-width:260px">{{cutText($inc->description, 120)}}</td>
                                            <td>{{$inc->lieu ?: '-'}}</td>
                                            <td>{{$inc->coursier ? $inc->coursier->noms.' '.$inc->coursier->prenoms : '-'}}</td>
                                            <td>{{$inc->cout ? formatMontant($inc->cout).' FCFA' : '-'}}</td>
                                            <td><span class="badge badge-glow {{$inc->statut_badge}}">{{$inc->statut_libelle}}</span>@if($inc->date_resolution)<br><small class="text-muted">le {{Ladate($inc->date_resolution)}}</small>@endif</td>
                                            <td class="text-center text-nowrap">
                                                <button type="button" class="btn btn-sm btn-icon btn-flat-info" title="Modifier" data-modal="#modal_incident" data-action="{{route('vehicule_incident.update',$inc->id)}}" data-method="PUT" data-titre="Modifier l'incident" data-json='{{ json_encode(array_merge($inc->only(['id_coursier','type','lieu','description','cout','statut','date_resolution','immobilise']), ['date' => \Carbon\Carbon::parse($inc->date)->format('Y-m-d\TH:i')])) }}' onclick="parcOuvrir(this)"><i data-feather="edit-2"></i></button>
                                                <button type="button" class="btn btn-sm btn-icon btn-flat-danger" title="Supprimer" onclick="remplir('{{route('vehicule_incident.destroy',$inc->id)}}','{{$inc->type_libelle}} du {{Ladate($inc->date)}}',' supprimer cet incident ','button_delete_parc')"><i data-feather="trash-2"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center text-muted py-2">Aucun incident déclaré.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- KILOMETRAGE --}}
                        <div class="tab-pane {{$onglet=='kilometrage'?'active':''}}" id="onglet_kilometrage" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h5 class="mb-0">Historique du compteur</h5>
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal_kilometrage"><i data-feather="plus"></i> Relevé</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead><tr><th>Date</th><th>Kilométrage</th><th>Distance depuis le relevé précédent</th><th>Source</th><th>Remarques</th><th class="text-center">Actions</th></tr></thead>
                                    <tbody>
                                    @php $releves_ordonnes = $releves->sortBy(function($r){ return $r->date.'-'.str_pad($r->kilometrage, 10, '0', STR_PAD_LEFT); })->values(); $precedent = []; foreach($releves_ordonnes as $i => $r){ $precedent[$r->id] = $i > 0 ? $r->kilometrage - $releves_ordonnes[$i-1]->kilometrage : null; } @endphp
                                    @forelse($releves as $r)
                                        <tr>
                                            <td>{{Ladate($r->date)}}</td>
                                            <td class="font-weight-bolder">{{formatMontant($r->kilometrage)}} km</td>
                                            <td>{{$precedent[$r->id] !== null ? '+'.formatMontant($precedent[$r->id]).' km' : '-'}}</td>
                                            <td><span class="badge badge-light-secondary">{{$r->source_libelle}}</span></td>
                                            <td>{{$r->description ?: '-'}}</td>
                                            <td class="text-center">
                                                @if($r->source == 'manuel')
                                                <button type="button" class="btn btn-sm btn-icon btn-flat-danger" title="Supprimer" onclick="remplir('{{route('vehicule_kilometrage.destroy',$r->id)}}','{{formatMontant($r->kilometrage)}} km',' supprimer ce relevé ','button_delete_parc')"><i data-feather="trash-2"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-2">Aucun relevé. Le compteur se met à jour automatiquement via les pleins, entretiens et attributions.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- ATTRIBUTIONS --}}
                        <div class="tab-pane {{$onglet=='attributions'?'active':''}}" id="onglet_attributions" role="tabpanel">
                            <h5 class="mb-1">Historique des attributions</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead><tr><th>Coursier</th><th>Début</th><th>Fin</th><th>Durée</th><th>Km début</th><th>Km fin</th><th>Distance</th></tr></thead>
                                    <tbody>
                                    @forelse($attributions as $a)
                                        <tr class="{{$a->date_fin == null ? 'table-success' : ''}}">
                                            <td class="font-weight-bolder">@if($a->coursier)<a href="{{route('coursiers.show',$a->coursier->id)}}">{{$a->coursier->noms}} {{$a->coursier->prenoms}}</a>@else -@endif</td>
                                            <td>{{Ladate($a->date_debut)}} <small class="text-muted">{{Heure($a->date_debut)}}</small></td>
                                            <td>@if($a->date_fin){{Ladate($a->date_fin)}} <small class="text-muted">{{Heure($a->date_fin)}}</small>@else<span class="badge badge-light-success">En cours</span>@endif</td>
                                            <td>{{TempsEcoule($a->date_debut)}}@if($a->date_fin == null) (en cours)@endif</td>
                                            <td>{{$a->kilometrage_debut !== null ? formatMontant($a->kilometrage_debut) : '-'}}</td>
                                            <td>{{$a->kilometrage_fin !== null ? formatMontant($a->kilometrage_fin) : '-'}}</td>
                                            <td>{{$a->distance !== null ? formatMontant($a->distance).' km' : '-'}}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted py-2">Aucune attribution enregistrée.</td></tr>
                                    @endforelse
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

@include('multi.pages.parc.partials.modal_document')
@include('multi.pages.parc.partials.modal_maintenance')
@include('multi.pages.parc.partials.modal_carburant')
@include('multi.pages.parc.partials.modal_incident')
@include('multi.pages.parc.partials.modal_kilometrage')
@include('multi.pages.parc.partials.modal_attribution')

<div id="button_delete_parc" hidden>
    <form method="POST" class="destroy_form">
        @csrf
        @method('DELETE')
        <input type="hidden" name="retour" value="{{$retour}}">
        <button type="submit" class="btn btn-gradient-success btn-success">Confirmer</button>
    </form>
    <button type="button" class="btn btn-gradient-danger btn-danger" data-dismiss="modal">Annuler</button>
</div>
@endsection
@section('javascript')
@include('multi.pages.parc.partials.scripts')
<script type="text/javascript">
    document.querySelector('#VehiculeListe').classList.add('active');
    // Rafraîchit les icônes des contenus dynamiques (onglets/modales).
    document.addEventListener('DOMContentLoaded', function(){ if (window.feather) { feather.replace(); } });
    function parcApresOuverture(form){ if (window.feather) { feather.replace(); } }
</script>
@endsection
