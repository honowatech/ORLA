<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class EstimationTarifRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'id_quartier_collecte' => ['required', 'integer', 'exists:quartier,id'],
            'id_quartier_livraison' => ['required', 'integer', 'exists:quartier,id'],
        ];
    }
}
