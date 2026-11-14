<?php

namespace App\Http\Requests\Api\V1;

use App\Services\CommandeStatutService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangerStatutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'statut' => ['required', 'string', Rule::in(CommandeStatutService::STATUTS)],
        ];
    }
}
