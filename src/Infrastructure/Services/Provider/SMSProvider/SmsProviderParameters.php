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
     * @throws \Exception
     */
    private function setPhoneNumber($data)
    {
        if (ServiceValidatePhone::validate($data)) {
            $this->phoneNumber = $data;
        } else {
            throw new \Exception('Указанный телефон не соответствует формату +380999999999++Зазначений телефон не відповідає формату +380999999999++The specified phone does not match the format +380999999999',60201);
        }
    }

    /**
     * @param $data string
     * @throws \Exception
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