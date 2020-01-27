<?php

namespace App\Domain\ValueObjects\EW\Size;

use App\Domain\ValueObjects\EW\Size\EwSize;
use App\Domain\ValueObjects\Price\Price52;

/**
 * class XL
 */
class XL extends EwSize
{
    /**
     * Predefined values for XL package.
     * NAME.
     */
    const NAME = 'XL';

    /**
     * PRICE_TOWN.
     */
    const PRICE_TOWN = 49.00;

    /**
     * PRICE_COUNTRY.
     */
    const PRICE_COUNTRY = 57.00;

    /**
     * WEIGHT_A.
     */
    const WEIGHT_A = 5.01;

    /**
     * WEIGHT_B.
     */
    const WEIGHT_B = 10.00;

    /**
     * LENGTH_A.
     */
    const LENGTH_A = 41;

    /**
     * LENGTH_B.
     */
    const LENGTH_B = 60;

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
