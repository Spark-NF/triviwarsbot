<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use TriviWars\Req;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\SystemCommand;

/**
 * Generic message command
 */
class GenericmessageCommand extends SystemCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'Genericmessage';
    protected $description = 'Handle generic message';
    protected $version = '1.0.2';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $message = $this->getMessage();
        $user_id = $message->getFrom()->getId();
        $chat_id = $message->getChat()->getId();
        
        $conversation = new Conversation($user_id, $chat_id);
        if ($conversation->exists() && ($command = $conversation->getCommand())) {
            return $this->telegram->executeCommand($command, $this->update);
        }
        
        $assoc = [
            '🏭 Buildings' => 'build',
            '🔙 Back' => 'status',
        ];
        
        $msg = $message->getText();
        if (isset($assoc[$msg])) {
            return $this->telegram->executeCommand($assoc[$msg], $this->update);
        }
        
        return Req::emptyResponse();
    }
}
