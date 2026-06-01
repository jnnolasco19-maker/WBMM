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
        $currentRate = (new \App\Models\RateModel())->getCurrent();
        $ratePerSqm = $currentRate ? (float) $currentRate['inside_rate_per_sqm'] : 45.0;
        $expectedAmount = round(6 * $ratePerSqm * 30, 2);

        $result = $this->withSession([
            'is_logged_in' => true,
            'user_id'      => 1,
            'user_role'    => 'admin',
            'user_name'    => 'Market Administrator',
        ])->get('payments/ajax/compute?stall_type=inside&payment_type=monthly&sqm=6');
        $result->assertOK();
        $json = json_decode($result->response()->getBody(), true);
        $this->assertSame($expectedAmount, (float) $json['computed_amount']);
    }

    public function testAjaxComputeOutsideMonthly(): void
    {
        $currentRate = (new \App\Models\RateModel())->getCurrent();
        $ratePerSqm = $currentRate ? (float) $currentRate['outside_monthly_rate'] : 50.0;
        $expectedAmount = round(2.5 * $ratePerSqm * 30, 2);

        $result = $this->withSession([
            'is_logged_in' => true,
            'user_id'      => 1,
            'user_role'    => 'admin',
            'user_name'    => 'Market Administrator',
        ])->get('payments/ajax/compute?stall_type=outside&payment_type=monthly&sqm=2.5');
        $result->assertOK();
        $json = json_decode($result->response()->getBody(), true);
        $this->assertSame($expectedAmount, (float) $json['computed_amount']);
    }
}
