<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use TriviWars\Entity\Planet;
use TriviWars\Req;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ReplyKeyboardMarkup;
use TriviWars\DB\TriviDB;

/**
 * Status command
 */
class StatusCommand extends UserCommand
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

        // Get current planet
        /** @var Planet $planet */
        $planet = $em->getRepository('TW:Planet')->findOneBy(array('player' => $em->getReference('TW:Player', $user_id), 'active' => true));
        $planet->update($em);
        $em->merge($planet);
        $em->flush();

        // Get production and consumption of all buildings
        $prod = array(60, 30);
        $energy = 0;
        $conso = 0;
        $buildings = $planet->getBuildings();
        foreach ($buildings as $l) {
            $level = $l->getLevel();
            $building = $l->getBuilding();
            if (empty($building)) {
                continue;
            }

            $p = $building->getProductionForLevel($level);
            foreach ($p as $i => $v) {
                $prod[$i] += $v;
            }

            $energy += $building->getEnergyForLevel($level);
            $conso += $building->getConsumptionForLevel($level);
        }

        // Energy factor
        $factor = $conso == 0 ? 0 : min(1, $energy / $conso);

        // Constructions
        $constructions = [];
        $researchs = [];
        $ships = [];
        $defenses = [];
        foreach ($planet->getConstructionBuildings() as $c) {
            $constructions[] = $c->getBuilding()->getName().' ('.$c->getRemainingTime(time()).')';
        }

        $text = '🌍 *'.$planet->getName().'* (5-120-7)' . "\n\n" .
            '💰 '.number_format(floor($planet->getResource1())).' ('.number_format($prod[0] * $factor).'/h)' . "\n" .
            '🌽 '.number_format(floor($planet->getResource2())).' ('.number_format($prod[1] * $factor).'/h)' . "\n" .
            '⚡ '.number_format($conso).'/'.number_format($energy).' ('.number_format($energy - $conso). ($factor < 1 ? ', '.round($factor * 100).'%' : '') . ')' . "\n\n" .
            $this->showList('Constructions', $constructions, $planet->getMaxConstructions(), $researchs) . "\n" .
            $this->showList('Research', $researchs, 0, $ships) . "\n" .
            $this->showList('Shipyard', $ships, 0, $defenses) . "\n" .
            $this->showList('Defense', $defenses, 0);

        $keyboard = [
            ['🏭 Buildings', '💊 Research', '🚀 Shipyard'],
            ['🛡 Defense', '⚔ Fleet', '🌟 Galaxy'],
            ['🔃 Refresh', '🌍 Switch', '🔧 Manage'],
        ];

        $markup = new ReplyKeyboardMarkup([
            'keyboard'          => $keyboard,
            'resize_keyboard'   => true,
            'one_time_keyboard' => false,
            'selective'         => false
        ]);

        return Req::send($chat_id, $text, $markup);
    }

    private function showList($name, $list, $max, $next = [])
    {
        // Title
        $ret = '*' . $name . '*';

        // Count and limit
        $ret .= ' (' . count($list) . (!empty($max) ? '/' . $max : '') . ')';

        // Actual list
        if (!empty($list)) {
            foreach ($list as $item) {
                $ret .= "\n" . '- ' . $item;
            }
        }

        // If there is a non-empty list afterwards, add a separator
        if (!empty($list) || !empty($next)) {
            $ret .= "\n";
        }

        return $ret;
    }
}
