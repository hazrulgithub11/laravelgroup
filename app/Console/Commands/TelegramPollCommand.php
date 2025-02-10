<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramPollCommand extends Command
{
    protected $signature = 'telegram:poll';
    protected $description = 'Start polling for Telegram updates';
    private $lastUpdateId = 0;

    public function handle()
    {
        $this->info('Starting Telegram polling...');
        
        while (true) {
            try {
                $this->info('Polling for updates...');
                
                $response = Http::withoutVerifying()
                    ->get('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/getUpdates', [
                        'offset' => $this->lastUpdateId + 1,
                        'timeout' => 30
                    ]);
                
                if ($response->successful()) {
                    $updates = $response->json()['result'] ?? [];
                    
                    foreach ($updates as $update) {
                        $this->processUpdate($update);
                        $this->lastUpdateId = $update['update_id'];
                    }
                } else {
                    $this->error('Failed to get updates: ' . $response->body());
                    sleep(5);
                }
            } catch (\Exception $e) {
                $this->error('Error: ' . $e->getMessage());
                sleep(5);
            }
        }
    }

    private function processUpdate($update)
    {
        $this->info('Processing update: ' . json_encode($update));

        if (isset($update['message'])) {
            $message = $update['message'];
            $chatId = $message['chat']['id'];
            $text = $message['text'] ?? '';
            $username = $message['chat']['username'] ?? null;

            $this->info("Received message '$text' from chat ID: $chatId (username: @$username)");

            if ($text === '/start' && $username) {
                // Try to find and update the user/provider with this telegram username
                $userOrProviderFound = $this->updateTelegramInfo($username, $chatId);

                $response = "Welcome to the Laundry System Bot! 🧺\n\n";
                $response .= "Your Chat ID is: `$chatId`\n";
                $response .= "Your Username is: @$username\n\n";
                
                if ($userOrProviderFound) {
                    $response .= "✅ Your Telegram information has been automatically saved.";
                } else {
                    $response .= "❗ No matching user or provider found for @$username\n";
                    $response .= "Please make sure you've registered with this Telegram username.";
                }
                
                $result = $this->sendMessage($chatId, $response);
                
                if ($result->successful() && ($result->json()['ok'] ?? false)) {
                    $this->info("Successfully sent welcome message to chat ID: $chatId");
                } else {
                    $this->error("Failed to send message: " . $result->body());
                }
            }
        }
    }

    private function updateTelegramInfo($username, $chatId)
    {
        // Remove @ from username if present
        $username = ltrim($username, '@');

        $found = false;

        // Try to find and update user with or without @ prefix
        $user = \App\Models\User::where('telegram_username', $username)
            ->orWhere('telegram_username', '@' . $username)
            ->first();
        
        if ($user) {
            $user->update([
                'telegram_chat_id' => $chatId,
                'telegram_verified_at' => now()
            ]);
            $this->info("Updated user {$user->name} with chat ID: $chatId");
            $found = true;
        }

        // Try to find and update provider with or without @ prefix
        $provider = \App\Models\Provider::where('telegram_username', $username)
            ->orWhere('telegram_username', '@' . $username)
            ->first();
        
        if ($provider) {
            $provider->update([
                'telegram_chat_id' => $chatId
            ]);
            $this->info("Updated provider {$provider->name} with chat ID: $chatId");
            $found = true;
        }

        if (!$found) {
            $this->warn("No user or provider found with telegram username: $username");
            // Log the actual search conditions for debugging
            $this->info("Searched for username: '$username' or '@$username'");
            
            // Log all users and their telegram usernames for debugging
            $allUsers = \App\Models\User::whereNotNull('telegram_username')->get(['id', 'name', 'telegram_username']);
            $this->info("All users with telegram usernames: " . json_encode($allUsers));
        }

        return $found;
    }

    private function sendMessage($chatId, $text)
    {
        $this->info("Sending message to $chatId: $text");
        
        return Http::withoutVerifying()
            ->post('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/sendMessage', [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown'
            ]);
    }
} 