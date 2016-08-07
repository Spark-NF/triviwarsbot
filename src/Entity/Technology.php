<?php
namespace TriviWars\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="technology")
 */
class Technology extends BaseEntity
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
     * @var string
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    protected $code;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     **/
    protected $order;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=false)
     **/
    protected $cost_1_a;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=false)
     **/
    protected $cost_1_b;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=false)
     **/
    protected $cost_2_a;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=false)
     **/
    protected $cost_2_b;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=false)
     **/
    protected $cost_3_a;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=false)
     **/
    protected $cost_3_b;

    public function getPriceForLevel($level)
    {
        return [
            floor($this->cost_1_a * pow($this->cost_1_b, $level - 1)),
            floor($this->cost_2_a * pow($this->cost_2_b, $level - 1)),
            floor($this->cost_3_a * pow($this->cost_3_b, $level - 1)),
        ];
    }

    public function getDurationForLevel($level)
    {
        $price = $this->getPriceForLevel($level);

        // Formula for duration in hours
        $duration = ($price[0] + $price[1] + $price[2]) / (2500);
        $duration *= 3600;

        return $duration;
    }
}
