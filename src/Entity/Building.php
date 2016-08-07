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
    protected $production_1_a = 0;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=false)
     **/
    protected $production_1_b = 0;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=false)
     **/
    protected $production_2_a = 0;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=false)
     **/
    protected $production_2_b = 0;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=false)
     **/
    protected $production_3_a = 0;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=false)
     **/
    protected $production_3_b = 0;

    /**
     * @var float
     * @ORM\Column(name="production_energy_a", type="float", nullable=false)
     **/
    protected $productionEnergyA = 0;

    /**
     * @var float
     * @ORM\Column(name="production_energy_b", type="float", nullable=false)
     **/
    protected $productionEnergyB = 0;

    /**
     * @var float
     * @ORM\Column(name="storage_1", type="float", nullable=false)
     **/
    protected $storage1 = 0;

    /**
     * @var float
     * @ORM\Column(name="storage_2", type="float", nullable=false)
     **/
    protected $storage2 = 0;

    /**
     * @var float
     * @ORM\Column(name="storage_3", type="float", nullable=false)
     **/
    protected $storage3 = 0;

    public function getStorageForLevel($level)
    {
        return [
            $this->storage1 * floor(2.5 * exp((20 * $level) / 33)),
            $this->storage2 * floor(2.5 * exp((20 * $level) / 33)),
            $this->storage3 * floor(2.5 * exp((20 * $level) / 33)),
        ];
    }

    public function getPriceForLevel($level)
    {
        return [
            floor($this->cost_1_a * pow($this->cost_1_b, $level - 1)),
            floor($this->cost_2_a * pow($this->cost_2_b, $level - 1)),
            floor($this->cost_3_a * pow($this->cost_3_b, $level - 1)),
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
            floor($this->production_3_a * $level * pow($this->production_3_b, $level)),
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
        $duration = ($price[0] + $price[1] + $price[2]) / (2500);
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
