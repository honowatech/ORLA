@extends('admin/templates/template')

@section('title')
{{'Paramètres de l\'entreprise'}}
@endsection
@section('contenu')
<div class="content app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-12 col-12 mb-2">
                <h2 class="content-header-title mb-0">Paramètres de l'entreprise</h2>
            </div>
        </div>
        <div class="content-body">
            <form method="POST" action="{{route('parametres.update')}}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row match-height">
                    <div class="col-lg-7 col-12">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title">Identité et coordonnées</h4></div>
                            <div class="card-body">
                                @foreach(['name' => ["Nom de l'entreprise", 'text', true], 'email' => ['Email de contact', 'email', false], 'telephone' => ['Téléphone (affiché sur le suivi des colis)', 'tel', false], 'telephone_secondaire' => ['Téléphone secondaire', 'tel', false], 'adresse' => ['Adresse', 'text', false]] as $champ => [$label, $type, $requis])
                                <div class="form-group">
                                    <label for="{{$champ}}">{{$label}}</label>
                                    <input type="{{$type}}" id="{{$champ}}" name="{{$champ}}" class="form-control @error($champ) is-invalid @enderror" value="{{ old($champ, $entreprise->$champ) }}" {{ $requis ? 'required' : '' }}>
                                    @error($champ)<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                                @endforeach
                                <div class="form-group">
                                    <label for="fuseau_horaire">Fuseau horaire</label>
                                    <select id="fuseau_horaire" name="fuseau_horaire" class="form-control">
                                        @foreach($fuseaux as $fuseau)
                                            <option value="{{$fuseau}}" {{ old('fuseau_horaire', $entreprise->fuseau_horaire) == $fuseau ? 'selected' : '' }}>{{$fuseau}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-12">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title">Personnalisation</h4></div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Logo (PNG, JPG ou WebP, 1 Mo max.)</label>
                                    @if($entreprise->logo)
                                        <div class="mb-1"><img src="{{route('entreprise.logo', $entreprise->slug)}}" alt="Logo" style="max-height: 64px; max-width: 200px"></div>
                                        <div class="custom-control custom-checkbox mb-1">
                                            <input type="checkbox" class="custom-control-input" id="supprimer_logo" name="supprimer_logo" value="1">
                                            <label class="custom-control-label" for="supprimer_logo">Supprimer le logo</label>
                                        </div>
                                    @endif
                                    <input type="file" name="logo" accept="image/png,image/jpeg,image/webp" class="form-control-file @error('logo') is-invalid @enderror">
                                    @error('logo')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                                </div>
                                <div class="form-group">
                                    <label for="couleur_principale">Couleur principale</label>
                                    <input type="color" id="couleur_principale" name="couleur_principale" class="form-control" style="max-width: 6rem" value="{{ old('couleur_principale', $entreprise->couleur_principale ?: '#00cfe8') }}">
                                    @error('couleur_principale')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                                    <small class="text-muted">Utilisée dans votre menu et sur la page de suivi envoyée à vos destinataires.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-info">Enregistrer</button>
            </form>
        </div>
    </div>
</div>
@endsection
