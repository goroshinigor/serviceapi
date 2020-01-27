<?php

namespace App\Domain\Queries;

use App\Domain\ValueObjects\EW\Size\ISize;
use App\Domain\Queries\IQuery;
use App\Domain\ValueObjects\Price\IPrice;
use App\Domain\ValueObjects\City\ICity;

/**
 * Description of CalculateEWPrice
 *
 * @author i.goroshyn
 */
class CalculateEWPriceQuery implements IQuery {

    /**
     *
     * @var type 
     */
    private $COD;

    /**
     *
     * @var type 
     */
    private $estimatedCost;

    /**
     * ICity (generally from).
     */
    private $locationA;

    /**
     * ICity (generally to).
     */
    private $locationB; 

    /**
     * CODComissionPayer.
     */
    private $CODComissionPayer;

    /**
     * deliveryPayer.
     */
    private $deliveryPayer;

    /**
     * Package size.
     */
    private $size;    

    /**
     * 
     * @param string $locationA
     * @param string $locationB
     * @param int $CODComissionPayer
     * @param int $deliveryPayer
     * @param ISize $size
     */
    public function __construct(
        IPrice $COD,
        IPrice $estimatedCost,
        ICity $locationA,
        ICity $locationB,
        int $CODComissionPayer,
        int $deliveryPayer,
        ISize $size
    ) {
        $this->COD = $COD;
        $this->estimatedCost = $estimatedCost;
        $this->locationA = $locationA;
        $this->locationB = $locationB;
        $this->CODComissionPayer = $CODComissionPayer;
        $this->deliveryPayer = $deliveryPayer;
        $this->size = $size;
    }

    function getCOD(): IPrice {
        return $this->COD;
    }

    function getEstimatedCost(): IPrice {
        return $this->estimatedCost;
    }

    function getLocationA(): ICity {
        return $this->locationA;
    }

    function getLocationB(): ICity {
        return $this->locationB;
    }

    function getCODComissionPayer() {
        return $this->CODComissionPayer;
    }

    function getDeliveryPayer() {
        return $this->deliveryPayer;
    }

    function getSize() {
        return $this->size;
    }
}
