<?php

namespace App\Domain\DTO;

class ServiceApiResponseMessageDTO {

    /**
     * response code.
     * @var type string
     */
    public $code = null;

    /**
     *
     * ru message.
     * @var type string
     */
    public $ru = null;

    /**
     * ua message
     * @var type string
     */
    public $ua = null;

    /**
     * en message
     * @var type string
     */
    public $en = null;

    /**
     * 
     * @param string $ru
     * @param string $ua
     * @param string $en
     */
    public function __construct(
            string $ru = null,
            string $ua = null,
            string $en = null,
            int $code = null
    ) {
        if(null !== $ru && null !== $ua && null !== $en){
            $this->ru = $ru;
            $this->ua = $ua;
            $this->en = $en;
            $this->code = $code;
        }
    }

    /**
     * 
     * @return array|null
     */
    public function getMessage() :? ServiceApiResponseMessageDTO
    {
        if(
            null == $this->ru 
            && null == $this->ua 
            && null == $this->en 
            && null == $this->code
        ) {
            return null;
        }

        return $this;
    }
}
