<?php
namespace TriviWars\DB;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Driver\PDOMySql\Driver as PDOMySqlDriver;
use TriviWars\DB\TriviDB;
use Longman\TelegramBot\Telegram;

class MyTelegram extends Telegram
{
    public function __construct($config)
    {
        parent::__construct($config['bot']['api_key'], $config['bot']['name']);

        // MySQL
        $this->enableMySql($config['mysql'], $config['mysql']['prefix']);

        // Admins
        $this->enableAdmins($config['admins']);

        // Doctrine
        $doctrineConfig = Setup::createAnnotationMetadataConfiguration($config['entities'], false, null, null, false);
        $doctrineConfig->addEntityNamespace('TW', 'TriviWars\\Entity');
        $conn = new Connection(array('pdo' => $this->getPDO()), new PDOMySqlDriver());
        $entityManager = EntityManager::create($conn, $doctrineConfig);
        TriviDB::setEntityManager($entityManager);

        // Commands
        foreach ($config['commands']['system'] as $dir) {
            $this->addCommandsPath($dir);
        }
        if ($this->isAdmin()) {
            foreach ($config['commands']['admin'] as $dir) {
                $this->addCommandsPath($dir, false);
            }
        }
        foreach ($config['commands']['user'] as $dir) {
            $this->addCommandsPath($dir, false);
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
    }

    public function getPDO()
    {
        return $this->pdo;
    }
}