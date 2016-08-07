<?php
define('ENV', getenv('APP_ENV') ?: 'production');
define('DEBUG', ENV == 'development');
if (DEBUG) {
    ini_set('display_errors',1);
    ini_set('display_startup_errors',1);
    error_reporting(-1);
    define('REQUEST_MICROTIME', microtime(true));
}

require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/config.php';
//require __DIR__ . '/../src/error_handler.php';

use TriviWars\Telegram;
use Longman\TelegramBot\TelegramLog;
use Longman\TelegramBot\Exception;

try {
    $telegram = new Telegram($config);
    $telegram->handle();
} catch (Exception\TelegramException $e) {
    TelegramLog::error($e);
} catch (Exception\TelegramLogException $e) {
    echo $e;
}
