<?php

namespace App\Domain\DTO;

class ServiceApiResponseResultDTO {

    /**
     *
     * @var type 
     */
    private $resultData;

    /**
     * 
     * @param array $resultData
     */
    public function __construct($resultData)
    {
        $this->resultData = $resultData;
    }

    /**
     * 
     * @return array
     */
    public function getResult():? array
    {
        return $this->resultData;
    }
}
