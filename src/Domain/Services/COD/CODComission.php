<?php

namespace App\Domain\Services\COD;

use App\Domain\ValueObjects\Price\IPrice;
use App\Domain\ValueObjects\Price\Price52;
use App\Domain\ValueObjects\COD\COD;

/**
 * Description of CalculateCODComission
 *
 * @author i.goroshyn
 */
class CODComission {
    
    /**
     * Main function calculate.
     */
    public function calculate(float $cod): IPrice
    {
        if (0 == $cod){
            return new Price52(0);
        }

        $comission = (($cod / 100) *COD::COD_PERCENT) + COD::COD_FIXED;
        if($comission > COD::COD_MIN && $comission < COD::COD_MAX)
        {
            return new Price52($comission);
        }

        throw new \Exception('Wrong incoming parameters were given!');
    }
}
