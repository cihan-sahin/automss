<?php

namespace App\Console\Commands;

use App\Jobs\SendMessageJob;
use App\Services\MessageService;
use Illuminate\Console\Command;

class ProcessMessages extends Command
{
    protected $signature = 'messages:process';

    protected $description = 'Process and queue unsent messages (2 messages per 5 seconds)';

    public function __construct(
        private MessageService $messageService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Starting message processing...');

        $messages = $this->messageService->getUnsentMessages(2);

        if ($messages->isEmpty()) {
            $this->info('No unsent messages found.');
            return self::SUCCESS;
        }

        $this->info("Found {$messages->count()} message(s) to process.");

        foreach ($messages as $message) {
            SendMessageJob::dispatch($message)->delay(now());
            $this->info("Queued message ID: {$message->id}");
        }

        $this->info('Messages have been queued successfully.');
        $this->info('Run "php artisan queue:work" to process the queue.');

        return self::SUCCESS;
    }
}
