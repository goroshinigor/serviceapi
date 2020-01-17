<?php

namespace App\Infrastructure\Services\Provider\SMSProvider;

use App\Domain\Exceptions\WrongPhoneNumberException;
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
     * @throws Exception
     */
    private function setPhoneNumber($data)
    {
        if (ServiceValidatePhone::validate($data)) {
            $this->phoneNumber = $data;
        } else {
            throw new WrongPhoneNumberException("Wrong phone number");
        }
    }

    /**
     * @param $data string
     * @throws Exception
     */
    private function setSmsText($data)
    {
        if (isset($data)) {
            $this->text = $data;
        } else {
            throw new \Exception("Empty SMS body");
        }
    }
}