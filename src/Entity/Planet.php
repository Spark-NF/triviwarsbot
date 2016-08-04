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
        
    }
}
