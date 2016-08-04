<?php
namespace TriviWars\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @var PlanetBuilding[]
     * @ORM\OneToMany(targetEntity="PlanetBuilding", mappedBy="planet")
     */
    protected $buildings;

    public function __construct()
    {
        $this->buildings = new ArrayCollection();
    }
    
    public function update()
    {
        $prod = array(60, 30);
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

        $factor = $conso == 0 ? 0 : min(1, $energy / $conso);

        $hours = (time() - $this->updated->getTimestamp()) / 3600;
        $gain = array(
            $prod[0] * $factor * $hours,
            $prod[1] * $factor * $hours,
        );

        $this->setResource1($this->getResource1() + $gain[0]);
        $this->setResource2($this->getResource2() + $gain[1]);
        $this->setUpdated(new \DateTime('now'));
    }
}
