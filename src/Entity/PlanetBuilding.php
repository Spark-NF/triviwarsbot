<?php
namespace TriviWars\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="planet_building")
 */
class PlanetBuilding extends BaseEntity
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var Planet
     * @ORM\ManyToOne(targetEntity="Planet", inversedBy="buildings", fetch="LAZY")
     * @ORM\JoinColumn(name="planet_id", referencedColumnName="id")
     **/
    protected $planet;

    /**
     * @var Building
     * @ORM\ManyToOne(targetEntity="Building", fetch="LAZY")
     * @ORM\JoinColumn(name="building_id", referencedColumnName="id")
     **/
    protected $building;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $level;
}
