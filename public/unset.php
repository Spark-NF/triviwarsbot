<?php
require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/config.php';

try {
    $telegram = new Longman\TelegramBot\Telegram($config['bot']['api_key'], $config['bot']['name']);
    
    $result = $telegram->unsetWebHook();

    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e;
}
