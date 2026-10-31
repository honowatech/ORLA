<?php

namespace App\Billing;

use App\Models\SuperAdmin\Api;
use Illuminate\Support\Facades\Http;

/**
 * Vérifications Monetbil côté serveur (signature et statut réel du paiement).
 *
 * Remplace, pour la confirmation, la librairie fournie qui désactivait la
 * vérification SSL et ne renvoyait ni le montant ni la référence payés.
 */
class Monetbil
{
    public const CHECK_PAYMENT_URL = 'https://api.monetbil.com/payment/v1/checkPayment';

    public const STATUT_SUCCES = 1;
    public const STATUT_ANNULE = -1;
    public const STATUT_SUCCES_TEST = 7;
    public const STATUT_ANNULE_TEST = 9;

    public function api(): ?Api
    {
        return Api::where('statut',[1])->where('name','Monetbill')->first();
    }

    /** Même algorithme que la librairie Monetbil : md5(secret + valeurs triées par clé). */
    public static function signer(string $secret, array $params): string
    {
        unset($params['sign']);
        ksort($params);

        return md5($secret.implode('', $params));
    }

    public function signatureValide(array $params): bool
    {
        $secret = $this->api()?->secret;

        return $secret !== null
            && isset($params['sign'])
            && hash_equals(static::signer($secret, $params), (string) $params['sign']);
    }

    /**
     * Interroge Monetbil sur un paiement.
     *
     * @return array{statut: string, montant: float|null, payment_ref: string|null}
     */
    public function verifierPaiement(string $paymentId): array
    {
        $transaction = Http::asForm()->timeout(30)
            ->post(self::CHECK_PAYMENT_URL, ['paymentId' => $paymentId])
            ->json('transaction');

        $statut = (int) ($transaction['status'] ?? 0);
        $succes = $statut === self::STATUT_SUCCES || ($statut === self::STATUT_SUCCES_TEST && ! app()->isProduction());
        $annule = in_array($statut, [self::STATUT_ANNULE, self::STATUT_ANNULE_TEST], true);

        return [
            'statut' => $succes ? 'success' : ($annule ? 'cancelled' : 'failed'),
            'montant' => isset($transaction['amount']) ? (float) $transaction['amount'] : null,
            'payment_ref' => $transaction['payment_ref'] ?? null,
        ];
    }
}
