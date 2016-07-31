<?php
namespace TriviWars\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="player")
 */
class Player extends BaseEntity
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(name="chat_id", type="integer", nullable=false)
     */
    protected $chatId;

    /**
     * @var Planet[]
     * @ORM\OneToMany(targetEntity="Planet", mappedBy="player")
     */
    protected $planets;

    public function __construct()
    {
        $this->planets = new ArrayCollection();
    }
}
