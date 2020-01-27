<?php

namespace App\Domain\ValueObjects\EW\Size;

use App\Domain\ValueObjects\EW\Size\ISize;
use \App\Domain\ValueObjects\Price\IPrice;
/**
 *
 * @author i.goroshyn
 */
interface ISize {
    public function getPriceCountry(): IPrice;
    public function getPriceTown(): IPrice;
    public function getMaxLength(): int;
    public function getWeight(): int;
    public function getSizeName(): string;
}
