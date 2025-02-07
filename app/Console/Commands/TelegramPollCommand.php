<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramPollCommand extends Command
{
    protected $signature = 'telegram:poll {--clear-updates}';
    protected $description = 'Poll for Telegram updates';

    private $lastUpdateId = 0;

    public function handle()
    {
        $this->info('Starting Telegram polling...');

        // Clear existing updates if requested
        if ($this->option('clear-updates')) {
            $this->clearUpdates();
        }
        
        while (true) {
            try {
                $this->info('Polling for updates...');
                $response = Http::get('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/getUpdates', [
                    'offset' => $this->lastUpdateId + 1,
                    'timeout' => 10
                ]);

                if (!$response->successful()) {
                    $this->error('HTTP Error: ' . $response->status());
                    $this->error('Response: ' . $response->body());
                    sleep(5);
                    continue;
                }

                $data = $response->json();
                $this->info('Response received: ' . json_encode($data));

                if ($data['ok'] && !empty($data['result'])) {
                    foreach ($data['result'] as $update) {
                        $this->processUpdate($update);
                        $this->lastUpdateId = $update['update_id'];
                    }
                }

                // Small delay to prevent hammering the API
                usleep(500000); // 0.5 second delay
            } catch (\Exception $e) {
                $this->error('Error: ' . $e->getMessage());
                Log::error('Telegram polling error:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                sleep(5);
            }
        }
    }

    private function clearUpdates()
    {
        $this->info('Clearing existing updates...');
        try {
            $response = Http::get('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/getUpdates', [
                'offset' => -1
            ]);
            $this->info('Updates cleared.');
        } catch (\Exception $e) {
            $this->error('Failed to clear updates: ' . $e->getMessage());
        }
    }

    private function processUpdate($update)
    {
        $this->info('Processing update: ' . json_encode($update));

        if (isset($update['message'])) {
            $message = $update['message'];
            $chatId = $message['chat']['id'];
            $text = $message['text'] ?? '';
            $username = $message['chat']['username'] ?? 'unknown';

            $this->info("Received message '$text' from chat ID: $chatId (username: @$username)");

            if ($text === '/start') {
                $response = "Welcome to the Laundry System Bot! 🧺\n\n";
                $response .= "Your Chat ID is: `$chatId`\n";
                $response .= "Your Username is: @$username\n\n";
                $response .= "Please save this Chat ID and update it in your provider profile to receive order notifications.";
                
                $result = $this->sendMessage($chatId, $response);
                
                if ($result->successful() && ($result->json()['ok'] ?? false)) {
                    $this->info("Successfully sent welcome message to chat ID: $chatId");
                } else {
                    $this->error("Failed to send message: " . $result->body());
                }
            }
        }
    }

    private function sendMessage($chatId, $text)
    {
        $this->info("Sending message to $chatId: $text");
        
        return Http::post('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown'
        ]);
    }
} 