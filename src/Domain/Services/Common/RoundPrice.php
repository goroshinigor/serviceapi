<?php

namespace App\Domain\Services\Common;

use App\Domain\ValueObjects\Price\Price52;
use App\Domain\ValueObjects\Price\IPrice;

/**
 * Description of RoundPrice
 *
 * @author i.goroshyn
 */
class RoundPrice {

    private const ROUND_PRECISION = 0;

    public function round(IPrice $price): IPrice
    {
        return new Price52(round($price->getPrice(),self::ROUND_PRECISION));
    }
}
