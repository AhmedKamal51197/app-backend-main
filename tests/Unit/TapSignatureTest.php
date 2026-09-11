<?php

namespace Tests\Unit;

use App\Services\Payment\Tap\TapSignature;
use App\Support\Money;
use PHPUnit\Framework\TestCase;

class TapSignatureTest extends TestCase
{
    public function test_hashstring_follows_the_documented_format(): void
    {
        $payload = [
            'id' => 'chg_1',
            'amount' => 1.5,
            'currency' => 'KWD',
            'status' => 'CAPTURED',
            'reference' => ['gateway' => 'gw', 'payment' => 'py'],
            'transaction' => ['created' => '1700'],
        ];

        $expected = hash_hmac(
            'sha256',
            'x_idchg_1x_amount1.500x_currencyKWDx_gateway_referencegwx_payment_referencepyx_statusCAPTUREDx_created1700',
            'test-secret'
        );

        $this->assertSame($expected, TapSignature::sign($payload, 'test-secret'));
        $this->assertTrue(TapSignature::verify($payload, strtoupper($expected), 'test-secret'));
        $this->assertFalse(TapSignature::verify($payload, $expected, 'other-secret'));
        $this->assertFalse(TapSignature::verify($payload, null, 'test-secret'));
        $this->assertFalse(TapSignature::verify($payload, $expected, ''));
    }

    public function test_two_decimal_currencies(): void
    {
        $payload = ['id' => 'chg_2', 'amount' => 10, 'currency' => 'USD', 'status' => 'CAPTURED', 'transaction' => ['created' => '1']];

        $expected = hash_hmac('sha256', 'x_idchg_2x_amount10.00x_currencyUSDx_gateway_referencex_payment_referencex_statusCAPTUREDx_created1', 'k');

        $this->assertSame($expected, TapSignature::sign($payload, 'k'));
    }

    public function test_money_conversions(): void
    {
        $this->assertSame(2, Money::decimals('SAR'));
        $this->assertSame(3, Money::decimals('kwd'));
        $this->assertSame(15050, Money::toMinor('150.50', 'USD'));
        $this->assertSame(30, Money::toMinor(0.1 + 0.2, 'SAR'));
        $this->assertSame(1.234, Money::toFloat(1234, 'KWD'));
        $this->assertSame('0.05 SAR', Money::format(5, 'sar'));
    }
}
