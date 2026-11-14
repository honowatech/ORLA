<?php

namespace App\Http\Resources\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $role = $this->role();

        return [
            'id' => $this->id,
            'noms' => $this->noms,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'role' => $role,
            'type_utilisateur' => $this->type_utilisateur?->libelle,
            'actif' => (bool) $this->statut,
            'ville' => $this->ville ? [
                'id' => $this->ville->id,
                'libelle' => $this->ville->libelle,
            ] : null,
            'coursier' => $this->when($role === User::ROLE_COURSIER && $this->coursier_utilisateur, fn () => [
                'id' => $this->coursier_utilisateur->id,
                'noms' => $this->coursier_utilisateur->noms,
                'prenoms' => $this->coursier_utilisateur->prenoms,
                'telephone' => $this->coursier_utilisateur->telephone,
                'disponible' => (bool) $this->coursier_utilisateur->statut,
            ]),
            'client' => $this->when($role === User::ROLE_CLIENT && $this->client_utilisateur, fn () => [
                'id' => $this->client_utilisateur->id,
                'noms' => $this->client_utilisateur->noms,
                'prenoms' => $this->client_utilisateur->Prenoms,
                'telephone' => $this->client_utilisateur->telephone,
                'type_client' => $this->client_utilisateur->type_client,
            ]),
        ];
    }
}
