<?php

namespace App\Bundles\JustinExceptionsBundle\Common;

/**
 * class ExceptionResponse
 */
class JustinExceptionResponseDTO
{
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
            JustinExceptionResponseStatusDTO $status,
            JustinExceptionResponseMessageDTO $message,
            JustinExceptionResponseResultDTO $result
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
