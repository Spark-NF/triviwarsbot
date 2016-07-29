<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

/**
 * Generic command
 */
class GenericCommand extends SystemCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'Generic';
    protected $description = 'Handles generic commands or is executed by default when a command is not found';
    protected $version = '1.0.1';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $message = $this->getMessage();

        $chat_id = $message->getChat()->getId();
        $user_id = $message->getFrom()->getId();
        $command = $message->getCommand();

        $text = 'The command /' . $command . ' does not exit.';

        return Request::sendMessage([
            'chat_id' => $chat_id,
            'text'    => $text,
        ]);
    }
}
