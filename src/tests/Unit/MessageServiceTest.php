<?php

namespace Tests\Unit;

use App\Contracts\MessageRepositoryInterface;
use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class MessageServiceTest extends TestCase
{
    public function test_get_unsent_messages_returns_collection(): void
    {
        $repository = Mockery::mock(MessageRepositoryInterface::class);
        $repository->shouldReceive('getUnsentMessages')
            ->with(2)
            ->once()
            ->andReturn(new Collection([new Message(), new Message()]));

        $service = new MessageService($repository);
        $result = $service->getUnsentMessages(2);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    public function test_get_sent_messages_returns_formatted_array(): void
    {
        $message = new Message([
            'id' => 1,
            'phone_number' => '+905551111111',
            'content' => 'Test message',
            'message_id' => 'test-id',
            'is_sent' => true,
        ]);
        $message->sent_at = now();
        $message->created_at = now();

        $repository = Mockery::mock(MessageRepositoryInterface::class);
        $repository->shouldReceive('getSentMessages')
            ->once()
            ->andReturn(new Collection([$message]));

        $service = new MessageService($repository);
        $result = $service->getSentMessages();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('phone_number', $result[0]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
