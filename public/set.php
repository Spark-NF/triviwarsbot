<?php
require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/config.php';

try {
    $telegram = new Longman\TelegramBot\Telegram($config['bot']['api_key'], $config['bot']['name']);

    $result = $telegram->setWebHook($config['bot']['hook_url']);
    //$result = $telegram->setWebHook($config['bot']['hook_url'], $path_certificate);

    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e;
}
