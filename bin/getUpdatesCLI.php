#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/config.php';

use TriviWars\Telegram;
use Longman\TelegramBot\TelegramLog;
use Longman\TelegramBot\Exception;

try {
    $telegram = new Telegram($config);
    $ServerResponse = $telegram->handleGetUpdates();

    // Print result to the console
    if ($ServerResponse->isOk()) {
        $n_update = count($ServerResponse->getResult());
        print(date('Y-m-d H:i:s', time()) . ' - Processed ' . $n_update . ' updates' . "\n");
    } else {
        print(date('Y-m-d H:i:s', time()) . ' - Failed to fetch updates' . "\n");
        echo $ServerResponse->printError() . "\n";
    }
} catch (Exception\TelegramException $e) {
    echo $e;
    TelegramLog::error($e);
} catch (Exception\TelegramLogException $e) {
    echo $e;
}
