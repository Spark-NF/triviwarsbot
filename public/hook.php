<?php
require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/config.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use TriviWars\DB\MyTelegram;
use TriviWars\DB\TriviDB;

try {
    $telegram = new MyTelegram($config['bot']['api_key'], $config['bot']['name']);
    
    // MySQL
    $telegram->enableMySql($config['mysql'], $config['mysql']['prefix']);

    // Admins
    $telegram->enableAdmins($config['admins']);

    // Doctrine
    $doctrineConfig = Setup::createAnnotationMetadataConfiguration($config['entities'], false, null, null, false);
    $doctrineConfig->addEntityNamespace('TW', 'TriviWars\\Entity');
    $conn = new Doctrine\DBAL\Connection(array('pdo' => $telegram->getPDO()), new Doctrine\DBAL\Driver\PDOMySql\Driver());
    $entityManager = EntityManager::create($conn, $doctrineConfig);
    TriviDB::setEntityManager($entityManager);
    
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
        $telegram->addCommandsPath($dir);
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

    // Handle telegram webhook request
    $telegram->handle();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Silence is gold!
    //echo $e;
    // log telegram errors
    \Longman\TelegramBot\TelegramLog::error($e);
} catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
    // Silence is gold! Uncomment this to catch log initilization errors
    //echo $e;
}
