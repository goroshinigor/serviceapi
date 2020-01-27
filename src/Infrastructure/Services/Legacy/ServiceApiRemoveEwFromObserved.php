<?php

namespace App\Infrastructure\Services\Legacy;

use App\Infrastructure\Services\Api\ApiService;
use App\Infrastructure\Services\Remote\CrmApiIntegration\Client\CheckPhoneExistService;
use App\Infrastructure\Services\Remote\CrmApiIntegration\Client\GetInfoService;

class ServiceApiRemoveEwFromObserved
{
    /**
     * @var string
     */
    private $authToken;

    private $apiLogin = 'Exchange';

    private $apiPassword = 'Exchange';

    private $justinApiRequest;

    private $apiKey = 'cf3a155f-09fd-11ea-a2d9-0050569bda1b';

    private $apiUrl = 'http://api.justin.ua/justin_pms_test/hs/api/v1/observers/ObserverRemove';

    private $checkPhoneService;

    private $getInfoService;

    private $response = [];

    public function __construct(CheckPhoneExistService $checkPhoneService, GetInfoService $getInfoService)
    {
        $this->checkPhoneService = $checkPhoneService;
        $this->getInfoService = $getInfoService;
        $this->authToken = base64_encode("$this->apiLogin:$this->apiPassword");
    }

    public function run(ApiService $apiService)
    {
        $data = (array)$apiService->getRequestParams();
        $memberId = $this->checkPhoneService->check($data['data']->phone_number);
        if (isset($memberId)) {
            $clientInfo = $this->getInfoService->get($memberId);
            $this->formJustinApiRequest($data, $clientInfo);
            $pmsResponse = $this->sendRequest($this->justinApiRequest);
            if (1 == $pmsResponse['result']) {
                $this->response['status'] = true;
            } else {
                $this->response['status'] = false;
            }
        }
        return $this->response;
    }

    private function formJustinApiRequest($data, $clientInfo)
    {
        $this->justinApiRequest = [
            "api_key" => $this->apiKey,
            "data" => [
                "number" => $data['data']->ew_number,
                "observer_phone" => $data['data']->phone_number,
                "observer_FIO" => sprintf('%s %s %s', $clientInfo['last_name'], $clientInfo['first_name'], $clientInfo['middle_name'])
            ]
        ];
    }

    private function sendRequest($data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Content-Type: application/json', "Authorization: Basic " . $this->authToken]);
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

}