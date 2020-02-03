<?php

namespace App\Infrastructure\Services\Remote\PMSIntegration;

/**
 * Description of PMSFetcher
 *
 * @author i.goroshyn
 */
class PMSFetcher {

    /**
     *
     * @var type 
     */
//    private $remoteSideUrl = 'https://api.justin.ua/hs/v2/runRequest';
    private $remoteSideUrl = 'https://api.justin.ua/justin_pms/hs/v2/runRequest';

    /**
     *
     * @var GuzzleHttp\Client
     */
    private $httpClient;

    /**
     * 
     */
    public function __construct(){
        $this->httpClient = new \GuzzleHttp\Client(
            [
                'base_uri' => $this->remoteSideUrl
            ]
        );
    }
    /**
     * 
     * @param type $jsonPost
     * @return type
     * @throws \Exception
     */
    public function fetch($jsonPost)
    {
        if(!isset($jsonPost) || empty($jsonPost)){
            throw new \Exception(
                'Не передан массив данных для получения из ПМС!' . '++' . 
                'Не переданий масив даних для отримання з ПМС!' . '++' . 
                'No data to parse from PMS was passed!'
            );
        }

        $response = $this->httpClient->post($this->remoteSideUrl, [
            'curl' => [
                CURLOPT_POSTFIELDS => json_encode($jsonPost),
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                    "Authorization: Basic " . base64_encode("Exchange:Exchange"))
            ]
        ]);

        $response = json_decode((string)$response->getBody());

        if(!isset($response->response->status) 
                || false == $response->response->status)
        {
            throw new \Exception(
                'Ошибка получения данных с сервера PMS!' . '++' . 
                'Помилка отримання даних з сервера PMS!' . '++' . 
                'Error while retrieving data from server PMS!'
            );
        }

        return $response;
    }
}
