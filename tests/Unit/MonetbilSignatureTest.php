<?php

namespace Tests\Unit;

use App\Services\Payment\Monetbil;
use PHPUnit\Framework\TestCase;

class MonetbilSignatureTest extends TestCase
{
    public function test_une_signature_valide_est_acceptee(): void
    {
        $params = ['transaction_id' => 'T1', 'status' => 'success', 'payment_ref' => 'speedex-transaction-4'];
        $params['sign'] = Monetbil::sign('secret', $params);

        $this->assertTrue(Monetbil::checkSign('secret', $params));
    }

    public function test_une_signature_falsifiee_ou_absente_est_refusee(): void
    {
        $params = ['transaction_id' => 'T1', 'status' => 'success'];
        $params['sign'] = Monetbil::sign('secret', $params);
        $params['status'] = 'failed';

        $this->assertFalse(Monetbil::checkSign('secret', $params));
        $this->assertFalse(Monetbil::checkSign('secret', ['status' => 'success']));
        $this->assertFalse(Monetbil::checkSign('secret', ['status' => 'success', 'sign' => ['tableau']]));
    }
}
