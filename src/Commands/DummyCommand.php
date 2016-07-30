<?php
namespace TriviWars\Commands;

use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Request;

abstract class DummyCommand extends Command
{
    public function execute()
    {
        return Request::emptyResponse();
    }
}
