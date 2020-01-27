<?php

namespace App\Domain\Services\EW;

use App\Domain\ValueObjects\Price\RecipientPrice;
use App\Domain\Queries\CalculateEWPriceQuery;
use App\Domain\Services\COD\CODComission;
use App\Domain\Services\EstimatedCost\EstimatedCostComission;
use App\Domain\Aggregates\EW\Ew;
use App\Domain\ValueObjects\Price\Price52;
use App\Infrastructure\Services\Shipping\DetermineShippingBounds;
use App\Domain\Services\Common\RoundPrice;

/**
 * Description of EWCalculateRecipientPrice
 *
 * @author i.goroshyn
 */
class EWCalculateRecipientPrice {

    /**
     *
     * @var CODComission 
     */
    private $CODComissionService;

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
     * @param \App\Domain\Services\EW\CODComission $CODComissionService
     */
    public function __construct(
        CODComission $CODComissionService,
        EstimatedCostComission $insuranceCalculator,
        RoundPrice $roundPriceService
    ) {
        $this->CODComissionService = $CODComissionService;
        $this->insuranceCalculator = $insuranceCalculator;
        $this->roundPriceService = $roundPriceService;
    }

    /**
     * 
     */
    public function calculate(
            CalculateEWPriceQuery $query,
            int $shippingBounds
    ): RecipientPrice {
        
        $fixedDeliveryPrice = new Price52(0);
        $insurancePrice = new Price52(0);
        $codComissionPrice = new Price52(0);
        $codPrice = new Price52($query->getCOD()->getPrice());
        
        switch($query->getCODComissionPayer()){
            case Ew::COD_PAYER_RECIPIENT:
                    $codComissionPrice = $this
                        ->CODComissionService
                        ->calculate($query
                            ->getCOD()
                            ->getPrice()
                    );
                break;

            case Ew::COD_PAYER_SENDER:
            default:
                    $codComissionPrice = new Price52(0);
                break;
        }
        switch($query->getDeliveryPayer()){
            case Ew::DELIVERY_PAYER_RECIPIENT:
                    $insurancePrice = $this
                        ->insuranceCalculator
                        ->calculate($query
                                ->getEstimatedCost()
                                ->getPrice());
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
            case Ew::DELIVERY_PAYER_SENDER:
            default:
                    $fixedDeliveryPrice = new Price52(0);
                break;
        }

        $fixedDeliveryPrice = new Price52($this->roundPriceService->round($fixedDeliveryPrice)->getPrice());
        $insurancePrice = new Price52($this->roundPriceService->round($insurancePrice)->getPrice());
        $codComissionPrice = new Price52($this->roundPriceService->round($codComissionPrice)->getPrice());
        $codPrice = new Price52($this->roundPriceService->round($codPrice)->getPrice());

        $deliveryPrice = new Price52($fixedDeliveryPrice->getPrice() + $insurancePrice->getPrice());
        $totalPrice = new Price52($fixedDeliveryPrice->getPrice() + $insurancePrice->getPrice() + $codPrice->getPrice() + $codComissionPrice->getPrice());

        $deliveryPrice = new Price52($this->roundPriceService->round($deliveryPrice)->getPrice());
        $totalPrice = new Price52($this->roundPriceService->round($totalPrice)->getPrice());

        return new RecipientPrice(
            $fixedDeliveryPrice,
            $insurancePrice,
            $codPrice,
            $codComissionPrice,
            $deliveryPrice,
            $totalPrice
        );
    }
}
