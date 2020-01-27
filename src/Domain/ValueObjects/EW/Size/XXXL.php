<?php

namespace App\Domain\ValueObjects\EW\Size;

use App\Domain\ValueObjects\EW\Size\EwSize;
use App\Domain\ValueObjects\Price\Price52;

/**
 * class XXXL
 */
class XXXL extends EwSize
{
    /**
     * Predefined values for XXXL package.
     * NAME.
     */
    const NAME = 'XXXL';

    /**
     * PRICE_TOWN.
     */
    const PRICE_TOWN = 90.00;

    /**
     * PRICE_COUNTRY.
     */
    const PRICE_COUNTRY = 95.00;

    /**
     * WEIGHT_A.
     */
    const WEIGHT_A = 15.01;

    /**
     * WEIGHT_B.
     */
    const WEIGHT_B = 30.00;

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
