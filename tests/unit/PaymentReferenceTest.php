<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class PaymentReferenceTest extends TestCase
{
    public function testReferenceNoPattern(): void
    {
        $ref = 'ARK-' . date('Ymd') . '-0001';
        $this->assertMatchesRegularExpression('/^ARK-\d{8}-\d{4}$/', $ref);
    }

    public function testReferenceSequencePadding(): void
    {
        $seq = str_pad('7', 4, '0', STR_PAD_LEFT);
        $this->assertSame('0007', $seq);
    }
}
