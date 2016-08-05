<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use TriviWars\Entity\ConstructionBuilding;
use TriviWars\Entity\Planet;
use TriviWars\Req;
use TriviWars\DB\TriviDB;
use TriviWars\Entity\PlanetBuilding;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ReplyKeyboardMarkup;
use Longman\TelegramBot\Conversation;

class SwitchCommand extends UserCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'switch';
    protected $description = 'Change the planet you want to manage';
    protected $usage = '/switch';
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

        $conversation = new Conversation($user_id, $chat_id, 'switch');
        $em = TriviDB::getEntityManager();

        // Get all player's planets
        $planets = $em->getRepository('TW:Planet')->findBy(array('player' => $em->getReference('TW:Player', $user_id)));

        // If the command is not for the list, it's an upgrade
        $command = trim($message->getText(true));
        if ($command != '🌍 Switch') {
            // Go back to status command
            if ($command == '🔙 Back') {
                $conversation->cancel();
                return $this->telegram->executeCommand('status');
            }

            // Get buildings
            $planet = $em->getRepository('TW:Planet')->findOneBy(array('name' => $command));
            if (empty($planet)) {
                return Req::error($chat_id, 'Invalid planet name');
            }

            // De-active all player's planets
            $em->createQuery('UPDATE TW:Planet p SET p.active = false WHERE p.player = :player')
                ->setParameter('player', $em->getReference('TW:Player', $user_id))
                ->execute();

            // Set new planet as active
            $planet->setActive(true);
            $em->merge($planet);
            $em->flush();

            $conversation->stop();

            Req::success($chat_id, 'Switched to planet '.$planet->getName());
            return $this->telegram->executeCommand('status');
        }

        $text = 'Which planet do you want to switch to?';

        $keyboard = [];
        foreach ($planets as $planet) {
            if (!$planet->isActive()) {
                $keyboard[] = [$planet->getName()];
            }
        }
        $keyboard[] = ['🔙 Back'];

        $markup = new ReplyKeyboardMarkup([
            'keyboard'          => $keyboard,
            'resize_keyboard'   => true,
            'one_time_keyboard' => false,
            'selective'         => false,
        ]);
        return Req::send($chat_id, $text, $markup);
    }
}
