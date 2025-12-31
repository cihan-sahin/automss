<?php

namespace App\Contracts;

use App\Models\Message;
use Illuminate\Support\Collection;

interface MessageRepositoryInterface
{
    public function getUnsentMessages(int $limit = 2): Collection;

    public function markAsSent(Message $message, string $messageId): void;

    public function getSentMessages(): Collection;

    public function create(array $data): Message;
}
