<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuartierResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'libelle' => $this->libelle,
            'id_ville' => $this->id_ville,
            'id_zone' => $this->id_zone,
            'ville' => $this->whenLoaded('ville', fn () => $this->ville?->libelle),
        ];
    }
}
