<?php

namespace App\Infrastructure\Services\Provider\SMSProvider;

use App\Infrastructure\Services\Validation\ServiceValidatePhone;

class SmsProviderParameters implements ISmsProviderParameters
{
    /**
     * @var string Phone number for send SMS
     */
    public $phoneNumber;

    /**
     * @var string Message content
     */
    public $text;

    public function __construct(string $phoneNumber, string $text)
    {
        $this->setPhoneNumber($phoneNumber);
        $this->setSmsText($text);
    }

    /**
     * @param $data string
     */
    private function setPhoneNumber($data)
    {
            $this->phoneNumber = $data;
    }

    /**
     * @param $data string
     */
    private function setSmsText($data)
    {
            $this->text = $data;
    }
}