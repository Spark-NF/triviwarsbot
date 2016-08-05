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

        // Get current planet
        $em = TriviDB::getEntityManager();
        /** @var Planet $planet */
        $planet = $em->getRepository('TW:Planet')->findOneBy(array('player' => $em->getReference('TW:Player', $user_id)));
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
        foreach ($planet->getConstructionBuildings() as $c) {
            $constructions[] = $c->getBuilding()->getName().' ('.$c->getRemainingTime(time()).')';
        }

        $researchs = [];
        $ships = [];

        $text = '🌍 *'.$planet->getName().'* (5-120-7)' . "\n\n" .
            '💰 '.number_format(floor($planet->getResource1())).' ('.number_format($prod[0] * $factor).'/h)' . "\n" .
            '🌽 '.number_format(floor($planet->getResource2())).' ('.number_format($prod[1] * $factor).'/h)' . "\n" .
            '⚡ '.number_format($conso).'/'.number_format($energy).' ('.number_format($energy - $conso). ($factor < 1 ? ', '.round($factor * 100).'%' : '') . ')' . "\n\n";
        $text .= '*Constructions* ('.count($constructions).'/'.$planet->getMaxConstructions().')';
        if (empty($constructions)) {
            $text .= ': _N/A_';
        } else {
            foreach ($constructions as $construction) {
                $text .= "\n" . '- '.$construction;
            }
        }
        if (!empty($constructions) || !empty($researchs)) {
            $text .= "\n";
        }
        $text .= "\n" . 'Research: _N/A_';
        if (!empty($researchs) || !empty($ships)) {
            $text .= "\n";
        }
        $text .= "\n" . 'Shipyard: _N/A_';

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

    private function showList()
    {
        $ret = '';
        if (empty($constructions)) {
            $ret .= ': _N/A_';
        } else {
            foreach ($constructions as $construction) {
                $ret .= "\n" . '- '.$construction;
            }
        }
        if (!empty($constructions) || !empty($researchs)) {
            $ret .= "\n";
        }
    }
}
