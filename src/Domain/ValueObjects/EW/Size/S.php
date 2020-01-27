<?php

namespace App\Domain\ValueObjects\EW\Size;

use App\Domain\ValueObjects\EW\Size\EwSize;
use App\Domain\ValueObjects\Price\Price52;

/**
 * class S
 */
class S extends EwSize
{
    /**
     * Predefined values for S package.
     * NAME.
     */
    const NAME = 'S';

    /**
     * PRICE_TOWN.
     */
    const PRICE_TOWN = 30.00;

    /**
     * PRICE_COUNTRY.
     */
    const PRICE_COUNTRY = 37.00;

    /**
     * WEIGHT_A.
     */
    const WEIGHT_A = 0.51;

    /**
     * WEIGHT_B.
     */
    const WEIGHT_B = 1.00;

    /**
     * LENGTH_A.
     */
    const LENGTH_A = 1;

    /**
     * LENGTH_B.
     */
    const LENGTH_B = 40;
    
    /**
     * Constructor.
     */
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
