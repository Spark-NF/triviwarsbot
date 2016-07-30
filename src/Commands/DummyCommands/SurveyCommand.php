<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use TriviWars\Commands\DummyCommand;

/**
 * Dummy "/survey" command
 */
class SurveyCommand extends DummyCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'survey';
    protected $description = 'Survery for bot users';
    protected $usage = '/survey';
    protected $version = '0.2.0';
    protected $need_mysql = true;
    protected $enabled = false;
    /**#@-*/
}
