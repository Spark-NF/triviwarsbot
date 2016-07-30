<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use TriviWars\Commands\DummyCommand;

/**
 * Dummy "/date" command
 */
class DateCommand extends DummyCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'date';
    protected $description = 'Show date/time by location';
    protected $usage = '/date <location>';
    protected $version = '1.3.0';
    protected $enabled = false;
    /**#@-*/
}
