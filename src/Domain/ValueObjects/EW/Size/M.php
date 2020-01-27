<?php

namespace App\Domain\ValueObjects\EW\Size;

use App\Domain\ValueObjects\EW\Size\EwSize;
use App\Domain\ValueObjects\Price\Price52;

/**
 * class M
 */
class M extends EwSize
{
    /**
     * Predefined values for M package.
     * NAME.
     */
    const NAME = 'M';

    /**
     * PRICE_TOWN.
     */
    const PRICE_TOWN = 35.00;

    /**
     * PRICE_COUNTRY.
     */
    const PRICE_COUNTRY = 42.00;

    /**
     * WEIGHT_A.
     */
    const WEIGHT_A = 1.01;

    /**
     * WEIGHT_B.
     */
    const WEIGHT_B = 2.00;

    /**
     * LENGTH_A.
     */
    const LENGTH_A = 1;

    /**
     * LENGTH_B.
     */
    const LENGTH_B = 40;
    
    public function __construct() {
        parent::__construct(
            self::NAME,
            new Price52(self::PRICE_TOWN),
            new Price52(self::PRICE_COUNTRY),
            self::WEIGHT_A,
            self::WEIGHT_B,
            self::LENGTH_A,
            self::LENGTH_B
        );
    }
}
