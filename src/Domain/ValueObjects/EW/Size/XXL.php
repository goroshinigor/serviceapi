<?php

namespace App\Domain\ValueObjects\EW\Size;

use App\Domain\ValueObjects\EW\Size\EwSize;
use App\Domain\ValueObjects\Price\Price52;

/**
 * class XXL
 */
class XXL extends EwSize
{
    /**
     * Predefined values for XXL package.
     * NAME.
     */
    const NAME = 'XXL';

    /**
     * PRICE_TOWN.
     */
    const PRICE_TOWN = 65.00;

    /**
     * PRICE_COUNTRY.
     */
    const PRICE_COUNTRY = 75.00;

    /**
     * WEIGHT_A.
     */
    const WEIGHT_A = 10.01;

    /**
     * WEIGHT_B.
     */
    const WEIGHT_B = 15.00;

    /**
     * LENGTH_A.
     */
    const LENGTH_A = 61;

    /**
     * LENGTH_B.
     */
    const LENGTH_B = 90;

    /**
     * Constructor
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
