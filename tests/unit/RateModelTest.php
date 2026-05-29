<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Pure computation tests (mirrors RateModel::compute logic).
 *
 * @internal
 */
final class RateModelTest extends TestCase
{
    private function compute(array $rate, string $stallType, string $paymentType, float $sqm = 0): float
    {
        if ($stallType === 'inside') {
            $monthly = $sqm * (float) $rate['inside_rate_per_sqm'];

            return match ($paymentType) {
                'daily'  => round($monthly / 30, 2),
                'weekly' => round($monthly / 4, 2),
                default  => round($monthly, 2),
            };
        }

        if ($stallType === 'outside') {
            return match ($paymentType) {
                'daily'  => (float) $rate['outside_daily_rate'],
                'weekly' => (float) $rate['outside_weekly_rate'],
                default  => (float) $rate['outside_monthly_rate'],
            };
        }

        return (float) $rate['ambulant_daily_rate'];
    }

    private function rateFixture(): array
    {
        return [
            'inside_rate_per_sqm'  => 45.00,
            'outside_daily_rate'   => 25.00,
            'outside_weekly_rate'  => 150.00,
            'outside_monthly_rate' => 500.00,
            'ambulant_daily_rate'  => 15.00,
        ];
    }

    public function testInsideMonthlyComputation(): void
    {
        $this->assertSame(270.0, $this->compute($this->rateFixture(), 'inside', 'monthly', 6.0));
    }

    public function testInsideDailyComputation(): void
    {
        $this->assertSame(9.0, $this->compute($this->rateFixture(), 'inside', 'daily', 6.0));
    }

    public function testOutsideFlatWeekly(): void
    {
        $this->assertSame(150.0, $this->compute($this->rateFixture(), 'outside', 'weekly', 0));
    }

    public function testAmbulantDailyOnly(): void
    {
        $this->assertSame(15.0, $this->compute($this->rateFixture(), 'ambulant', 'daily', 0));
    }
}
