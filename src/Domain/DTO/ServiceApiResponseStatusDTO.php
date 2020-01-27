<?php

namespace App\Domain\DTO;

class ServiceApiResponseStatusDTO {
    
    /**
     *
     * @var type 
     */
    private $code = -1;
    
    /**
     * 
     * @param string $code
     */
    public function __construct(int $code) 
    {
        $this->code = $code;
    }

    /**
     * 
     * @param type $code
     * @return bool
     */
    public function setCode($code): bool
    {
        return $this->code = $code;
    }

    /**
     * 
     * @return string
     */
    public function getCode(): int
    {
        return intval($this->code);
    }
}
