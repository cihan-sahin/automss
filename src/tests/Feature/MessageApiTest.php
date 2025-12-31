<?php

namespace Tests\Feature;

use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_sent_messages(): void
    {
        Message::factory()->count(3)->create([
            'is_sent' => true,
            'message_id' => 'test-message-id',
            'sent_at' => now(),
        ]);

        Message::factory()->count(2)->create([
            'is_sent' => false,
        ]);

        $response = $this->getJson('/api/messages');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'count',
                'data' => [
                    '*' => [
                        'id',
                        'phone_number',
                        'content',
                        'message_id',
                        'sent_at',
                        'created_at',
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'count' => 3,
            ]);
    }

    public function test_sent_messages_list_is_empty_when_no_messages_sent(): void
    {
        Message::factory()->count(2)->create([
            'is_sent' => false,
        ]);

        $response = $this->getJson('/api/messages');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'count' => 0,
                'data' => []
            ]);
    }
}
