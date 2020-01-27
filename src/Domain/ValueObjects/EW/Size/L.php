<?php

namespace App\Domain\ValueObjects\EW\Size;

use App\Domain\ValueObjects\EW\Size\EwSize;
use App\Domain\ValueObjects\Price\Price52;

/**
 * class L
 */
class L extends EwSize
{
    /**
     * Predefined values for L package.
     * NAME.
     */
    const NAME = 'L';

    /**
     * PRICE_TOWN.
     */
    const PRICE_TOWN = 38.00;

    /**
     * PRICE_COUNTRY.
     */
    const PRICE_COUNTRY = 47.00;

    /**
     * WEIGHT_A.
     */
    const WEIGHT_A = 2.01;

    /**
     * WEIGHT_B.
     */
    const WEIGHT_B = 5.00;

    /**
     * LENGTH_A.
     */
    const LENGTH_A = 41;

    /**
     * LENGTH_B.
     */
    const LENGTH_B = 60;

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
