<?php

namespace Tests\Feature\Tenancy;

use App\Billing\Monetbil;
use App\Billing\PaiementAbonnement;
use App\Models\Entreprise;
use App\Models\SuperAdmin\Abonnement;
use App\Models\SuperAdmin\Api;
use App\Models\SuperAdmin\Transaction;
use App\Notifications\AbonnementARenouveler;
use App\Tenancy\EntrepriseCourante;
use App\Tenancy\InscriptionEntreprise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class FacturationTest extends TestCase
{
    use RefreshDatabase;

    protected const SECRET = 'secret-de-test';

    protected function offre(array $limites = []): Abonnement
    {
        $offre = new Abonnement;
        $offre->titre = 'Essentiel';
        $offre->accumulateur = 1;
        $offre->type_periode = 'mois';
        $offre->montant = 10000;
        $offre->periode_grace = 0;
        $offre->statut = 1;
        foreach ($limites as $colonne => $valeur) {
            $offre->$colonne = $valeur;
        }
        $offre->save();

        return $offre;
    }

    protected function transaction(Entreprise $entreprise, Abonnement $offre): Transaction
    {
        $api = new Api;
        $api->name = 'Monetbill';
        $api->key = 'cle';
        $api->secret = self::SECRET;
        $api->statut = 1;
        $api->save();

        $transaction = new Transaction;
        $transaction->id_client = $entreprise->id;
        $transaction->id_abonnement = $offre->id;
        $transaction->methode = 'mobile';
        $transaction->montant = $offre->montant;
        $transaction->nbre_abonnement = 1;
        $transaction->date_debut = now();
        $transaction->date_fin = now()->addMonth();
        $transaction->statut = 'waiting';

        return app(PaiementAbonnement::class)->preparer($transaction);
    }

    protected function retourMonetbil(Transaction $transaction, array $surcharge = []): array
    {
        $params = array_merge(['transaction_id' => 'TX123', 'payment_ref' => $transaction->reference_paiement, 'status' => 'success'], $surcharge);
        $params['sign'] = Monetbil::signer(self::SECRET, $params);

        return $params;
    }

    protected function monetbilRepond(int $statut, float $montant, ?string $ref = null): void
    {
        Http::fake([Monetbil::CHECK_PAYMENT_URL => Http::response(['transaction' => array_filter([
            'status' => $statut, 'testmode' => 0, 'amount' => $montant, 'payment_ref' => $ref,
        ], fn ($v) => $v !== null)])]);
    }

    public function test_un_paiement_verifie_prolonge_l_acces_une_seule_fois(): void
    {
        $entreprise = Entreprise::create(['name' => 'Alpha', 'statut' => 1]);
        $offre = $this->offre();
        $transaction = $this->transaction($entreprise, $offre);
        $this->monetbilRepond(Monetbil::STATUT_SUCCES, 10000, $transaction->reference_paiement);

        $service = app(PaiementAbonnement::class);
        $this->assertSame('success', $service->confirmer($transaction, $this->retourMonetbil($transaction)));
        $finApresPaiement = $entreprise->fresh()->date_fin;

        $this->assertTrue($entreprise->fresh()->abonnementValide());
        $this->assertSame('success', $service->confirmer($transaction->fresh(), $this->retourMonetbil($transaction)));
        $this->assertEquals($finApresPaiement, $entreprise->fresh()->date_fin);
    }

    public function test_un_paiement_falsifie_n_accorde_rien(): void
    {
        $entreprise = Entreprise::create(['name' => 'Alpha', 'statut' => 1]);
        $transaction = $this->transaction($entreprise, $this->offre());
        $this->monetbilRepond(Monetbil::STATUT_SUCCES, 10000);
        $service = app(PaiementAbonnement::class);

        $signatureFausse = $this->retourMonetbil($transaction);
        $signatureFausse['sign'] = 'faux';
        $this->assertSame('failed', $service->confirmer($transaction, $signatureFausse));

        $autreReference = $this->retourMonetbil($transaction, ['payment_ref' => 'une-autre-reference']);
        $this->assertSame('failed', $service->confirmer($transaction->fresh(), $autreReference));

        $this->monetbilRepond(Monetbil::STATUT_SUCCES, 100);
        $this->assertSame('failed', $service->confirmer($transaction->fresh(), $this->retourMonetbil($transaction)));

        $this->assertFalse($entreprise->fresh()->abonnementValide());
    }

    public function test_les_limites_de_l_offre_s_appliquent_hors_essai(): void
    {
        $offre = $this->offre(['max_coursiers' => 1]);
        $entreprise = Entreprise::create(['name' => 'Alpha', 'statut' => 1, 'id_abonnement' => $offre->id, 'date_fin' => now()->addMonth()]);
        $courante = app(EntrepriseCourante::class);

        $creerLivreur = fn () => $courante->pour($entreprise, function () {
            $coursier = new \App\Models\coursiers\Coursiers;
            $coursier->noms = 'Livreur';
            $coursier->prenoms = 'Test';
            $coursier->telephone = '690000000';
            $coursier->statut = 1;
            $coursier->save();
        });

        $creerLivreur();
        $this->expectException(ValidationException::class);
        $creerLivreur();
    }

    public function test_aucune_limite_pendant_l_essai(): void
    {
        $offre = $this->offre(['max_coursiers' => 1]);
        $entreprise = Entreprise::create(['name' => 'Alpha', 'statut' => 1, 'id_abonnement' => $offre->id, 'essai_jusqu_au' => now()->addDays(5)]);

        app(EntrepriseCourante::class)->pour($entreprise, function () {
            foreach ([1, 2] as $i) {
                $coursier = new \App\Models\coursiers\Coursiers;
                $coursier->noms = "Livreur $i";
                $coursier->prenoms = 'Test';
                $coursier->telephone = '69000000'.$i;
                $coursier->statut = 1;
                $coursier->save();
            }
        });

        $this->assertSame(2, \Illuminate\Support\Facades\DB::table('coursiers')->where('entreprise_id', $entreprise->id)->count());
    }

    public function test_la_relance_est_envoyee_une_seule_fois(): void
    {
        Notification::fake();
        $entreprise = Entreprise::create(['name' => 'Alpha', 'statut' => 1, 'essai_jusqu_au' => now()->addDays(3)->setTime(12, 0)]);
        $user = app(InscriptionEntreprise::class)->creerAdministrateur($entreprise, 'Awa', 'awa@alpha.cm');

        $this->artisan('abonnements:relancer')->assertSuccessful();
        $this->artisan('abonnements:relancer')->assertSuccessful();

        Notification::assertSentToTimes($user, AbonnementARenouveler::class, 1);
    }
}
