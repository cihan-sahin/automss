<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Message $message
    ) {
    }

    public function middleware(): array
    {
        return [new RateLimited('messages')];
    }

    public function handle(MessageService $messageService): void
    {
        Log::info('Processing message', ['message_id' => $this->message->id]);

        $messageService->sendMessage($this->message);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Job failed for message', [
            'message_id' => $this->message->id,
            'error' => $exception->getMessage()
        ]);
    }
}
