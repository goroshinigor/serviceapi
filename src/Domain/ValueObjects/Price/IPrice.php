<?php

namespace App\Domain\ValueObjects\Price;

/**
 * Interface IPrice
 * @author i.goroshyn
 */
interface IPrice {
    public function getPrice(): float;
    public function checkPriceFormat(float $price): bool;
}
