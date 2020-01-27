<?php

namespace App\Domain\Services\Common;

/**
 * Description of FromCommaToDot
 *
 * @author i.goroshyn
 */
class FromCommaToDot {

    /**
     * 
     */
    public function convert(string $price): float
    {
        if(strchr($price,",")){
            return floatval(str_replace(',', '.',$price));
        }

        return floatval($price);
    }
}
