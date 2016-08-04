<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use TriviWars\Req;
use TriviWars\DB\TriviDB;
use TriviWars\Entity\PlanetBuilding;
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
        $planet = $em->getRepository('TW:Planet')->findOneBy(array('player' => $em->getReference('TW:Player', $user_id)));

        $command = trim($message->getText(true));
        if ($command != '🏭 Buildings') {
            if ($command == '🔙 Back') {
                $conversation->cancel();
                return $this->telegram->executeCommand('status');
            }

            // Get buildings
            $building = $em->getRepository('TW:Building')->findOneBy(array('name' => $command));
            if (empty($building)) {
                Req::error($chat_id, 'Invalid building name');
            }

            $planetBuilding = $em->getRepository('TW:PlanetBuilding')->findOneBy(array('planet' => $planet, 'building' => $building));
            if (empty($planetBuilding)) {
                $planetBuilding = new PlanetBuilding();
                $planetBuilding->setBuilding($building);
                $planetBuilding->setPlanet($planet);
                $planetBuilding->setLevel(0);
            }

            $planetBuilding->setLevel($planetBuilding->getLevel() + 1);
            $em->merge($planetBuilding);

            // TODO: remove resources
            $em->flush();

            $conversation->stop();

            Req::success($chat_id, 'Up '.$building->getName());
            return $this->telegram->executeCommand('status');
        }

        // Get buildings and their levels
        $buildings = $em->getRepository('TW:Building')->findAll();
        $planetBuildings = $em->getRepository('TW:PlanetBuilding')->findBy(array('planet' => $planet));
        $levels = [];
        foreach ($planetBuildings as $building) {
            $b = $building->getBuilding();
            if (empty($b)) {
                continue;
            }
            $levels[$b->getId()] = $building->getLevel();
        }

        // Generate reply text
        $text = '';
        foreach ($buildings as $i => $building) {
            if ($i > 0) {
                $text .= "\n\n";
            }

            $id = $building->getId();
            $currentLevel = isset($levels[$id]) ? $levels[$id] : 0;
            $price = $building->getPriceForLevel($currentLevel + 1);
            $conso = $building->getConsumptionForLevel($currentLevel + 1);
            $consoCurrent = $building->getConsumptionForLevel($currentLevel);
            $prod = $building->getProductionForLevel($currentLevel + 1);
            $prodCurrent = $building->getProductionForLevel($currentLevel);
            $energy = $building->getEnergyForLevel($currentLevel + 1);
            $energyCurrent = $building->getEnergyForLevel($currentLevel);

            // Name
            $line1 = $building->getName().' ('.($currentLevel + 1).') -';

            // Production
            if (!empty($prod[0])) {
                $line1 .= ' 💰'.$prod[0];
                if ($prod[0] != $prodCurrent[0]) {
                    $line1 .= ' (+'.($prod[0] - $prodCurrent[0]).')';
                } else {
                    $line1 .= ' (=)';
                }
            }
            if (!empty($prod[1])) {
                $line1 .= ' 🌽'.$prod[1];
                if ($prod[1] != $prodCurrent[1]) {
                    $line1 .= ' (+'.($prod[1] - $prodCurrent[1]).')';
                } else {
                    $line1 .= ' (=)';
                }
            }
            if (!empty($energy)) {
                $line1 .= ' ⚡️'.$energy;
                if ($energy != $energyCurrent) {
                    $line1 .= ' (+'.($energy - $energyCurrent).')';
                } else {
                    $line1 .= ' (=)';
                }
            }

            // Price
            $line2 = '';
            if (!empty($price[0])) {
                $line2 .= (!empty($line2) ? ' ' : '') . '💰'.$price[0];
            }
            if (!empty($price[1])) {
                $line2 .= (!empty($line2) ? ' ' : '') . '🌽'.$price[1];
            }
            if (!empty($conso)) {
                $line2 .= (!empty($line2) ? ' ' : '') . '⚡️'.$conso;
                if ($conso != $consoCurrent) {
                    $line2 .= ' (+'.($conso - $consoCurrent).')';
                } else {
                    $line2 .= ' (=)';
                }
            }
            $text .= $line1 . "\n" . $line2;
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
