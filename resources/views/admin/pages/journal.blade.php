@extends('admin/templates/template')

@section('title')
{{'Journal d\'activité'}}
@endsection
@section('contenu')
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-12 col-12 mb-2 d-flex justify-content-between flex-wrap" style="gap: .5rem">
                <h2 class="content-header-title mb-0">Journal d'activité</h2>
                <a href="{{route('export')}}" class="btn btn-outline-info btn-sm">Exporter toutes mes données (ZIP)</a>
            </div>
        </div>
        <div class="content-body">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="form-inline mb-1" style="gap: .5rem">
                        <select name="table" class="form-control form-control-sm">
                            <option value="">Toutes les données</option>
                            @foreach($libelles as $table => $libelle)
                                <option value="{{$table}}" {{ request('table') == $table ? 'selected' : '' }}>{{$libelle}}</option>
                            @endforeach
                        </select>
                        <select name="action" class="form-control form-control-sm">
                            <option value="">Toutes les actions</option>
                            @foreach($actions as $action => [$libelle])
                                <option value="{{$action}}" {{ request('action') == $action ? 'selected' : '' }}>{{$libelle}}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-sm btn-info">Filtrer</button>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Date</th><th>Par</th><th>Action</th><th>Donnée</th><th>Détail</th></tr></thead>
                            <tbody>
                                @forelse($entrees as $entree)
                                <tr>
                                    <td class="text-nowrap">{{$entree->created_at->format('d/m/Y H:i')}}</td>
                                    <td>{{$entree->user->noms ?? 'Système'}}</td>
                                    <td><span class="badge badge-light-{{$actions[$entree->action][1] ?? 'secondary'}}">{{$actions[$entree->action][0] ?? $entree->action}}</span></td>
                                    <td>{{$libelles[$entree->sujet_table] ?? ($entree->action == 'export' ? 'Toutes les données' : $entree->sujet_table)}} @if($entree->action != 'export')n° {{$entree->sujet_id}}@endif</td>
                                    <td class="small">
                                        @if($entree->action == 'modification')
                                            @foreach($entree->changements ?? [] as $colonne => $valeurs)
                                                <div><b>{{$colonne}}</b> : {{ Str::limit((string) ($valeurs['avant'] ?? '—'), 40) }} → {{ Str::limit((string) ($valeurs['apres'] ?? '—'), 40) }}</div>
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted">Aucune activité.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $entrees->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
