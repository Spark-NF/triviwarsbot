<?php
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

require __DIR__ . '/../../vendor/autoload.php';
$config = require __DIR__ . '/../../config/config.php';

$doctrineConfig = Setup::createAnnotationMetadataConfiguration($config['entities'], false, null, null, false);
$conn = array(
    'driver' => 'pdo_mysql',
    'host' => $config['mysql']['host'],
    'user' => $config['mysql']['user'],
    'password' => $config['mysql']['password'],
    'dbname' => $config['mysql']['database'],
);
$entityManager = EntityManager::create($conn, $doctrineConfig);

// Support enum
$platform = $entityManager->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'string');