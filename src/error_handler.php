<?php
use Longman\TelegramBot\TelegramLog;
use TriviWars\Req;

define('E_FATAL',  E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
define('E_WARN',  E_WARNING |E_COMPILE_WARNING | E_USER_WARNING);

// Catch fatal errors
register_shutdown_function('tw_shutdown');

// Redirect fatal errors to other handler to share code
function tw_shutdown() {
    $error = error_get_last();
    if ($error && ($error['type'])) {
        tw_error_handler($error['type'], $error['message'], $error['file'], $error['line']);
    }
}

// Catch non-fatal errors
set_error_handler('tw_error_handler');

// Write error to the logs
function tw_error_handler($errno, $errstr, $errfile, $errline) {
    if ($errno && (($errno & E_FATAL) || ($errno & E_WARN))) {
        $e = 'Error ' . $errno . ' (' . substr($errfile, 13) . ':' . $errline . '): ' . $errstr;
        TelegramLog::error($e);

        if (DEBUG) {
            Req::send(null, $e);
        }
    }
};