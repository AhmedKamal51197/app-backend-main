<?php

namespace Tests\Feature;

use App\Events\LahzaWebhookProcessEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LahzaWebhookTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_processes_lahza_webhook_and_triggers_event()
    {
        Event::fake();

        // Simulate Lahza Gateway sending a webhook payload
        $request = [
            'reference' => 'TEST123',
            // Add other fields as needed
        ];

        $response = $this->post('webhook/lahza', $request);

        // Assert that the response is a 200 OK
        $response->assertStatus(200);

        Event::assertDispatched(LahzaWebhookProcessEvent::class, 1);
    }
}
