#!/usr/bin/env php
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//README
//This configuration file is intented to run the bot with the webhook method
//Uncommented parameters must be filled

//bash script
//while true; do ./getUpdatesCLI.php; done

// Load composer
require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/config.php';

$commands_path = __DIR__ . '/../src/Commands/';

try {
    $telegram = new Longman\TelegramBot\Telegram($config['bot']['api_key'], $config['bot']['name']);
    
    // MySQL
    $telegram->enableMySQL($config['mysql'], $config['mysql']['prefix']);

    // Admins
    $telegram->enableAdmins($config['admins']);
    
    // Commands
    foreach ($config['commands']['system'] as $dir) {
        $telegram->addCommandsPath($dir);
    }
    if ($telegram->isAdmin()) {
        foreach ($config['commands']['admin'] as $dir) {
            $telegram->addCommandsPath($dir, false);
        }
    }
    foreach ($config['commands']['user'] as $dir) {
        $telegram->addCommandsPath($dir, false);
    }

    // Command-specific parameters
    /*$telegram->setCommandConfig('sendtochannel', ['your_channel' => '@type_here_your_channel']);
    $telegram->setCommandConfig('date', ['google_api_key' => 'your_google_api_key_here']);*/

    // Logging
    /*\Longman\TelegramBot\TelegramLog::initialize($your_external_monolog_instance);
    \Longman\TelegramBot\TelegramLog::initErrorLog($config['log']['error']);
    \Longman\TelegramBot\TelegramLog::initDebugLog($config['log']['debug']);
    \Longman\TelegramBot\TelegramLog::initUpdateLog($config['log']['update']);*/

    // Custom upload and download path
    /*$telegram->setDownloadPath('../Download');
    $telegram->setUploadPath('../Upload');*/

    // Botan.io integration
    //$telegram->enableBotan('your_token');

    // Handle telegram getUpdate request
    $ServerResponse = $telegram->handleGetUpdates();

    if ($ServerResponse->isOk()) {
        $n_update = count($ServerResponse->getResult());
        print(date('Y-m-d H:i:s', time()) . ' - Processed ' . $n_update . ' updates' . "\n");
    } else {
        print(date('Y-m-d H:i:s', time()) . ' - Failed to fetch updates' . "\n");
        echo $ServerResponse->printError() . "\n";
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e;
    // log telegram errors
    \Longman\TelegramBot\TelegramLog::error($e);
} catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
    //catch log initilization errors
    echo $e;
}
