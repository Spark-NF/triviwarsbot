<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use TriviWars\Commands\DummyCommand;

/**
 * Dummy "/slap" command
 */
class SlapCommand extends DummyCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'slap';
    protected $description = 'Slap someone with their username';
    protected $usage = '/slap <@user>';
    protected $version = '1.0.1';
    protected $enabled = false;
    /**#@-*/
}
