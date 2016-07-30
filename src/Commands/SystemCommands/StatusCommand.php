<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\ReplyKeyboardMarkup;
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

        DB::isDbConnected();

        $text = '🌍 *Planet X* (5-120-7)' . "\n\n" .
            '💰 100 (2/h)' . "\n" .
            '🌽 100 (2/h)' . "\n" .
            '💎 100 (2/h)' . "\n" .
            '👨 40 (1/h)' . "\n\n" .
            'Constructions: _N/A_' . "\n" .
            'Research: _N/A_' . "\n" .
            'Shipyard: _N/A_';

        $keyboard[] = ['🏭 Buildings', '💊 Research', '🚀 Shipyard'];
        $keyboard[] = ['🛡 Defense', '⚔ Fleet', '🌟 Galaxy'];
        $keyboard[] = ['🔃 Switch', '🔧 Manage'];

        $markup = new ReplyKeyboardMarkup([
            'keyboard'          => $keyboard,
            'resize_keyboard'   => true,
            'one_time_keyboard' => false,
            'selective'         => false
        ]);

        return Request::sendMessage([
            'reply_markup'  => $markup,
            'chat_id'       => $chat_id,
            'parse_mode'    => 'MARKDOWN',
            'text'          => $text,
        ]);
    }
}
