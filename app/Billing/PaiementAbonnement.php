<?php

namespace App\Billing;

use App\Models\SuperAdmin\Info_transaction;
use App\Models\SuperAdmin\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Cycle de vie d'un paiement d'abonnement : démarrage, confirmation (retour
 * navigateur ou notification serveur Monetbil) et prolongation de l'accès.
 */
class PaiementAbonnement
{
    public function __construct(protected Monetbil $monetbil)
    {
    }

    /** Référence unique transmise à Monetbil et exigée au retour. */
    public function preparer(Transaction $transaction): Transaction
    {
        $transaction->reference_paiement = Str::random(40);
        $transaction->save();

        return $transaction;
    }

    /**
     * Confirme une transaction à partir des paramètres renvoyés par Monetbil.
     * Rien n'est accordé sans signature valide, référence identique et montant
     * suffisant confirmés par l'API Monetbil. Rejouer la confirmation est sans effet.
     */
    public function confirmer(Transaction $transaction, array $params): string
    {
        if ($transaction->statut === 'success') {
            return 'success';
        }

        $statut = $this->statutVerifie($transaction, $params);

        DB::transaction(function () use ($transaction, $params, $statut) {
            $transaction = Transaction::lockForUpdate()->findOrFail($transaction->id);
            if ($transaction->statut === 'success') {
                return;
            }

            $this->enregistrerInfos($transaction, $params);
            $transaction->statut = $statut;
            $transaction->save();

            if ($statut === 'success') {
                $this->accorder($transaction);
            }
        });

        return $statut;
    }

    /**
     * Prolonge l'accès de l'entreprise : la période payée commence à la fin de
     * l'abonnement en cours, ou maintenant s'il est déjà terminé.
     */
    public function accorder(Transaction $transaction): void
    {
        $entreprise = $transaction->client;
        $abonnement = $transaction->abonnement;

        $debut = $entreprise->date_fin !== null && $entreprise->date_fin->isFuture() ? $entreprise->date_fin : now();
        $fin = Sa_prochaine_date_paie($abonnement->type_periode, $abonnement->accumulateur, $debut, max(1, (int) $transaction->nbre_abonnement));

        $transaction->date_debut = $debut;
        $transaction->date_fin = $fin;
        $transaction->confirme_le = now();
        $transaction->save();

        $entreprise->id_abonnement = $abonnement->id;
        $entreprise->date_fin = $fin;
        $entreprise->date_dernier_paiement = now();
        $entreprise->derniere_relance = null;
        $entreprise->save();
    }

    protected function statutVerifie(Transaction $transaction, array $params): string
    {
        if (! $this->monetbil->signatureValide($params)) {
            return 'failed';
        }

        if ($transaction->reference_paiement === null
            || ! hash_equals($transaction->reference_paiement, (string) ($params['payment_ref'] ?? ''))) {
            return 'failed';
        }

        if (empty($params['transaction_id'])) {
            return 'failed';
        }

        $paiement = $this->monetbil->verifierPaiement((string) $params['transaction_id']);

        if ($paiement['statut'] !== 'success') {
            return $paiement['statut'];
        }

        if ($paiement['payment_ref'] !== null && $paiement['payment_ref'] !== $transaction->reference_paiement) {
            return 'failed';
        }

        if ($paiement['montant'] === null || $paiement['montant'] < (float) $transaction->montant) {
            return 'failed';
        }

        return 'success';
    }

    protected function enregistrerInfos(Transaction $transaction, array $params): void
    {
        $cles = ['transaction_id', 'phone', 'user', 'item_ref', 'payment_ref', 'first_name', 'last_name', 'email', 'status'];
        foreach ($cles as $cle) {
            $valeur = $params[$cle] ?? null;
            if ($valeur === null || $valeur === '') {
                continue;
            }
            $existe = Info_transaction::where('id_transaction',$transaction->id)->where('name',$cle)->where('value',$valeur)->exists();
            if (! $existe) {
                $info = new Info_transaction;
                $info->id_transaction = $transaction->id;
                $info->name = $cle;
                $info->value = $valeur;
                $info->save();
            }
        }
    }
}
