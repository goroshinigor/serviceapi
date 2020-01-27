<?php

namespace App\Bundles\JustinExceptionsBundle\Common;

class JustinExceptionResponseMessageDTO {

    /**
     * response code.
     * @var type string
     */
    public $code;

    /**
     *
     * ru message.
     * @var type string
     */
    public $ru;

    /**
     * ua message
     * @var type string
     */
    public $ua;

    /**
     * en message
     * @var type string
     */
    public $en;

    /**
     * 
     * @param string $ru
     * @param string $ua
     * @param string $en
     */
    public function __construct(
            string $ru,
            string $ua,
            string $en,
            int $code
    ) {
        $this->ru = $ru;
        $this->ua = $ua;
        $this->en = $en;
        $this->code = $code;
    }
    
    public function getMessage(): JustinExceptionResponseMessageDTO
    {
        return $this;
    }
}
