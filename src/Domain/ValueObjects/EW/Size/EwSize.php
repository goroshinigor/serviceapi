<?php

namespace App\Domain\ValueObjects\EW\Size;

use App\Domain\ValueObjects\Price\Price;
use App\Domain\ValueObjects\Price\IPrice;
use App\Domain\ValueObjects\EW\Size\ISize;
/*
 * Abstract class EwSize.
 */
abstract class EwSize implements ISize
{
    /**
     * Predefined type (XL\L\XS etc)
     */
    const TYPE_PREDEFINED = 1;

    /**
     * Calculated type(XS\L\XS etc calculated on given params like weight and length)
     */
    const TYPE_CALCULATED = 2;

    /**
     *
     * @var type 
     */
    public $name;

    /**
     *
     * @var type 
     */
    public $priceTown;

    /**
     *
     * @var type 
     */
    public $priceCountry;

    /**
     *
     * @var type 
     */
    public $weightA;

    /**
     *
     * @var type 
     */
    public $weightB;

    /**
     *
     * @var type 
     */
    public $lengthA;

    /**
     *
     * @var type 
     */
    public $lengthB;

    /**
     * 
     * @param type $name
     * @param type $price
     * @param type $weightA
     * @param type $weightB
     * @param type $lengthA
     * @param type $lengthB
     */
    public function __construct(
        string $name,
        IPrice $priceTown,
        IPrice $priceCountry,
        float $weightA,
        float $weightB,
        float $lengthA,
        float $lengthB
    ) {
        $this->setName($name);
        $this->setPriceTown($priceTown);
        $this->setPriceCountry($priceCountry);
        $this->setWeightA($weightA);
        $this->setWeightB($weightB);
        $this->setLengthA($lengthA);
        $this->setLengthB($lengthB);
    }

    /**
     * 
     * @return string
     */
    public final function getName(): string
    {
        return $this->name;
    }

    /**
     * 
     * @return float
     */
    public final function getPriceTown(): IPrice
    {
        return $this->priceTown;
    }

    /**
     * 
     * @return float
     */
    public final function getPriceCountry(): IPrice
    {
        return $this->priceCountry;
    }

    /**
     * 
     * @return float
     */
    public final function getWeightA(): float
    {
        return $this->weightA;
    }

    /**
     * 
     * @return float
     */
    public final function getWeightB(): float
    {
        return $this->weightB;
    }

    /**
     * 
     * @return float
     */
    public final function getLengthA(): float
    {
        return $this->lengthA;
    }

    /**
     * 
     * @return string
     */
    public final function getLengthB(): string
    {
        return $this->lengthB;
    }

    /**
     * 
     * @return string
     */
    public final function setName(string $name): bool
    {
        return $this->name = $name;
    }

    /**
     * 
     * @return bool
     */
    public final function setPriceTown(Price $price): bool
    {
        return (bool)$this->priceTown = $price;
    }

    /**
     * 
     * @return bool
     */
    public final function setPriceCountry(Price $price): bool
    {
        return (bool)$this->priceCountry = $price;
    }

    /**
     * 
     * @return bool
     */
    public final function setWeightA(float $weightA): bool
    {
        return (bool)$this->weightA = $weightA;
    }

    /**
     * 
     * @return bool
     */
    public final function setWeightB(float $weightB): bool
    {
        return (bool)$this->weightB = $weightB;
    }

    /**
     * 
     * @return bool
     */
    public final function setLengthA(float $lengthA): bool
    {
        return (bool)$this->lengthA = $lengthA;
    }

    /**
     * 
     * @return bool
     */
    public final function setLengthB(float $lengthB): bool
    {
        return (bool)$this->lengthB = $lengthB;
    }

    /**
     * 
     * @return bool
     */
    public final function getMaxLength(): int
    {
        if($this->lengthB > $this->lengthA){
            return $this->lengthB;
        }

        return $this->lengthA;
    }

    /**
     * 
     * @return bool
     */
    public final function getWeight(): int
    {
        if($this->weightB > $this->weightA){
            return $this->weightB;
        }

        return $this->weightA;
    }

    /**
     * 
     * @return bool
     */
    public final function getSizeName(): string
    {
        return static::NAME;
    }
}
