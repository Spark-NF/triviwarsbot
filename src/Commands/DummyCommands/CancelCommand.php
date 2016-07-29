<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Commands\DummyCommand;

/**
 * Dummy "/whoami" command
 */
class CancelCommand extends DummyCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'cancel';
    protected $description = 'Cancel the currently active conversation';
    protected $usage = '/cancel';
    protected $version = '0.1.1';
    protected $need_mysql = true;
    /**#@-*/
}
