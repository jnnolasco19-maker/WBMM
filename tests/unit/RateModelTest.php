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
            $daily = $sqm * (float) $rate['inside_rate_per_sqm'];
            return match ($paymentType) {
                'daily'   => round($daily, 2),
                default   => round($daily * 30, 2),
            };
        }

        if ($stallType === 'outside') {
            $daily = $sqm * (float) $rate['outside_monthly_rate'];
            return match ($paymentType) {
                'daily'   => round($daily, 2),
                default   => round($daily * 30, 2),
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
            'outside_monthly_rate' => 50.00,
            'ambulant_daily_rate'  => 15.00,
        ];
    }

    public function testInsideMonthlyComputation(): void
    {
        $this->assertSame(8100.0, $this->compute($this->rateFixture(), 'inside', 'monthly', 6.0));
    }

    public function testInsideDailyComputation(): void
    {
        $this->assertSame(270.0, $this->compute($this->rateFixture(), 'inside', 'daily', 6.0));
    }

    public function testOutsideMonthlyComputation(): void
    {
        $this->assertSame(3750.0, $this->compute($this->rateFixture(), 'outside', 'monthly', 2.5));
    }

    public function testOutsideDailyComputation(): void
    {
        $this->assertSame(125.0, $this->compute($this->rateFixture(), 'outside', 'daily', 2.5));
    }

    public function testAmbulantDailyOnly(): void
    {
        $this->assertSame(15.0, $this->compute($this->rateFixture(), 'ambulant', 'daily', 0));
    }
}
