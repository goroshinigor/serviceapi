<?php

namespace App\Domain\Services\EW;

use App\Domain\Queries\CalculateEWPriceQuery;
use App\Domain\ValueObjects\Price\SenderPrice;
use App\Domain\Services\COD\CODComission;
use App\Domain\Aggregates\EW\Ew;
use App\Domain\Services\EstimatedCost\EstimatedCostComission;
use App\Infrastructure\Services\Shipping\DetermineShippingBounds;
use App\Domain\ValueObjects\Price\Price52;
use App\Domain\Services\Common\RoundPrice;

/**
 * EWCalculateSenderPrice domain service.
 */
class EWCalculateSenderPrice {

    /**
     *
     * @var type CODComission
     */
    private $CODComissionCalculator;

    /**
     *
     * @var type EstimatedCostComission
     */
    private $insuranceCalculator;

    /**
     *
     * @var type EstimatedCostComission
     */
    private $roundPriceService;

    /**
     * 
     * @param CODComission $codComissionCalculator
     */
    public function __construct(
            CODComission $CODComissionCalculator,
            EstimatedCostComission $insuranceCalculator,
            RoundPrice $roundPriceService
    ) {
        $this->CODComissionCalculator = $CODComissionCalculator;
        $this->insuranceCalculator = $insuranceCalculator;
        $this->roundPriceService = $roundPriceService;
    }

    /**
     * 
     * @param CalculateEWPriceQuery $query
     * @return EwSenderPriceDTO
     */
    public function calculate(
            CalculateEWPriceQuery $query,
            int $shippingBounds
    ): SenderPrice {
        $fixedDeliveryPrice = new Price52(0);
        $insurancePrice = new Price52(0);
        $codComissionPrice = new Price52(0);
        
        // Trying to calculate Cash on delivery Comission for Sender
        switch($query->getCODComissionPayer()){
            case Ew::COD_PAYER_SENDER:
                    $codComissionPrice = $this
                        ->CODComissionCalculator
                        ->calculate($query
                            ->getCOD()
                            ->getPrice()
                    );

                break;

            case Ew::COD_PAYER_RECIPIENT:
            default:
                    $codComissionPrice = new Price52(0);
                break;
        }

        // Calculating Cash on delivery Comission for Sender
        switch($query->getDeliveryPayer()){
            case Ew::DELIVERY_PAYER_SENDER:
                    $insurancePrice = $this
                        ->insuranceCalculator
                        ->calculate($query->getEstimatedCost()->getPrice());
                switch($shippingBounds){
                    case DetermineShippingBounds::WITHIN_COUNTRY:
                            $fixedDeliveryPrice = $query
                            ->getSize()
                            ->getPriceCountry();

                        break;
                    case DetermineShippingBounds::WITHIN_CITY:
                    default:
                            $fixedDeliveryPrice = $query
                                ->getSize()
                                ->getPriceTown();
                        break;
                }
                break;
            case Ew::DELIVERY_PAYER_RECIPIENT:
            default:
                    $fixedDeliveryPrice = new Price52(0);
                    $insurancePrice = new Price52(0);
                break;
        }

        $fixedDeliveryPrice = new Price52($this->roundPriceService->round($fixedDeliveryPrice)->getPrice());
        $insurancePrice = new Price52($this->roundPriceService->round($insurancePrice)->getPrice());
        $codComissionPrice = new Price52($this->roundPriceService->round($codComissionPrice)->getPrice());

        $deliveryPrice = new Price52($fixedDeliveryPrice->getPrice() + $insurancePrice->getPrice());
        $totalPrice = new Price52($fixedDeliveryPrice->getPrice() + $insurancePrice->getPrice() + $codComissionPrice->getPrice());

        $deliveryPrice = new Price52($this->roundPriceService->round($deliveryPrice)->getPrice());
        $totalPrice = new Price52($this->roundPriceService->round($totalPrice)->getPrice());
        
        return new SenderPrice(
            $fixedDeliveryPrice,
            $insurancePrice,
            $codComissionPrice,
            $deliveryPrice,
            $totalPrice
        );
    }
}
