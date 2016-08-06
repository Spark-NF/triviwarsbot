<?php
namespace TriviWars\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="construction_building")
 */
class ConstructionBuilding extends BaseEntity
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
     * @ORM\ManyToOne(targetEntity="Planet", inversedBy="constructionBuildings", fetch="LAZY")
     * @ORM\JoinColumn(name="planet_id", referencedColumnName="id")
     **/
    protected $planet;

    /**
     * @var Building
     * @ORM\ManyToOne(targetEntity="Building", fetch="EAGER")
     * @ORM\JoinColumn(name="building_id", referencedColumnName="id")
     **/
    protected $building;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $level;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $duration;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $finish;

    public function isFinished()
    {
        return $this->finish->getTimestamp() <= time();
    }

    public function getRemainingTime($time)
    {
        return $this->finish->getTimestamp() - $time;
    }
}
