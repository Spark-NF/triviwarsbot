<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use TriviWars\Req;
use TriviWars\DB\TriviDB;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\ReplyKeyboardMarkup;
use Longman\TelegramBot\Conversation;

class BuildCommand extends UserCommand
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
        $user_id = $message->getFrom()->getId();
        $chat_id = $message->getChat()->getId();
        $em = TriviDB::getEntityManager();
        
        $conversation = new Conversation($user_id, $chat_id, 'build');
        
        $command = trim($message->getText(true));
        if ($command != '🏭 Buildings') {
            // Get buildings
            $building = $em->getRepository('TriviWars\\Entity\\Building')->findOneBy(array('name' => $command));
            if (empty($building)) {
                Req::error($chat_id, 'Invalid building name');
            }
        
            $conversation->stop();
        
            Req::success($chat_id, 'Up '.$building->getName());
            return $this->telegram->executeCommand('status');
        }
        
        // Get buildings
        $buildings = $em->getRepository('TriviWars\\Entity\\Building')->findAll();

        // Generate reply text
        $text = '';
        foreach ($buildings as $i => $building) {
            if ($i > 0) {
                $text .= "\n";
            }
            
            $currentLevel = 0;
            $price = $building->getPriceForLevel($currentLevel + 1);
            $conso = $building->getConsumptionForLevel($currentLevel + 1);
            
            $text .= $building->getName().' ('.($currentLevel + 1).') - 💰'.$price[0].' 🌽'.$price[1].' ⚡️'.$conso;
        }

        // Generate keyboard with 3 buildings per line
        $keyboard = [];
        $curr = [];
        foreach ($buildings as $i => $building) {
            $curr[] = $building->getName();
        
            if ($i % 3 == 2 && $i != 0) {
                $keyboard[] = $curr;
                $curr = [];
            }
        }
        if ($i % 3 != 2) {
            $keyboard[] = $curr;
        }
        $keyboard[] = ['🔙 Back'];

        $markup = new ReplyKeyboardMarkup([
            'keyboard'          => $keyboard,
            'resize_keyboard'   => true,
            'one_time_keyboard' => true,
            'selective'         => false
        ]);
        return Req::send($chat_id, $text, $markup);
    }
}
