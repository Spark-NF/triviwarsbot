<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

/**
 * Start command
 */
class StartCommand extends SystemCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'start';
    protected $description = 'Start command';
    protected $usage = '/start';
    protected $version = '1.0.1';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $message = $this->getMessage();

        $chat = $message->getChat();
        $chat_id = $chat->getId();
        $username = $chat->getUsername();

        $text = "\xF0\x9F\x93\xA8" . ' *New message received*' . "\n\n" .
            'Hello '.$username.'!' . "\n\n" .
            'You have been selected to be the leader of a new colony on Planet X. Congratulations!' . "\n" .
            'We sent you '."\xF0\x9F\x92\xB0".'100, '."\xF0\x9F\x8C\xBD".'100, '."\xF0\x9F\x92\x8E".'100, and '."\xF0\x9F\x91\xA8".'40 to help you start your colony.' . "\n" .
            'Make good use of it!' . "\n\n" .
            'Regards,' . "\n" .
            'CyberCorp Corporation' . "\n\n" .
            '_PS: if you\'re ever lost, remember you can type_ /help _to see all commands!_';

        return Request::sendMessage([
            'chat_id'       => $chat_id,
            'parse_mode'    => 'MARKDOWN',
            'text'          => $text,
        ]);
    }
}
