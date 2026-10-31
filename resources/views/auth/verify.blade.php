@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Confirmez votre adresse email</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            Un nouveau lien de confirmation vient de vous être envoyé.
                        </div>
                    @endif

                    Pour accéder à votre espace, cliquez sur le lien de confirmation envoyé à {{ auth()->user()->email }}.
                    Vous n'avez rien reçu ?
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">Renvoyer le lien</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
