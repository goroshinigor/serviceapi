<?php

namespace App\Domain\DTO;

use App\Domain\DTO\ServiceApiResponseMessageDTO;
use App\Domain\DTO\ServiceApiResponseResultDTO;
use App\Domain\DTO\ServiceApiResponseStatusDTO;

class ServiceApiResponseDTO {

    /**
     *
     * @var type int
     */
    public $status;

    /**
     *
     * @var type array
     */
    public $msg;

    /**
     *
     * @var type array
     */
    public $result;

    /**
     * 
     */
    public function __construct(
            ServiceApiResponseStatusDTO $status,
            ServiceApiResponseMessageDTO $message,
            ServiceApiResponseResultDTO $result
    ) {
        $this->status = $status->getCode();
        $this->msg = $message->getMessage();
        $this->result = $result->getResult();
    }

    /**
     * 
     * @return type
     */
    public function toArray() 
    {
        return json_decode(json_encode((array)get_object_vars($this)));
    }
}
