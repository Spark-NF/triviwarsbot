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

class BuildCommand extends UserCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'build';
    protected $description = 'Manage buildings on the planet';
    protected $usage = '/build';
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

        $conversation = new Conversation($user_id, $chat_id, 'build');
        $em = TriviDB::getEntityManager();

        // Get current planet
        /** @var Planet $planet */
        $planet = $em->getRepository('TW:Planet')->findOneBy(array('player' => $em->getReference('TW:Player', $user_id), 'active' => true));
        $planet->update($em);
        $em->merge($planet);
        $em->flush();

        // If the command is not for the list, it's an upgrade
        $command = trim($message->getText(true));
        if ($command != '🏭 Buildings') {
            // Go back to status command
            if ($command == '🔙 Back') {
                $conversation->cancel();
                return $this->telegram->executeCommand('status');
            }

            // Cannot build too many buildings at once
            $countConstructions = count($planet->getConstructionBuildings());
            if ($countConstructions >= $planet->getMaxConstructions()) {
                return Req::error($chat_id, 'You reached your concurrent building limit');
            }

            // Get buildings
            $building = $em->getRepository('TW:Building')->findOneBy(array('name' => $command));
            if (empty($building)) {
                return Req::error($chat_id, 'Invalid building name');
            }

            // Check for another construction order
            $constructionBuilding = $em->getRepository('TW:ConstructionBuilding')->findOneBy(array('planet' => $planet, 'building' => $building));
            if (!empty($constructionBuilding)) {
                return Req::error($chat_id, 'This building is already under construction');
            }

            // Get current building level or create it if not found
            $planetBuilding = $em->getRepository('TW:PlanetBuilding')->findOneBy(array('planet' => $planet, 'building' => $building));
            if (empty($planetBuilding)) {
                $planetBuilding = new PlanetBuilding();
                $planetBuilding->setBuilding($building);
                $planetBuilding->setPlanet($planet);
                $planetBuilding->setLevel(0);
            }

            // Pay the cost
            $price = $building->getPriceForLevel($planetBuilding->getLevel() + 1);
            if (!$planet->canPay($price)) {
                return Req::error($chat_id, 'You do not have enough resources');
            }
            $planet->pay($price);
            $em->merge($planet);

            // Upgrade duration
            $duration = ($price[0] + $price[1]) / (2500);
            $duration *= 3600;

            // Update building level
            $construction = new ConstructionBuilding();
            $construction->setPlanet($planet);
            $construction->setBuilding($building);
            $construction->setLevel($planetBuilding->getLevel() + 1);
            $construction->setFinish(new \DateTime(date('c', time() + $duration)));
            $em->persist($construction);

            $planet->addConstructionBuilding($construction);
            $em->merge($planet);

            $em->flush();

            $conversation->stop();

            Req::success($chat_id, 'Started upgrading '.$building->getName());
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

        // Get buildings under construction
        $constructionBuildings = $planet->getConstructionBuildings();
        $constructions = [];
        foreach ($constructionBuildings as $c) {
            $constructions[$c->getBuilding()->getId()] = $c;
        }

        // Generate reply text
        $text = '';
        foreach ($buildings as $i => $building) {
            if ($i > 0) {
                $text .= "\n\n";
            }

            // Get building information and costs
            $id = $building->getId();
            $currentLevel = isset($levels[$id]) ? $levels[$id] : 0;
            $price = $building->getPriceForLevel($currentLevel + 1);
            $conso = $building->getConsumptionForLevel($currentLevel + 1);
            $consoCurrent = $building->getConsumptionForLevel($currentLevel);
            $prod = $building->getProductionForLevel($currentLevel + 1);
            $prodCurrent = $building->getProductionForLevel($currentLevel);
            $energy = $building->getEnergyForLevel($currentLevel + 1);
            $energyCurrent = $building->getEnergyForLevel($currentLevel);

            // Production
            $production  = $this->displayUnit('💰', $prod[0], $prodCurrent[0], 'h');
            $production .= $this->displayUnit('🌽', $prod[1], $prodCurrent[1], 'h');
            $production .= $this->displayUnit('⚡', $energy, $energyCurrent);

            // Price
            $cost  = $this->displayUnit('💰', $price[0]);
            $cost .= $this->displayUnit('🌽', $price[1]);
            $cost .= $this->displayUnit('⚡', $conso, $consoCurrent);

            $text .= '*'.$building->getName().'* ('.(isset($constructions[$building->getId()]) ? 'under construction' : $currentLevel.' > '.($currentLevel + 1)).')';
            if (!empty($production)) {
                $text .= "\n" . '- Production:'.$production;
            }
            $text .= "\n" . '- Cost:' . $cost;
        }

        // Generate keyboard with 3 buildings per line
        $keyboard = [];
        $curr = [];
        $i = 0;
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
            'one_time_keyboard' => false,
            'selective'         => false,
        ]);
        return Req::send($chat_id, $text, $markup);
    }

    protected function displayUnit($unit, $prod, $current = null, $period = null)
    {
        $ret = '';
        if (!empty($prod)) {
            $ret .= ' '.$unit.$prod.(!empty($period) ? '/'.$period : '');
            if ($current !== null) {
                if ($prod != $current) {
                    $ret .= ' (+'.($prod - $current).')';
                } else {
                    $ret .= ' (=)';
                }
            }
        }
        return $ret;
    }
}
