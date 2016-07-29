<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

/**
 * Status command
 */
class StatusCommand extends SystemCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'status';
    protected $description = 'Check the status of the current planet';
    protected $usage = '/status';
    protected $version = '1.0.0';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();

        $text = '🌍 *Planet X* (5-120-7)' . "\n\n" .
            'Resources: 💰100 🌽100 💎100 👨40' . "\n" .
	        'Constructions: _N/A_' . "\n" .
	        'Research: _N/A_' . "\n" .
	        'Recruitment: _N/A_';

        return Request::sendMessage([
            'chat_id'       => $chat_id,
            'parse_mode'    => 'MARKDOWN',
            'text'          => $text,
        ]);
    }
}
