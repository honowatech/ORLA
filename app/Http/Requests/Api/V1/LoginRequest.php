<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            // Nom de l'appareil, affiché dans la liste des sessions (ex. « Pixel 8 de Paul »).
            'device_name' => ['required', 'string', 'max:255'],
        ];
    }
}
