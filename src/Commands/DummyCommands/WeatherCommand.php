<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Commands\DummyCommand;

/**
 * Dummy "/weather" command
 */
class WeatherCommand extends DummyCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'weather';
    protected $description = 'Show weather by location';
    protected $usage = '/weather <location>';
    protected $version = '1.1.0';
    protected $enabled = false;
    /**#@-*/
}
