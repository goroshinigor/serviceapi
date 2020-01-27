<?php

namespace App\Domain\ValueObjects\EW\Dimensions;

/**
 * Description of Dimensions
 *
 * @author i.goroshyn
 */
class Dimensions {

    /**
     *
     * @var type 
     */
    private $weigth;

    /**
     *
     * @var type 
     */
    private $maxLenght;

    /**
     * 
     * @param type $maxLenght
     * @param type $weigth
     */
    public function __construct($maxLenght, $weigth) {
        $this->maxLenght = $maxLenght;
        $this->weigth = $weigth;
    }

    /**
     * 
     * @return type
     */
    public function getMaxLenght(){
        return $this->maxLenght;
    }

    /**
     * 
     * @return type
     */
    public function getWeigth(){
        return $this->weigth;
    }
}
