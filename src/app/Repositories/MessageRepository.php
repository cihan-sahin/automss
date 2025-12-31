<?php

namespace App\Repositories;

use App\Contracts\MessageRepositoryInterface;
use App\Models\Message;
use Illuminate\Support\Collection;

class MessageRepository implements MessageRepositoryInterface
{
    public function __construct(
        private Message $model
    ) {
    }

    public function getUnsentMessages(int $limit = 2): Collection
    {
        return $this->model
            ->where('is_sent', false)
            ->orderBy('id')
            ->limit($limit)
            ->get();
    }

    public function markAsSent(Message $message, string $messageId): void
    {
        $message->update([
            'is_sent' => true,
            'message_id' => $messageId,
            'sent_at' => now(),
        ]);
    }

    public function getSentMessages(): Collection
    {
        return $this->model
            ->where('is_sent', true)
            ->orderBy('sent_at', 'desc')
            ->get();
    }

    public function create(array $data): Message
    {
        return $this->model->create($data);
    }
}
