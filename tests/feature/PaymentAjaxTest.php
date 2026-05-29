<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\Mock\MockSecurity;
use Config\Security;
use Config\Services;

/**
 * @internal
 */
final class PaymentAjaxTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected $migrate = false;

    protected function setUp(): void
    {
        parent::setUp();
        Services::injectMock('security', new MockSecurity(config(Security::class)));
    }

    public function testAjaxComputeInsideMonthly(): void
    {
        $result = $this->withSession([
            'is_logged_in' => true,
            'user_id'      => 1,
            'user_role'    => 'admin',
            'user_name'    => 'Market Administrator',
        ])->get('payments/ajax/compute?stall_type=inside&payment_type=monthly&sqm=6');
        $result->assertOK();
        $json = json_decode($result->response()->getBody(), true);
        $this->assertSame(270.0, (float) $json['computed_amount']);
    }
}
