<?php

namespace App\Services;

use App\Contracts\MessageRepositoryInterface;
use App\Models\Message;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MessageService
{
    private const MAX_MESSAGE_LENGTH = 1000;

    public function __construct(
        private MessageRepositoryInterface $repository
    ) {
    }

    public function getUnsentMessages(int $limit = 2): Collection
    {
        return $this->repository->getUnsentMessages($limit);
    }

    public function sendMessage(Message $message): bool
    {
        if (!$this->validateMessageContent($message->content)) {
            Log::error('Message content exceeds character limit', [
                'message_id' => $message->id,
                'content_length' => strlen($message->content)
            ]);
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-ins-auth-key' => config('services.webhook.auth_key', 'INS.me1x9uMcyYG1hKKQVPoc.bO3j9aZwRT0cA2Ywo')
            ])->post(config('services.webhook.url'), [
                'to' => $message->phone_number,
                'content' => $message->content
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $messageId = $data['messageId'] ?? null;

                if ($messageId) {
                    $this->repository->markAsSent($message, $messageId);
                    $this->cacheMessageData($messageId, $message);

                    Log::info('Message sent successfully', [
                        'message_id' => $message->id,
                        'external_message_id' => $messageId
                    ]);

                    return true;
                }
            }

            Log::error('Failed to send message', [
                'message_id' => $message->id,
                'response' => $response->body()
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Exception while sending message', [
                'message_id' => $message->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    public function getSentMessages(): array
    {
        $messages = $this->repository->getSentMessages();

        return $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'phone_number' => $message->phone_number,
                'content' => $message->content,
                'message_id' => $message->message_id,
                'sent_at' => $message->sent_at?->toIso8601String(),
                'created_at' => $message->created_at->toIso8601String(),
            ];
        })->toArray();
    }

    private function validateMessageContent(string $content): bool
    {
        return strlen($content) <= self::MAX_MESSAGE_LENGTH;
    }

    private function cacheMessageData(string $messageId, Message $message): void
    {
        $cacheKey = "message:{$message->id}";
        $cacheData = [
            'message_id' => $messageId,
            'record_id' => $messageId,
            'sent_at' => $message->sent_at->toIso8601String(),
        ];

        Cache::put($cacheKey, $cacheData, now()->addDays(7));
    }
}
