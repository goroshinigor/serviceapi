<?php

namespace App\Domain\Aggregates\EW;

use App\Domain\ValueObjects\Price\IPrice;
use App\Domain\ValueObjects\Price\Price52;
use App\Domain\ValueObjects\EW\Size\ISize;
use App\Domain\DTO\EwPriceDTO;
use App\Domain\ValueObjects\Route\PackageRoute;
use App\Domain\Queries\CalculateEWPriceQuery;
use App\Domain\Services\EW\EWCalculateSenderPrice;
use App\Domain\Services\EW\EWCalculateRecipientPrice;
use App\Domain\Services\Common\FromCommaToDot;
use App\Domain\Services\Common\RoundPrice;
use App\Infrastructure\Services\Shipping\DetermineShippingBounds;
/**
 * Description of CalculateEwPrice
 * EW - электронная накладная (Electronic Waybill).
 * @author i.goroshyn
 */
class Ew {

    /**
     * DELIVERY_PAYER_SENDER.
     */
    const DELIVERY_PAYER_SENDER = 1;

    /**
     * DELIVERY_PAYER_RECEIVER.
     */
    const DELIVERY_PAYER_RECIPIENT = 2;

    /**
     * DELIVERY_PAYER_DEFAULT.
     */
    const DELIVERY_PAYER_DEFAULT = 2;

    /**
     * COD_PAYER_SENDER.
     */
    const COD_PAYER_SENDER = 1;

    /**
     * COD_PAYER_RECEIVER.
     */
    const COD_PAYER_RECIPIENT = 2;

    /**
     * COD_PAYER_DEFAULT.
     */
    const COD_PAYER_DEFAULT = 2;

    /**
     * COD_NOT_DEFINED_RU.
     */
    const COD_NOT_DEFINED_RU = 'Не указана сумма cod';

    /**
     * COD_NOT_DEFINED_UA.
     */
    const COD_NOT_DEFINED_UA = 'Не вказана сума cod';

    /**
     * COD_NOT_DEFINED_EN.
     */
    const COD_NOT_DEFINED_EN = 'Cod sum not defined';

    /**
     * COD_NOT_DEFINED_RU.
     */
    const DELIVERY_PAYER_NOT_DEFINED_RU = 'Не указан плательщик доставки';

    /**
     * COD_NOT_DEFINED_UA.
     */
    const DELIVERY_PAYER_NOT_DEFINED_UA = 'Не вказаний платник доставки';

    /**
     * COD_NOT_DEFINED_EN.
     */
    const DELIVERY_PAYER_NOT_DEFINED_EN = 'Delivery payer not defined';

    /**
     * COD_NOT_DEFINED_RU.
     */
    const COD_PAYER_NOT_DEFINED_RU = 'Не указан плательщик COD';

    /**
     * COD_NOT_DEFINED_UA.
     */
    const COD_PAYER_NOT_DEFINED_UA = 'Не вказаний платник COD';

    /**
     * COD_NOT_DEFINED_EN.
     */
    const COD_PAYER_NOT_DEFINED_EN = 'COD payer not defined';

    /**
     *
     * @var type SenderPrice
     */
    private $senderPrice;

    /**
     *
     * @var type RecipientPrice
     */
    private $recipientPrice;

    /**
     *
     * @var PackageRoute 
     */
    private $packageRoute;

    /**
     *
     * @var ISize
     */
    private $packageSize;

    /**
     *
     * @var CalculateEWPriceQuery 
     */
    private $ewPriceQuery;

    /**
     *
     * @var int 
     */
    private $shippingBounds;

    /**
     *
     * @var int 
     */
    private $deliveryPayer;

    /**
     *
     * @var int 
     */
    private $codComissionPayer;

    /**
     *
     * @var IPrice 
     */
    private $codComissionPrice;

    /**
     *
     * @var IPrice 
     */
    private $estimatedCostComissionPrice;

    /**
     *
     * @var array 
     */
    private $requestParameters;

    /**
     *
     * @var array 
     */
    private $fromCommaToDot;

    /**
     *
     * @var array 
     */
    private $roundPrice;

    /**
     *
     * @var string
     */
    private $packagePrice;

    /**
     * Constructor.
     */
    public function __construct(
        EWCalculateSenderPrice $senderPriceCalculator,
        EWCalculateRecipientPrice $recipientPriceCalculator,
        FromCommaToDot $fromCommaToDot,
        RoundPrice $roundPrice,
        PackageRoute $packageRoute,
        IPrice $codComissionPrice,
        IPrice $estimatedCostComissionPrice,
        $deliveryPayer = self::DELIVERY_PAYER_DEFAULT,
        $codComissionPayer = self::COD_PAYER_DEFAULT,
        $requestParameters,
        CalculateEWPriceQuery $calculateEWPriceQuery,
        int $shippingBounds
    ) {
        $this->senderPriceCalculator = $senderPriceCalculator;
        $this->recipientPriceCalculator = $recipientPriceCalculator;
        $this->packageRoute = $packageRoute;
        $this->packageSize = $calculateEWPriceQuery->getSize();
        $this->ewPriceQuery = $calculateEWPriceQuery;
        $this->shippingBounds = $shippingBounds;
        $this->requestParameters = $requestParameters;
        $this->fromCommaToDot = $fromCommaToDot;
        $this->roundPrice = $roundPrice;

        $this->codComissionPrice = $codComissionPrice;
        $this->estimatedCostComissionPrice = $estimatedCostComissionPrice;

        $this->deliveryPayer = $deliveryPayer;
        $this->codComissionPayer = $codComissionPayer;

        $this->senderPrice = $this
                ->senderPriceCalculator
                ->calculate($this->ewPriceQuery, $this->shippingBounds);

        $this->recipientPrice = $this
                ->recipientPriceCalculator
                ->calculate($this->ewPriceQuery, $this->shippingBounds);
    }

    /**
     * 
     * @return EwPriceDTO
     */
    public function getEwPrice(): EwPriceDTO 
    {
        $maxLength = 0;
        $weight = 0;
        $cod = new Price52(0);
        $packagePrice = new Price52(0);

        if(isset($this->requestParameters->data->max_length)){
            $maxLength = $this->requestParameters->data->max_length;
        }

        if(isset($this->requestParameters->data->weight)){
            $weight = $this->requestParameters->data->weight;
        }

        if(isset($this->requestParameters->data->cod)){
            $cod = $this->requestParameters->data->cod;
        }
        $cod = $this->roundPrice
                ->round(new Price52($this->fromCommaToDot->convert($cod)));

        $this->codComissionPrice = $this
                ->roundPrice
                    ->round(new Price52($this->codComissionPrice->getPrice())
                );

        $this->estimatedCostComissionPrice = $this
                ->roundPrice
                    ->round(new Price52($this->estimatedCostComissionPrice->getPrice())
                );

        $estimatedCost = $this
                ->roundPrice
                    ->round($this->ewPriceQuery->getEstimatedCost()
                );

        if(DetermineShippingBounds::WITHIN_CITY == $this->shippingBounds){
            $this->townDelivery = 1;
            $packagePrice = $this->packageSize->getPriceTown();
        } else {
            $this->townDelivery = 0;
            $packagePrice = $this->packageSize->getPriceCountry();
        }

        $totalPrice = new Price52(
            $packagePrice->getPrice() +
            $this->codComissionPrice->getPrice() +
            $this->estimatedCostComissionPrice->getPrice()
        );

        return new EwPriceDTO(
            $this->senderPrice,
            $this->recipientPrice,
            $this->packageRoute,
            $this->packageSize,
            $packagePrice,
            $cod,
            $this->codComissionPrice,
            $this->estimatedCostComissionPrice,
            $estimatedCost,
            floatval($maxLength),
            floatval($weight),
            $totalPrice,
            $this->townDelivery
        );
    }
}
