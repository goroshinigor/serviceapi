<?php

namespace App\Domain\Services\EstimatedCost;

use App\Domain\ValueObjects\Price\IPrice;
use App\Domain\ValueObjects\Price\Price52;
use App\Domain\ValueObjects\EstimatedCost\EstimatedCost;
/**
 * Description of CalculateEstimatedCostComission
 *
 * @author i.goroshyn
 */
class EstimatedCostComission {

    /**
     * 
     */
    public function calculate(float $estCost): IPrice
    {
        if($estCost <= EstimatedCost::ESTIMATED_COST_COD_LIMIT){
            return new Price52(0);
        }
        $comission = EstimatedCost::ESTIMATED_COST_COD_PERCENT / 100 * $estCost;

        return new Price52($comission);
    } 
}
