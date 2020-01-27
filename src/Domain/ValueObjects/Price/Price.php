<?php

namespace App\Domain\ValueObjects\Price;

use App\Domain\ValueObjects\Price\IPrice;

/**
 * 
 */
abstract class Price implements IPrice
{
    private $price;
    /**
     * 
     */
    public function __construct(float $price, $locale = 'uk_UA')
    {
        $this->checkPriceFormat($price);
        $this->price = $price;
//        setlocale(LC_MONETARY, $locale);
////        $this->price = \money_format('%i', $price);
    }

    /**
     * 
     * @return type
     */
    public function getPrice(): float
    {
        return (float)$this->price;
    }

    /**
     * 
     * @return type
     */
    public function setPrice(Price $price): IPrice
    {
        $this->price = $price;

        return $this;
    }

    /**
     * checkPriceFormat
     */
    public function checkPriceFormat(float $price): bool
    {
        if($price < 0) {
            throw new \Exception('Income Value cannot be less than zero!');
        }

        return true;
    }
}
