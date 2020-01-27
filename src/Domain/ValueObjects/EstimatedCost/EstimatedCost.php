<?php

namespace App\Domain\ValueObjects\EstimatedCost;

/**
 * class EstimatedCost.
 */
class EstimatedCost {

    /**
     * If the estimated cost lower then that bounds 
     * estimated cost for cod will be 0 but if greater then it will be 
     *   calculated by next scheme ESTIMATED_COST_COD_LIMIT * ESTIMATED_COST_COD_PERCENT
     */
    const ESTIMATED_COST_COD_LIMIT = 200;

    /**
     * ESTCOST PERCENT.
     */
    const ESTIMATED_COST_COD_PERCENT = 0.5;

    /**
     * COD payment cannot be greater than ESTCOST.
     */
    const ESTIMATED_COST_ALWAYS_GREATER_THEN_COD = 1;
}
