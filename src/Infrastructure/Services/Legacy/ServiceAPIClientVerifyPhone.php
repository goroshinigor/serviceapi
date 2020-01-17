<?php

namespace App\Infrastructure\Services\Legacy;

use App\Infrastructure\Services\Api\ApiService;
use App\Infrastructure\Services\Provider\SmsProvider;
use App\Infrastructure\Services\Provider\SMSProvider\SmsProviderParameters;
use Doctrine\ORM\EntityManagerInterface;

class ServiceAPIClientVerifyPhone
{
    private $result = array("Wrong result code");
    /**
     * @var string verification code
     */
    private $code;
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;
    /**
     * @var array request data
     */
    private $data;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->connection = $entityManager->getConnection();
        $this->generateCode();
    }

    /**
     * Generate verification code
     */
    private function generateCode()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->code .= rand(0, 9);
        }
    }

    /**
     * @param ApiService $apiService
     * @return array|mixed
     */
    public function run(ApiService $apiService)
    {
        $this->data = (array)$apiService->getRequestParams();
        if (isset($this->data)) {
            $provider = new SmsProvider();
            $provider = $provider->getProvider();
            $providerParameters = new SmsProviderParameters($this->data['data']->phone, $this->code);
            $res = (object)$res = $provider->send(1, $providerParameters);
            if (1 == $res->resultCode) {
                $this->result = $this->makeResponse($res->data);
            }
            return $this->result;
        }
    }

    private function makeResponse($result)
    {
        return [
            "status" => 1,
            "msg" => null,
            "result" => [
                "verify_code" => $result
            ]
        ];
    }
}
