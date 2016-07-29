<?php
require_once __DIR__ . '/../src/Commands/DummyCommand.php';

return [
    'bot' => [
        'api_key'   => '249542397:AAHFYmpEeugwhcewA937mlU-wIAj46YobiQ',
        'name'      => 'triviwarsbot',
        'hook_url'  => 'https://bot.nicolas-faure.fr/hook.php',
        //'certificate' => '.',
    ],
    'mysql' => [
        'host'      => 'localhost',
        'user'      => 'triviwars',
        'password'  => 'raokwher',
        'database'  => 'triviwarsbot',
        'prefix'    => '',
    ],
    'log' => [
        'error'     => __DIR__ . '/../log/error.log',
        'debug'     => __DIR__ . '/../log/debug.log',
        'update'    => __DIR__ . '/../log/update.log',
    ],
    'commands' => [
        'admin' => [
            __DIR__ . '/../src/Commands/AdminCommands/',
        ],
        'user' => [
            __DIR__ . '/../src/Commands/UserCommands/',
            __DIR__ . '/../src/Commands/DummyCommands/',
        ]
    ],
    'admins' => [
        63496576, // SparkNF
        51336466, // lelimacon
    ],
];