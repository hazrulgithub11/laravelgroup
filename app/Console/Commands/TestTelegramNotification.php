<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestTelegramNotification extends Command
{
    protected $signature = 'telegram:test {username?}';
    protected $description = 'Test Telegram notification sending';

    private function getTelegramBot()
    {
        return Http::baseUrl('https://api.telegram.org/bot' . config('services.telegram-bot-api.token'));
    }

    public function handle()
    {
        try {
            // First, let's check the bot's information
            $this->info("Checking bot status...");
            $botInfo = $this->getTelegramBot()->get('getMe')->json();
            
            if ($botInfo['ok']) {
                $bot = $botInfo['result'];
                $this->info("✓ Bot is active!");
                $this->info("Bot Details:");
                $this->info("- Name: " . $bot['first_name']);
                $this->info("- Username: @" . $bot['username']);
                $this->info("- Bot ID: " . $bot['id']);

                $this->info("\nIMPORTANT: To receive messages, you need to:");
                $this->info("1. Open Telegram");
                $this->info("2. Search for @" . $bot['username']);
                $this->info("3. Click 'Start' or send /start command");
                $this->info("4. The bot will reply with your Chat ID");
            } else {
                throw new \Exception("Could not get bot information. Please check your TELEGRAM_BOT_TOKEN.");
            }

            // If a username was provided, try to send a test message
            $username = $this->argument('username');
            if ($username) {
                // Check if it looks like a Chat ID (numeric)
                $chatId = is_numeric($username) ? $username : '@' . $username;
                
                $this->info("\nAttempting to send test message to " . $chatId . "...");
                
                $message = "🔔 This is a test notification from your Laundry System.\n\nIf you receive this message, notifications are working correctly!";
                
                $response = $this->getTelegramBot()->post('sendMessage', [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'Markdown'
                ])->json();
                
                if ($response['ok']) {
                    $this->info('Test message sent successfully!');
                } else {
                    throw new \Exception($response['description'] ?? 'Unknown error');
                }
            }
        } catch (\Exception $e) {
            $this->error('Error occurred:');
            $this->error($e->getMessage());
            $this->newLine();
            $this->info('Please check:');
            $this->info('1. Your TELEGRAM_BOT_TOKEN in .env is correct');
            $this->info('2. If sending message: Make sure you have started the bot');
            $this->info('3. If using username: Try using your Chat ID instead (get it by starting the bot)');
        }
    }
} 