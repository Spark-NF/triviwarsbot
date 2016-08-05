<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use TriviWars\Req;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;
use TriviWars\DB\TriviDB;
use TriviWars\Entity\Planet;
use TriviWars\Entity\Player;

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
        $user_id = $message->getFrom()->getId();
        $chat = $message->getChat();
        $chat_id = $chat->getId();
        $username = $chat->getUsername();

        // Check if the player does not already have an account
        $em = TriviDB::getEntityManager();
        $existing = $em->getRepository('TW:Player')->find($user_id);
        if (!empty($existing))
        {
            return Req::error($chat_id, 'You already have a game started');
        }

        // Create a new account and planet for this player
        $player = new Player();
        $player->setId($user_id);
        $planet = new Planet();
        $planet->setPlayer($player);
        $planet->setName('Planet '.rand(100, 999));
        $planet->setActive(true);
        $planet->setResource1(500);
        $planet->setResource2(400);
        $planet->setUpdated(new \DateTime('now'));
        $em->persist($player);
        $em->persist($planet);
        $em->flush();

        // Welcome message
        $text = '📨 *New message received*' . "\n\n" .
            'Hello '.$username.'!' . "\n\n" .
            'You have been selected to be the leader of a new colony on '.$planet->getName().'. Congratulations!' . "\n" .
            'We sent you 💰'.$planet->getResource1().', 🌽'.$planet->getResource2().' to help you start your colony.' . "\n" .
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
