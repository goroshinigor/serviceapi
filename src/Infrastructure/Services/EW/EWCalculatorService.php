<?php

namespace App\Infrastructure\Services\EW;

use App\Domain\DTO\ServiceApiResponseResultDTO;
use App\Infrastructure\Services\Api\ApiService;
use App\Domain\Services\EW\EWCalculateSizeByDimensions;
use App\Domain\ValueObjects\EW\Size\EwSize;
use App\Domain\ValueObjects\EW\Dimensions\Dimensions;
use App\Domain\ValueObjects\EW\Size\ISize;
use App\Domain\Services\COD\CODComission;
use App\Domain\Services\EstimatedCost\EstimatedCostComission;
use App\Domain\Aggregates\EW\Ew;
use App\Domain\Queries\CalculateEWPriceQuery;
use App\Domain\ValueObjects\Price\Price52;
use App\Domain\Services\EW\EWCalculateSenderPrice;
use App\Domain\Services\EW\EWCalculateRecipientPrice;
use App\Infrastructure\Services\Shipping\DetermineShippingBounds;
use App\Domain\Services\Common\FromCommaToDot;
use App\Infrastructure\Services\Locality\GetPointLocality;
use App\Domain\Services\Common\RoundPrice;

/**
 * Class EWCalculatorService.
 */
class EWCalculatorService {

    /**
     *
     * @var type 
     */
    private $delimiter = "\\";

    /**
     *
     * @var type 
     */
    private $sizePrefix="App\Domain\ValueObjects\EW\Size";

    /**
     *
     * @var type 
     */
    private $fromDimensionsToSizeService;

    /**
     *
     * @var CODComission 
     */
    private $codComissionCalculator;

    /**
     *
     * @var EstimatedCostComission 
     */
    private $estCostComissionCalculator;

    /**
     *
     * @var type DetermineShippingBounds.
     */
    private $shippingBoundsService;

    /**
     *
     * @var type FromCommaToDot.
     */
    private $commaDotConverter;

    /**
     *
     * @var type GetPointLocality.
     */
    private $pointLocality;

    /**
     *
     * @var type EWCalculateSenderPrice.
     */
    private $senderPriceCalculator;

    /**
     *
     * @var type EWCalculateRecipientPrice.
     */
    private $recipientPriceCalculator;

    /**
     *
     * @var type RoundPrice.
     */
    private $roundPriceService;

    /**
     *
     * @param EWCalculateSizeByDimensions $fromDimensionsToSizeService
     */
    public function __construct(
        EWCalculateSenderPrice $senderPriceCalculator,
        EWCalculateRecipientPrice $recipientPriceCalculator,
        EWCalculateSizeByDimensions $fromDimensionsToSizeService,
        CODComission $codComissionCalculator,
        EstimatedCostComission $estCostComissionCalculator,
        DetermineShippingBounds $shippingBoundsService,
        FromCommaToDot $commaDotConverter,
        RoundPrice $roundPriceService,
        GetPointLocality $pointLocality
    ) {
        $this->senderPriceCalculator = $senderPriceCalculator;
        $this->recipientPriceCalculator = $recipientPriceCalculator;
        $this->fromDimensionsToSizeService = $fromDimensionsToSizeService;
        $this->codComissionCalculator = $codComissionCalculator;
        $this->estCostComissionCalculator = $estCostComissionCalculator;
        $this->shippingBoundsService = $shippingBoundsService;
        $this->commaDotConverter = $commaDotConverter;
        $this->roundPriceService = $roundPriceService;
        $this->pointLocality = $pointLocality;
    }

    /**
     * Main function.
     */
    public function get(ApiService $apiService): ServiceApiResponseResultDTO
    {
        $requestParameters = $apiService->getRequestParams();
        $packageRoute = $this->pointLocality->getLocality($apiService);
        $point_a_name = $packageRoute->getLocalityA();
        $point_b_name = $packageRoute->getLocalityB();

        if (array_key_exists('cod_payer', $requestParameters->data)) {
            if(!array_key_exists('cod', $requestParameters->data)){
                throw new \Exception(
                    Ew::COD_NOT_DEFINED_RU . '++' .
                    Ew::COD_NOT_DEFINED_UA . '++' .
                    Ew::COD_NOT_DEFINED_EN,
                    60421
                );
            }
        }

        if (!array_key_exists('cod', $requestParameters->data)) {
            $requestParameters->data->cod = 0;
        }

        if (!array_key_exists('estcost', $requestParameters->data)) {
            $requestParameters->data->estcost = 0;
        }

        if (!isset($requestParameters->data->cod_payer)) {
            $requestParameters->data->cod_payer = Ew::COD_PAYER_DEFAULT;
        }
 
        if (1 != $requestParameters->data->cod_payer && 
            2 != $requestParameters->data->cod_payer ) {
            throw new \Exception(
                Ew::COD_PAYER_NOT_DEFINED_RU . "++" .
                Ew::COD_PAYER_NOT_DEFINED_UA . "++" .
                Ew::COD_PAYER_NOT_DEFINED_EN,
                60450
            );
        }
 
        if (!isset($requestParameters->data->delivery_payer)) {
            throw new \Exception(
                Ew::DELIVERY_PAYER_NOT_DEFINED_RU . "++" .
                Ew::DELIVERY_PAYER_NOT_DEFINED_UA . "++" .
                Ew::DELIVERY_PAYER_NOT_DEFINED_EN,
                60440
            );
        }
 
        if (1 != $requestParameters->data->delivery_payer && 
            2 != $requestParameters->data->delivery_payer ) {
            throw new \Exception(
                Ew::DELIVERY_PAYER_NOT_DEFINED_RU . "++" .
                Ew::DELIVERY_PAYER_NOT_DEFINED_UA . "++" .
                Ew::DELIVERY_PAYER_NOT_DEFINED_EN,
                60440
            );
        }

        $packageSize = $this->getPackageSize($apiService);

        $cod = $this
                ->commaDotConverter
                ->convert($requestParameters->data->cod);
        $estCost = $this
                ->commaDotConverter
                ->convert($requestParameters->data->estcost);

        $codComissionPrice = $this
                ->codComissionCalculator
                ->calculate($cod);
        $estimatedCostComissionPrice = $this
                ->estCostComissionCalculator
                ->calculate($estCost);

        $EwPriceQuery = new CalculateEWPriceQuery(
            new Price52($cod),
            new Price52($estCost),
            $point_a_name,
            $point_b_name,
            $requestParameters->data->cod_payer,
            $requestParameters->data->delivery_payer,
            $packageSize
        );

        $shippingBounds = $this->shippingBoundsService->getBounds($EwPriceQuery);

        $Ew = new Ew(
                $this->senderPriceCalculator,
                $this->recipientPriceCalculator,
                $this->commaDotConverter,
                $this->roundPriceService,
                $packageRoute,
                $codComissionPrice,
                $estimatedCostComissionPrice,
                $requestParameters->data->delivery_payer,
                $requestParameters->data->cod_payer,
                $requestParameters,
                $EwPriceQuery,
                $shippingBounds
            );

        return new ServiceApiResponseResultDTO($Ew->getEwPrice()->toArray());
    }

    /**
     * TODO: move to separated service.
     * @return \App\Infrastructure\Services\EW\IPrice
     */
    private function getPackageSize(ApiService $apiService): ISize
    {
        $requestParameters = $apiService->getRequestParams();

        if(!isset($requestParameters->data->size)
            && !isset($requestParameters->data->weight)
            && !isset($requestParameters->data->max_length)
        ) {
            throw new \Exception('No or wrong parameters were given!');
        }

        if(isset($requestParameters->data->size) 
            || !empty($requestParameters->data->size)){
                $className = $this->sizePrefix 
                        . $this->delimiter 
                        . $requestParameters->data->size;

                return new $className;
        } elseif((isset($requestParameters->data->max_length) 
                || !empty($requestParameters->data->max_length)) 
                && (isset($requestParameters->data->weight) 
                || !empty($requestParameters->data->weight))
        ) {
            $dimensions = new Dimensions(
                $requestParameters->data->max_length,
                $requestParameters->data->weight
            );

            return $this
                ->fromDimensionsToSizeService
                ->calculate($dimensions);
        } else {
            throw new \Exception('No or wrong size options were given!');
        }
    }
}
