<?php

namespace App\Infrastructure\Services\Provider\SMSProvider;

use Exception;

class TurboSmsProvider implements ISmsProvider
{
    /**
     * @var string TurboSMS wsdl URL
     */
    private $wsdlUrl;
    /**
     * @var string TurboSMS login
     */
    private $login;
    /**
     * @var string TurboSMS password
     */
    private $password;
    /**
     * @var string TurboSMS Sms sender
     */
    private $sender;
    /**
     * @var string SOAP Client
     */
    private $client;
    /**
     * @var string TurboSMS message body
     */
    private $text;
    /**
     * @var string TurboSMS auth data
     */
    private $auth;

    public function __construct()
    {
        $this->wsdlUrl = $_ENV['TURBOSMS_WSDL_URL'];
        $this->login = $_ENV['TURBOSMS_LOGIN'];
        $this->password = $_ENV['TURBOSMS_PASSWORD'];
        $this->sender = $_ENV['TURBOSMS_SENDER'];
        $this->client = new \SoapClient($this->wsdlUrl);
        $this->auth = [
            'login' => $this->login,
            'password' => $this->password
        ];
        $this->client->Auth($this->auth);
    }

    /**
     * @param $contactId int
     * @param ISmsProviderParameters $providerParameters
     * @return mixed
     * @throws Exception
     */
    public function send($contactId, ISmsProviderParameters $providerParameters)
    {
        $sms = [
            'sender' => $this->sender,
            'destination' => $providerParameters->phoneNumber,
            'text' => $providerParameters->text
        ];
        $response = $this->client->SendSMS($sms);
        if ($response && 'Сообщения успешно отправлены' == $response->SendSMSResult->ResultArray[0]) {
            $response->resultCode = 1;
            $response->data = $providerParameters->text;
            return $response;
        } else {
            throw new Exception('Wrong provider response');
        }
    }
}