<?php
namespace TriviWars\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="planet")
 */
class Planet extends BaseEntity
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var Player
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="planets", fetch="LAZY")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
     **/
    protected $player;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updated;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $active;

    /**
     * @var float
     * @ORM\Column(name="resource_1", type="float", nullable=false)
     **/
    protected $resource1;

    /**
     * @var float
     * @ORM\Column(name="resource_2", type="float", nullable=false)
     **/
    protected $resource2;

    /**
     * @var float
     * @ORM\Column(name="resource_3", type="float", nullable=false)
     **/
    protected $resource3;

    /**
     * @var PlanetBuilding[]
     * @ORM\OneToMany(targetEntity="PlanetBuilding", mappedBy="planet")
     */
    protected $buildings;

    /**
     * @var ConstructionBuilding[]
     * @ORM\OneToMany(targetEntity="ConstructionBuilding", mappedBy="planet")
     * @ORM\OrderBy({"finish" = "ASC"})
     */
    protected $constructionBuildings;

    public function __construct()
    {
        $this->buildings = new ArrayCollection();
        $this->constructionBuildings = new ArrayCollection();
    }

    public function getHourlyProduction()
    {
        // Initial resource production
        $prod = [60, 30, 0];
        $energy = 0;
        $conso = 0;

        $buildings = $this->getBuildings();
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

        // If we use more energy than we have, all productions are reduced by this factor
        $factor = $conso == 0 ? 0 : min(1, $energy / $conso);

        return [
            $prod[0] * $factor,
            $prod[1] * $factor,
            $prod[2] * $factor,
        ];
    }

    public function getMaxResources()
    {
        $storage = [0, 0, 0];

        $buildings = $this->getBuildings();
        foreach ($buildings as $l) {
            $level = $l->getLevel();
            $building = $l->getBuilding();
            if (empty($building)) {
                continue;
            }

            $p = $building->getStorageForLevel($level);
            foreach ($p as $i => $v) {
                $storage[$i] += $v;
            }
        }

        foreach ($storage as $i => &$v) {
            $v = max(10000, $v);
        }

        return $storage;
    }

    /**
     * @param EntityManager $em
     */
    public function update($em)
    {
        $from = $this->updated->getTimestamp();

        // Apply finished constructions
        foreach ($this->getConstructionBuildings() as $c) {
            if (!$c->isFinished()) {
                break;
            }

            $to = $c->getFinish()->getTimestamp();
            $this->updateBetween($from, $to);
            $from = $to;

            // Increase building level
            $planetBuilding = $em->getRepository('TW:PlanetBuilding')->findOneBy(['planet' => $this, 'building' => $c->getBuilding()]);
            if (empty($planetBuilding)) {
                $planetBuilding = new PlanetBuilding();
                $planetBuilding->setBuilding($c->getBuilding());
                $planetBuilding->setPlanet($this);
                $planetBuilding->setLevel(1);
                $em->persist($planetBuilding);

                $this->buildings[] = $planetBuilding;
            } else {
                $planetBuilding->setLevel($planetBuilding->getLevel() + 1);
                $em->merge($planetBuilding);
            }
            $em->remove($c);
        }

        $this->updateBetween($from, time());
        $this->setUpdated(new \DateTime('now'));
    }

    protected function updateBetween($from, $to)
    {
        // If for some reason an already finished upgrade is started, we ignore it
        $diff = $to - $from;
        if ($diff < 0) {
            return;
        }

        $prod = $this->getHourlyProduction();
        $hours = $diff / 3600;
        $gain = [
            $prod[0] * $hours,
            $prod[1] * $hours,
            $prod[2] * $hours,
        ];

        $this->gain($gain);
    }

    /**
     * @param array $price
     * @return bool
     */
    public function canPay($price)
    {
        return $this->getResource1() >= $price[0]
            && $this->getResource2() >= $price[1]
            && $this->getResource3() >= $price[2];
    }

    /**
     * @param array $price
     */
    public function pay($price)
    {
        $this->setResource1(max(0, $this->getResource1() - $price[0]));
        $this->setResource2(max(0, $this->getResource2() - $price[1]));
        $this->setResource3(max(0, $this->getResource3() - $price[2]));
    }

    /**
     * @param array $gain
     */
    public function gain($gain)
    {
        $max = $this->getMaxResources();
        $this->setResource1(min($max[0], $this->getResource1() + $gain[0]));
        $this->setResource2(min($max[1], $this->getResource2() + $gain[1]));
        $this->setResource3(min($max[2], $this->getResource3() + $gain[2]));
    }

    /**
     * @return int
     */
    public function getMaxConstructions()
    {
        return 2;
    }
}
