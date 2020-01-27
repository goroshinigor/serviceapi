<?php

namespace App\Domain\ValueObjects\Price;
use App\Domain\ValueObjects\Price\Price;
use App\Domain\ValueObjects\Price\Price52;

/**
 * Description of RecipientPrice.
 *
 * @author i.goroshyn
 */
class RecipientPrice extends Price
{
    /**
     *
     * @var Price52 
     */
    private $totalPrice;

    /**
     *
     * @var Price52 
     */
    private $fixedDeliveryPrice;

    /**
     *
     * @var Price52 
     */
    private $deliveryComissionPrice;

    /**
     *
     * @var Price52 
     */
    private $deliveryPrice;

    /**
     *
     * @var Price52 
     */
    private $codPrice;

    /**
     *
     * @var Price52 
     */
    private $codComissionPrice;

    /**
     * 
     * @param \App\Domain\DTO\Price52 $deliveryPrice
     * @param \App\Domain\DTO\Price52 $codComissionPrice
     */
    function __construct(
        Price52 $fixedDeliveryPrice,
        Price52 $deliveryComissionPrice,
        Price52 $codPrice,
        Price52 $codComissionPrice,
        Price52 $deliveryPrice,
        Price52 $totalPrice
    ) {
        $this->fixedDeliveryPrice = $fixedDeliveryPrice;
        $this->deliveryComissionPrice = $deliveryComissionPrice;
        $this->codPrice = $codPrice;
        $this->codComissionPrice = $codComissionPrice;
        $this->deliveryPrice = $deliveryPrice;
        $this->totalPrice = $totalPrice;
//        $this->deliveryPrice = new Price52($fixedDeliveryPrice->getPrice() + $deliveryComissionPrice->getPrice());
//        $this->totalPrice = new Price52($fixedDeliveryPrice->getPrice() + $deliveryComissionPrice->getPrice() + $codPrice->getPrice() + $codComissionPrice->getPrice());
    }

    /**
     * 
     * @return array
     */
    public function toArray(): array
    {
        foreach($this as $key => $value){
            if($value instanceof Price52){
                $result[$key] = $value->getPrice();
            }
        }

        return $result;
    }
}
