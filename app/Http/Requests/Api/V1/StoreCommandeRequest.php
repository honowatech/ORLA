<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Commande « simple » passée par un client depuis l'application mobile.
 *
 * Pour chaque point (collecte, livraison), fournir soit `id_quartier`,
 * soit `quartier` (libellé libre) + `id_ville`.
 * Le montant de livraison est calculé côté serveur.
 */
class StoreCommandeRequest extends FormRequest
{
    private const TELEPHONE = 'regex:/^[62][0-9]{8}$/';

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $point = fn (string $p) => [
            "$p" => ['required', 'array'],
            "$p.contact" => ['required', 'string', self::TELEPHONE],
            "$p.id_quartier" => ["required_without:$p.quartier", 'nullable', 'integer', 'exists:quartier,id'],
            "$p.quartier" => ["required_without:$p.id_quartier", 'nullable', 'string', 'min:3', 'max:255'],
            "$p.id_ville" => ["required_with:$p.quartier", 'nullable', 'integer', 'exists:ville,id'],
            "$p.description" => ['nullable', 'string', 'max:255'],
        ];

        return [
            ...$point('collecte'),
            ...$point('livraison'),
            'livraison.nom' => ['required', 'string', 'min:2', 'max:255'],
            'date_livraison' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'heure_livraison' => ['nullable', 'date_format:H:i'],
            'montant_a_recuperer' => ['nullable', 'numeric', 'min:0'],
            'mode_de_paiement' => ['required', Rule::in(['coursier', 'speedex'])],
            'description' => ['required', 'string', 'min:8', 'max:255'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'collecte.contact.regex' => 'Le contact de collecte doit être un numéro à 9 chiffres commençant par 6 ou 2.',
            'livraison.contact.regex' => 'Le contact de livraison doit être un numéro à 9 chiffres commençant par 6 ou 2.',
        ];
    }
}
