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
        $duration = $this->finish->getTimestamp() - $time;

        $ret = '';
        if ($duration > 24 * 3600) {
            $days = floor($duration / (24 * 3600));
            $duration -= $days * 24 * 3600;
            $ret .= $days.'d';
        }
        if ($duration > 3600) {
            $hours = floor($duration / 3600);
            $duration -= $hours * 3600;
            $ret .= $hours.'h';
        }
        if ($duration > 60) {
            $mins = floor($duration / 60);
            $duration -= $mins * 60;
            $ret .= $mins.'m';
        }
        $ret .= $duration.'s';
        return $ret;
    }
}
