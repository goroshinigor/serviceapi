<?php

namespace App\Domain\Services\EW;

use App\Domain\ValueObjects\EW\Dimensions\Dimensions;
use App\Domain\ValueObjects\EW\Size\XS;
use App\Domain\ValueObjects\EW\Size\S;
use App\Domain\ValueObjects\EW\Size\M;
use App\Domain\ValueObjects\EW\Size\L;
use App\Domain\ValueObjects\EW\Size\XL;
use App\Domain\ValueObjects\EW\Size\XXL;
use App\Domain\ValueObjects\EW\Size\XXXL;
use App\Domain\ValueObjects\EW\Size\ISize;
/**
 * Description of EWCalculateSizeByDimensions
 */
class EWCalculateSizeByDimensions {

    /**
     * Calculate.
     */
    public function calculate(Dimensions $dimensions)
    {
        return $this->determineSize(
            $dimensions->getMaxLenght(),
            $dimensions->getWeigth()
        );
    }

    /**
     * 
     * @param type $lenght
     * @param type $weight
     * @return S|XL|M|XS|L|XXL|XXXL
     */
    private function determineSize($lenght, $weight): ISize
    {
        return $this->getBestPrice(
            $this->determineWeight($weight),
            $this->determineLength($lenght)
        );
    }

    /**
     * 
     * @param type $weight
     * @return ISize
     * @throws \Exception
     */
    private function determineWeight($weight): ISize
    {
        switch(true){
            case ($weight >= XS::WEIGHT_A
                && $weight <= XS::WEIGHT_B):
                    return new XS();

            case ($weight >= S::WEIGHT_A
                && $weight <= S::WEIGHT_B):
                    return  new S();

            case ($weight >= M::WEIGHT_A
                && $weight <= M::WEIGHT_B):
                    return  new M();

            case ($weight >= L::WEIGHT_A
                && $weight <= L::WEIGHT_B):
                    return  new L();

            case ($weight >= XL::WEIGHT_A
                && $weight <= XL::WEIGHT_B):
                    return  new XL();

            case ($weight >= XXL::WEIGHT_A
                && $weight <= XXL::WEIGHT_B):
                    return new XXL();

            case ($weight >= XXXL::WEIGHT_A
                && $weight <= XXXL::WEIGHT_B):
                    return new XXXL();

            default:
                throw new \Exception('Incorrect weight was given!');
        }
    }

    /**
     * determineLength.
     */
    private function determineLength($length): ISize
    {
        switch(true){
            case ($length >= XS::LENGTH_A
                && $length <= XS::LENGTH_B):
                    return new XS();
                break;

            case ($length >= S::LENGTH_A 
                && $length <= S::LENGTH_B):
                    return  new S();
                break;

            case ($length >= M::LENGTH_A 
                && $length <= M::LENGTH_B):
                    return  new M();
                break;

            case ($length >= L::LENGTH_A 
                && $length <= L::LENGTH_B):
                    return  new L();
                break;

            case ($length >= XL::LENGTH_A 
                && $length <= XL::LENGTH_B):
                    return  new XL();
                break;

            case ($length >= XXL::LENGTH_A 
                && $length <= XXL::LENGTH_B):
                    return new XXL();
                break;

            case ($length >= XXXL::LENGTH_A 
                && $length <= XXXL::LENGTH_B):
                    return  new XXXL();
                break;

            default:
                throw new \Exception('Incorrect length was given!');
        }
    }
    
    /**
     * 
     * @return ISize
     */
    private function getBestPrice(ISize $size1, ISize $size2): ISize{
        $price1 = $size1->getPriceCountry()->getPrice() + $size1->getPriceTown()
                ->getPrice();
        $price2 = $size2->getPriceCountry()->getPrice() + $size2->getPriceTown()
                ->getPrice();

        if ($price1 > $price2) {
            return $size1;
        }

        return $size2;
    }
    
}
