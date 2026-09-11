<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentGateway\PaymentGatewayCheckout;
use App\Models\PaymentGateway\PaymentGatewayTransaction;
use App\Models\PaymentGateway\PaymentGatewayWebhookLog;
use App\Models\Refund;
use App\Models\ServicePackage;
use App\Models\User;
use App\Services\Order\OrderService;
use App\Services\Payment\Tap\TapException;
use App\Services\Payment\Tap\TapSignature;
use App\Services\Payment\TapService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Tap payment flow: charge creation, server-side verification, webhooks, refunds.
 * Order creation itself (OrderService) is mocked: these tests pin down when it runs.
 */
class TapPaymentTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'unit-test-secret';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'tap.enabled' => true,
            'tap.secret_key' => self::SECRET,
            'tap.currency' => 'USD',
            'tap.merchant_id' => null,
            'tap.webhook_url' => 'https://api.test/webhook/tap',
            'tap.return_url' => null,
            'tap.success_url' => 'https://front.test/payment/success',
            'tap.failure_url' => 'https://front.test/payment/failure',
        ]);

        Notification::fake();
        Http::preventStrayRequests();
    }

    // ---------------------------------------------------------------- charge

    public function test_checkout_creates_a_tap_charge_and_returns_the_payment_page(): void
    {
        Http::fake(['api.tap.company/v2/charges' => Http::response($this->charge('INITIATED'))]);
        $checkout = $this->checkout(reference: null);

        $result = app(TapService::class)->checkout($checkout->user, $checkout);

        $this->assertSame($this->charge('INITIATED')['transaction']['url'], $result['url']);
        $this->assertSame($checkout->uuid, $result['reference']);
        $this->assertSame('chg_TS0001', $checkout->fresh()->gateway_reference);

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && $request->url() === 'https://api.tap.company/v2/charges'
            && $request->hasHeader('Authorization', 'Bearer '.self::SECRET)
            && $request['amount'] == 110.0
            && $request['currency'] === 'USD'
            && $request['threeDSecure'] === true
            && $request['source']['id'] === 'src_all'
            && $request['reference']['transaction'] === $checkout->uuid
            && $request['customer']['email'] === $checkout->user->email
            && $request['post']['url'] === 'https://api.test/webhook/tap'
            && $request['redirect']['url'] === route('tap.return'));
    }

    public function test_captured_charge_creates_the_order_exactly_once(): void
    {
        $this->fakeRetrieve($this->charge('CAPTURED'));
        $this->expectOrders(1);
        $checkout = $this->checkout();

        app(TapService::class)->syncCharge('chg_TS0001');
        app(TapService::class)->syncCharge('chg_TS0001');

        $checkout->refresh();
        $this->assertTrue((bool) $checkout->is_processed);
        $this->assertNotNull($checkout->paid_at);
        $this->assertSame('CAPTURED', $checkout->gateway_status);
        $this->assertSame(1, PaymentGatewayTransaction::where('transaction_id', 'chg_TS0001')->count());
    }

    public function test_declined_charge_does_not_create_an_order(): void
    {
        $this->fakeRetrieve($this->charge('DECLINED'));
        $this->expectOrders(0);
        $checkout = $this->checkout();

        app(TapService::class)->syncCharge('chg_TS0001');

        $checkout->refresh();
        $this->assertFalse((bool) $checkout->is_processed);
        $this->assertSame('DECLINED', $checkout->gateway_status);
    }

    public function test_capture_for_a_different_amount_or_currency_is_not_fulfilled(): void
    {
        $this->fakeRetrieve(fn (Request $request) => Http::response(str_ends_with($request->url(), 'chg_TS0001')
            ? $this->charge('CAPTURED', ['amount' => 1.00])
            : $this->charge('CAPTURED', ['id' => 'chg_TS0002', 'currency' => 'SAR'])));
        $this->expectOrders(0);
        $first = $this->checkout();
        $second = $this->checkout(reference: 'chg_TS0002');

        app(TapService::class)->syncCharge('chg_TS0001');
        app(TapService::class)->syncCharge('chg_TS0002');

        foreach ([$first, $second] as $checkout) {
            $checkout->refresh();
            $this->assertFalse((bool) $checkout->is_processed);
            $this->assertSame('AMOUNT_MISMATCH', $checkout->gateway_status);
        }
    }

    // --------------------------------------------------------------- webhook

    public function test_valid_webhook_creates_the_order_and_duplicates_are_ignored(): void
    {
        $this->fakeRetrieve($this->charge('CAPTURED'));
        $this->expectOrders(1);
        $checkout = $this->checkout();

        $this->signedWebhook($this->charge('CAPTURED'))->assertOk();
        $this->signedWebhook($this->charge('CAPTURED'))->assertOk();

        $this->assertTrue((bool) $checkout->fresh()->is_processed);
        $this->assertSame(2, PaymentGatewayWebhookLog::where('payment_gateway', 'tap')->where('is_processed', true)->count());
    }

    public function test_webhooks_with_a_missing_wrong_or_tampered_signature_are_rejected(): void
    {
        Http::fake();
        $this->expectOrders(0);
        $checkout = $this->checkout();
        $payload = $this->charge('CAPTURED');

        $this->postJson(route('tap.webhook'), $payload)->assertStatus(401);

        $this->withHeaders(['hashstring' => TapSignature::sign($payload, 'attacker-secret')])
            ->postJson(route('tap.webhook'), $payload)
            ->assertStatus(401);

        $this->withHeaders(['hashstring' => TapSignature::sign($payload, self::SECRET)])
            ->postJson(route('tap.webhook'), array_replace($payload, ['amount' => 1.0]))
            ->assertStatus(401);

        $this->assertFalse((bool) $checkout->fresh()->is_processed);
        Http::assertNothingSent();
    }

    public function test_status_comes_from_the_api_not_from_the_webhook_body(): void
    {
        $this->fakeRetrieve($this->charge('DECLINED'));
        $this->expectOrders(0);
        $checkout = $this->checkout();

        $this->signedWebhook($this->charge('CAPTURED'))->assertOk();

        $this->assertFalse((bool) $checkout->fresh()->is_processed);
    }

    public function test_webhook_for_an_unknown_charge_is_acknowledged(): void
    {
        $this->fakeRetrieve($this->charge('CAPTURED', ['id' => 'chg_OTHER']));
        $this->expectOrders(0);

        $this->signedWebhook($this->charge('CAPTURED', ['id' => 'chg_OTHER']))->assertOk();
    }

    public function test_tap_outage_returns_500_so_the_webhook_is_retried(): void
    {
        $this->fakeRetrieve(fn () => Http::response(['errors' => [['description' => 'down']]], 503));
        $this->expectOrders(0);
        $checkout = $this->checkout();

        $this->signedWebhook($this->charge('CAPTURED'))->assertStatus(500);

        $this->assertFalse((bool) $checkout->fresh()->is_processed);
    }

    // ---------------------------------------------------------------- return

    public function test_return_verifies_the_charge_then_redirects_to_the_frontend(): void
    {
        $this->fakeRetrieve($this->charge('CAPTURED'));
        $this->expectOrders(1);
        $checkout = $this->checkout();

        $this->get(route('tap.return', ['tap_id' => 'chg_TS0001', 'status' => 'whatever']))
            ->assertRedirect('https://front.test/payment/success?reference='.$checkout->uuid.'&status=paid');
    }

    public function test_return_for_a_declined_charge_redirects_to_failure(): void
    {
        $this->fakeRetrieve($this->charge('DECLINED'));
        $this->expectOrders(0);
        $checkout = $this->checkout();

        $this->get(route('tap.return', ['tap_id' => 'chg_TS0001']))
            ->assertRedirect('https://front.test/payment/failure?reference='.$checkout->uuid.'&status=failed');
    }

    public function test_return_with_unknown_or_malformed_ids_never_calls_tap(): void
    {
        Http::fake();

        $this->get(route('tap.return', ['tap_id' => 'chg_FORGED']))->assertRedirect('https://front.test/payment/failure?status=failed');
        $this->get(route('tap.return', ['tap_id' => '../charges']))->assertRedirect('https://front.test/payment/failure?status=failed');

        Http::assertNothingSent();
    }

    // --------------------------------------------------------------- refunds

    public function test_full_refund(): void
    {
        $this->fakeRefunds();
        $checkout = $this->checkout(attributes: ['is_processed' => true]);

        $refund = app(TapService::class)->refund($checkout);

        $this->assertSame(TapService::REFUND_REFUNDED, $refund->status);
        $this->assertEquals(110.0, $refund->amount);
        Http::assertSent(fn (Request $request) => $request->url() === 'https://api.tap.company/v2/refunds'
            && $request['charge_id'] === 'chg_TS0001'
            && $request['amount'] == 110.0
            && $request['currency'] === 'USD'
            && $request['post']['url'] === 'https://api.test/webhook/tap');
    }

    public function test_partial_refunds_cannot_exceed_the_paid_amount(): void
    {
        $this->fakeRefunds();
        $checkout = $this->checkout(attributes: ['is_processed' => true]);
        $service = app(TapService::class);

        $service->refund($checkout, 60.25);
        $last = $service->refund($checkout); // remaining 49.75

        $this->assertEquals(49.75, $last->amount);

        $this->expectException(TapException::class);
        $service->refund($checkout, 0.01);
    }

    public function test_unpaid_checkout_cannot_be_refunded(): void
    {
        Http::fake();
        $checkout = $this->checkout();

        $this->expectException(TapException::class);

        try {
            app(TapService::class)->refund($checkout);
        } finally {
            Http::assertNothingSent();
        }
    }

    public function test_refund_rejected_by_tap_frees_the_balance(): void
    {
        $this->fakeRefunds(fn () => Http::response(['errors' => [['description' => 'Refund not allowed']]], 400));
        $checkout = $this->checkout(attributes: ['is_processed' => true]);

        try {
            app(TapService::class)->refund($checkout);
            $this->fail('Expected TapException');
        } catch (TapException) {
        }

        $this->assertSame(TapService::REFUND_FAILED, Refund::sole()->status);
    }

    public function test_pending_refund_reserves_the_balance_and_is_completed_by_webhook(): void
    {
        $this->fakeRefunds(
            fn (Request $request) => Http::response($this->refundResponse('PENDING', (float) $request['amount'])),
            $this->refundResponse('REFUNDED', 110.0),
        );
        $checkout = $this->checkout(attributes: ['is_processed' => true]);

        $refund = app(TapService::class)->refund($checkout);
        $this->assertSame(TapService::REFUND_PENDING, $refund->status);

        try {
            app(TapService::class)->refund($checkout, 1);
            $this->fail('A pending refund must reserve the balance');
        } catch (TapException) {
        }

        $this->signedWebhook($this->refundResponse('REFUNDED', 110.0))->assertOk();

        $this->assertSame(TapService::REFUND_REFUNDED, $refund->fresh()->status);
    }

    public function test_refund_command(): void
    {
        $this->fakeRefunds();
        $checkout = $this->checkout(attributes: ['is_processed' => true]);

        $this->artisan('tap:refund', ['checkout' => $checkout->uuid, 'amount' => '10'])->assertSuccessful();
        $this->artisan('tap:refund', ['checkout' => $checkout->uuid, 'amount' => '1000'])->assertFailed();

        $this->assertSame(1, Refund::where('status', TapService::REFUND_REFUNDED)->count());
    }

    // ------------------------------------------------------------------ sync

    public function test_sync_command_completes_a_charge_whose_webhook_was_missed(): void
    {
        $this->fakeRetrieve($this->charge('CAPTURED'));
        $this->expectOrders(1);
        $checkout = $this->checkout();

        $this->artisan('tap:sync')->assertSuccessful();

        $this->assertTrue((bool) $checkout->fresh()->is_processed);
    }

    // --------------------------------------------------------------- helpers

    private function expectOrders(int $times): void
    {
        $this->mock(OrderService::class, function (MockInterface $mock) use ($times) {
            $mock->shouldReceive('store')->times($times)->andReturn(new Order());
        });
    }

    private function checkout(?string $reference = 'chg_TS0001', array $attributes = []): PaymentGatewayCheckout
    {
        $user = User::create([
            'name' => 'Test Buyer',
            'email' => Str::random(8).'@example.com',
            'password' => bcrypt('secret-password'),
        ]);

        return PaymentGatewayCheckout::create(array_merge([
            'amount' => 110.0,
            'payable_id' => $this->servicePackageId($user),
            'payable_type' => ServicePackage::class,
            'user_id' => $user->id,
            'payment_gateway' => 'tap',
            'gateway_reference' => $reference,
            'currency' => 'USD',
        ], $attributes));
    }

    /**
     * Category rows are irrelevant here (order creation is mocked), so foreign keys are skipped.
     */
    private function servicePackageId(User $user): int
    {
        Schema::disableForeignKeyConstraints();

        $serviceId = DB::table('services')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'title' => 'Logo design',
            'description' => 'Test service',
            'category_id' => 1,
            'sub_category_id' => 1,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $packageId = DB::table('service_packages')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'price' => 100,
            'days' => 5,
            'revisions' => 2,
            'service_id' => $serviceId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::enableForeignKeyConstraints();

        return $packageId;
    }

    private function charge(string $status, array $overrides = []): array
    {
        return array_replace_recursive([
            'id' => 'chg_TS0001',
            'object' => 'charge',
            'amount' => 110.0,
            'currency' => 'USD',
            'status' => $status,
            'reference' => ['gateway' => '1234567', 'payment' => '9876543'],
            'response' => ['code' => $status === 'CAPTURED' ? '000' : '507', 'message' => $status],
            'transaction' => [
                'created' => '1789050000000',
                'url' => 'https://sandbox.payments.tap.company/test_gosell/v2/payment/tap_process.aspx?chg=chg_TS0001',
            ],
        ], $overrides);
    }

    private function refundResponse(string $status, float $amount, array $overrides = []): array
    {
        return array_replace_recursive([
            'id' => 're_TS0001',
            'object' => 'refund',
            'charge_id' => 'chg_TS0001',
            'amount' => $amount,
            'currency' => 'USD',
            'status' => $status,
            'created' => '1789060000000',
            'reference' => ['gateway' => '555', 'payment' => '666'],
        ], $overrides);
    }

    private function fakeRetrieve(array|\Closure $charge): void
    {
        Http::fake([
            'api.tap.company/v2/charges/*' => $charge instanceof \Closure ? $charge : Http::response($charge),
        ]);
    }

    private function fakeRefunds(?\Closure $create = null, ?array $retrieve = null): void
    {
        Http::fake([
            'api.tap.company/v2/refunds' => $create ?? fn (Request $request) => Http::response(
                $this->refundResponse('REFUNDED', (float) $request['amount'], ['id' => 're_'.Str::random(10)])
            ),
            'api.tap.company/v2/refunds/*' => Http::response($retrieve ?? $this->refundResponse('REFUNDED', 110.0)),
        ]);
    }

    private function signedWebhook(array $payload): TestResponse
    {
        return $this->withHeaders(['hashstring' => TapSignature::sign($payload, self::SECRET)])
            ->postJson(route('tap.webhook'), $payload);
    }
}
