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
    protected $consumption_a;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=false)
     **/
    protected $consumption_b;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=false)
     **/
    protected $production_1_a;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=false)
     **/
    protected $production_1_b;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=false)
     **/
    protected $production_2_a;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=false)
     **/
    protected $production_2_b;

    /**
     * @var float
     * @ORM\Column(name="production_energy_a", type="float", nullable=false)
     **/
    protected $productionEnergyA;

    /**
     * @var float
     * @ORM\Column(name="production_energy_b", type="float", nullable=false)
     **/
    protected $productionEnergyB;

    public function getPriceForLevel($level)
    {
        return [
            floor($this->cost_1_a * pow($this->cost_1_b, $level - 1)),
            floor($this->cost_2_a * pow($this->cost_2_b, $level - 1)),
        ];
    }

    public function getConsumptionForLevel($level)
    {
        return floor($this->consumption_a * $level * pow($this->consumption_b, $level));
    }

    public function getProductionForLevel($level)
    {
        return [
            floor($this->production_1_a * $level * pow($this->production_1_b, $level)),
            floor($this->production_2_a * $level * pow($this->production_2_b, $level)),
        ];
    }

    public function getEnergyForLevel($level)
    {
        return floor($this->productionEnergyA * $level * pow($this->productionEnergyB, $level));
    }

    public function getDurationForLevel($level)
    {
        $price = $this->getPriceForLevel($level);

        // Formula for duration in hours
        $duration = ($price[0] + $price[1]) / (2500);
        $duration *= 3600;

        return $duration;
    }

    public static function durationToString($duration)
    {
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
        $ret .= floor($duration).'s';
        return $ret;
    }
}
