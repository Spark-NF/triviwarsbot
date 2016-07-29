<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Commands\DummyCommand;

/**
 * Dummy "/whoami" command
 */
class WhoamiCommand extends DummyCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'whoami';
    protected $description = 'Show your id, name and username';
    protected $usage = '/whoami';
    protected $version = '1.0.1';
    protected $public = true;
    protected $enabled = false;
    /**#@-*/
}
