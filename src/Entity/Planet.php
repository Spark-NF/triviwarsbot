<?php
namespace TriviWars\Entity;

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
}
