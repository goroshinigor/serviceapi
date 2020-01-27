<?php

namespace App\Domain\DTO;

use App\Domain\ValueObjects\Price\SenderPrice;
use App\Domain\ValueObjects\Price\RecipientPrice;
use App\Domain\ValueObjects\Route\PackageRoute;
use App\Domain\ValueObjects\EW\Size\ISize;
use App\Domain\ValueObjects\Price\IPrice;
use App\Infrastructure\Services\Shipping\DetermineShippingBounds;

/**
 * Description of EwPriceDTO
 *
 * @author i.goroshyn
 */
class EwPriceDTO {

    /**
     *
     * @var SenderPrice 
     */
    private $senderPrice;

    /**
     *
     * @var RecipientPrice 
     */
    private $recipientPrice;

    /**
     *
     * @var PackageRoute 
     */
    private $packageRoute;

    /**
     *
     * @var PackageSize
     */
    private $packageSize;

    /**
     *
     * @var PackageSize
     */
    private $townDelivery;

    /**
     *
     * @var string
     */
    private $priceCod;

    /**
     *
     * @var string
     */
    private $priceCodComission;

    /**
     *
     * @var string
     */
    private $priceInsurance;

    /**
     *
     * @var IPrice
     */
    private $estimatedCostPrice;

    /**
     *
     * @var float
     */
    private $maxLength;

    /**
     *
     * @var float
     */
    private $weight;

    /**
     *
     * @var IPrice
     */
    private $totalPrice;

    /**
     * 
     * @param App\Domain\ValueObjects\Price\Price52 $senderBillTotal
     * @param App\Domain\ValueObjects\Price\Price52 $senderDeliveryComission
     * @param App\Domain\ValueObjects\Price\Price52 $senderCODComission
     * @param App\Domain\ValueObjects\Price\Price62 $receiverBillTotal
     * @param App\Domain\ValueObjects\Price\Price52 $receiverDeliveryComission
     * @param App\Domain\ValueObjects\Price\Price52 $receiverCOD
     * @param App\Domain\ValueObjects\Price\Price52 $receiverCODComission
     */
    public function __construct(
            SenderPrice $senderPrice,
            RecipientPrice $recipientPrice,
            PackageRoute $packageRoute,
            ISize $packageSize,
            IPrice $packagePrice,
            IPrice $priceCod,
            IPrice $priceCodComission,
            IPrice $priceIsurance,
            IPrice $estimatedCostPrice,
            float $maxLength,
            float $weight,
            IPrice $totalPrice,
            int $townDelivery
    ) {
        $this->senderPrice = $senderPrice;
        $this->recipientPrice = $recipientPrice;
        $this->packageRoute = $packageRoute;
        $this->packageSize = $packageSize;
        $this->packagePrice = $packagePrice;
        $this->priceCod = $priceCod;
        $this->priceCodComission = $priceCodComission;
        $this->priceIsurance = $priceIsurance;
        $this->estimatedCostPrice = $estimatedCostPrice;
        $this->maxLength = $maxLength;
        $this->weight = $weight;
        $this->totalPrice = $totalPrice;
        $this->townDelivery = $townDelivery;
    }

    /**
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            "point_a_locality_name" => $this->packageRoute->getLocalityA()->getTitleRu(),
            "point_a_locality_uuid" => $this->packageRoute->getLocalityA()->getUuid(),
            "point_a_locality_scoatou" => $this->packageRoute->getLocalityA()->getScoatou(),
            "point_b_locality_name" => $this->packageRoute->getLocalityB()->getTitleRu(),
            "point_b_locality_uuid" => $this->packageRoute->getLocalityB()->getUuid(),
            "point_b_locality_scoatou" => $this->packageRoute->getLocalityB()->getScoatou(),
            "max_length" => $this->maxLength,
            "weight" => $this->weight,
            "size" => $this->packageSize->getSizeName(),
            "cod" => $this->priceCod->getPrice(),
            "estcost" => $this->estimatedCostPrice->getPrice(),
            "town_delivery" => $this->townDelivery,
            "price" => $this->packagePrice->getPrice(),
            "price_cod_commission" => $this->priceCodComission->getPrice(),
            "price_insurance" => $this->priceIsurance->getPrice(),
            "price_total" => $this->totalPrice->getPrice(),
            'senderPrice' => $this->senderPrice->toArray(),
            'recipientPrice' => $this->recipientPrice->toArray(),
        ];
    }

}
