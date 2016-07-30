<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use TriviWars\Commands\DummyCommand;

/**
 * Dummy "/echo" command
 */
class EchoCommand extends DummyCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'echo';
    protected $description = 'Show text';
    protected $usage = '/echo <text>';
    protected $version = '1.0.1';
    protected $enabled = false;
    /**#@-*/
}
