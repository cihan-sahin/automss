<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Message::create([
            'phone_number' => '+905551111111',
            'content' => 'Insider - Project',
            'is_sent' => false,
        ]);

        Message::create([
            'phone_number' => '+905552222222',
            'content' => 'Welcome to the Message Sending System!',
            'is_sent' => false,
        ]);

        Message::create([
            'phone_number' => '+905553333333',
            'content' => 'Your message has been queued for delivery.',
            'is_sent' => false,
        ]);

        Message::factory()->count(7)->create();
    }
}
