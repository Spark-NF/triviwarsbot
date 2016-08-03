<?php
namespace TriviWars\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="building")
 */
class Building extends BaseEntity
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
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     **/
    protected $cost_1_a;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     **/
    protected $cost_1_b;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     **/
    protected $cost_2_a;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     **/
    protected $cost_2_b;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     **/
    protected $consumption_a;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     **/
    protected $consumption_b;
    
    public function getPriceForLevel($level)
    {
        return [
            floor($this->cost_1_a * pow($this->cost_1_b, $level - 1)),
            floor($this->cost_2_a * pow($this->cost_2_b, $level - 1)),
        ];
    }
    
    public function getConsumptionForLevel($level)
    {
        return floor($this->consumption_a * $level * pow($this->consumption_a, $level));
    }
}
