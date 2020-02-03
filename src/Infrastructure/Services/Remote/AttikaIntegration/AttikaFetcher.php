<?php

namespace App\Infrastructure\Services\Remote\AttikaIntegration;

/**
 * Description of PMSFetcher
 *
 * @author i.goroshyn
 */
class AttikaFetcher {

    /**
     *
     * @var type 
     */
    private $remoteSideUrl = 'https://attika.justin.ua/api/';

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
                'Не передан массив данных для получения из Аттики!' . '++' . 
                'Не переданий масив даних для отримання з Аттицi!' . '++' . 
                'No data to parse from Attika was passed!'
            );
        }

        $response = $this->httpClient->request('POST', $this->remoteSideUrl, [
            'body' => $jsonPost
        ]);

        $response = json_decode((string)$response->getBody());

        if(!isset($response->result)
            || false == $response->result)
        {
            throw new \Exception(
                'Ошибка получения данных с сервера Аттика!' . '++' . 
                'Помилка отримання даних з сервера Аттика!' . '++' . 
                'Error while retrieving data from server Attika!'
            );
        }

        return $response;
    }
}
