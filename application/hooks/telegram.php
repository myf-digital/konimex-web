<?php
// Load composer
require __DIR__ . '/vendor/autoload.php';

$API_KEY = '382394330:AAE3hny3KQcxCpEEU35WSVknv3ONSKIS38c';
$BOT_NAME = 'sphereds_bot';
try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($API_KEY, $BOT_NAME);

    // Handle telegram webhook request
    $telegram->handle();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Silence is golden!
    // log telegram errors
    // echo $e;
}