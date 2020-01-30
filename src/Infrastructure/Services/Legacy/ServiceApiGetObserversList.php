<?php

namespace App\Infrastructure\Services\Legacy;

use App\Infrastructure\Services\Api\ApiService;
use App\Infrastructure\Services\Remote\CrmApiIntegration\Client\CheckPhoneExistService;
use App\Infrastructure\Services\Remote\CrmApiIntegration\Client\GetInfoService;
use App\Infrastructure\Services\Validation\ServiceValidatePhone;

class ServiceApiGetObserversList
{
    /**
     * @var string
     */
    private $authToken;

    private $apiLogin = 'Exchange';

    private $apiPassword = 'Exchange';

    private $pmsApiRequest;

    private $apiKey = 'cf3a155f-09fd-11ea-a2d9-0050569bda1b';

    private $apiUrl = 'http://api.justin.ua/justin_pms_test/hs/api/v1/observers/ObserverStatus';

    private $apiUrlGetData = 'http://api.justin.ua/justin_pms_test/hs/v2/runRequest';

    private $checkPhoneService;

    private $getInfoService;

    private $getDataRequest;

    private $sign;

    private $response;

    private $validatePhoneService;

    private $apiResponse;

    private $statusesListService;

    private $deliveryStatusesList;

    /**
     * ServiceApiGetObserversList constructor.
     * @param CheckPhoneExistService $checkPhoneService
     * @param GetInfoService $getInfoService
     * @param ServiceValidatePhone $validatePhoneService
     * @param ServiceApiStatusesList $statusesListService
     */
    public function __construct(CheckPhoneExistService $checkPhoneService, GetInfoService $getInfoService, ServiceValidatePhone $validatePhoneService)
    {
        $this->apiResponse['status'] = false;
        $this->checkPhoneService = $checkPhoneService;
        $this->getInfoService = $getInfoService;
        $this->validatePhoneService = $validatePhoneService;
        $this->authToken = base64_encode("$this->apiLogin:$this->apiPassword");
        $this->statusesListService = new ServiceApiStatusesList();
        $pattern = sprintf('%s:%s', $this->apiPassword, date('yy-m-d'));
        $this->sign = sha1($pattern);
    }

    public function run(ApiService $apiService)
    {
        $data = (array)$apiService->getRequestParams();
        $this->deliveryStatusesList = $this->statusesListService->getResult();
        $this->validatePhoneService->validate($data['data']->phone_number);
        $memberId = $this->checkPhoneService->check($data['data']->phone_number);
        if (isset($memberId)) {
            $clientInfo = $this->getInfoService->get($memberId);
            $this->formPmsApiRequest($data, $clientInfo);
            $pmsResponse = $this->sendRequest($this->pmsApiRequest, $this->apiUrl);
            if (1 == $pmsResponse['result']) {
                $this->formApiResponse($pmsResponse);
            }
        }
        return $this->apiResponse;
    }

    private function formApiResponse($data)
    {
        foreach ($data['Orders'] as $key => $order) {
            $this->formGetDataRequest($order);
            $response = $this->sendRequest($this->getDataRequest, $this->apiUrlGetData);
            $this->apiResponse['orders'][$key]['ew_number'] = $order['Number'];
            $this->apiResponse['orders'][$key]['kis_number'] = $order['Number_KIS'];
            $this->apiResponse['orders'][$key]['ttn_number'] = $order['Number_TTN'];
            $this->apiResponse['orders'][$key]['status_time'] = $response['data'][0]['fields']['statusDate'];
            $this->apiResponse['orders'][$key]['status_id'] = $response['data'][0]['fields']['statusOrder']['uuid'];
            $this->apiResponse['orders'][$key]['status_name'] = $order['Status'];
            $this->apiResponse['orders'][$key]['status_description'] = $this->deliveryStatusesList["statuses"][$this->apiResponse['orders'][$key]['status_id']]["platforms"]["forservices"]["description_ua"];
        }
    }

    private function formPmsApiRequest($data, $clientInfo)
    {
        $this->pmsApiRequest = [
            "api_key" => $this->apiKey,
            "data" => [
                "observer_phone" => $data['data']->phone_number,
            ]
        ];
    }

    private function formGetDataRequest($data)
    {
        $params = new \stdClass();
        $params->name = 'orderNumber';
        $params->comparison = 'equal';
        $params->leftValue = $data['Number'];
        $this->getDataRequest = [
            "keyAccount" => "Exchange",
            "sign" => $this->sign,
            "request" => "getData",
            "type" => "request",
            "name" => "getOrderStatusesHistory",
            "params" => new \stdClass(),
            "filter" => [$params]
        ];
    }

    private function sendRequest($data, $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Content-Type: application/json', "Authorization: Basic " . $this->authToken]);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }
}