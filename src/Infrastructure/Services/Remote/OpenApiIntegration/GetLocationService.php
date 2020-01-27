<?php

namespace App\Infrastructure\Services\Remote\OpenApiIntegration;

use GuzzleHttp\RequestOptions;
use GuzzleHttp\Psr7\Response as GuzzleHttpResponse;
use App\Infrastructure\Entity\ServiceapiCity;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\ValueObjects\City\ICity;
use App\Domain\Exceptions\LocationNotFoundException;

/**
 * Class GetLocationService.
 */
class GetLocationService {

    /**
     *
     * @var type 
     */
    private $openApiLogin = 'OPENAPI';

    /**
     *
     * @var type 
     */
    private $openApiPassword = 'RIAneVEs';

    /**
     *
     * @var type 
     */
    private $remoteSideUrl = 'https://api.justin.ua/justin_pms/hs/v2/runRequest';

    /**
     *
     * @var GuzzleHttp\Client
     */
    private $httpClient;

    /**
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * 
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->httpClient = new \GuzzleHttp\Client();
    }

    /**
     * 
     */
    public function getByName(ICity $cityName): ServiceapiCity
    {
        //Получаем название города тремя запросами ибо такова архитектура openapi
        $responseRU = $this->getCity($cityName->getTitleRu(),'RU');
        $responseRU = json_decode($responseRU->getBody());
        $responseUA = $this->getCity($cityName->getTitleRu(),'UA');
        $responseUA = json_decode($responseUA->getBody());
        $responseEN = $this->getCity($cityName->getTitleRu(),'EN');
        $responseEN = json_decode($responseEN->getBody());

        //Если нет объекта ответа значит в запросе что то пошло не так
        if(!isset($responseRU->data)){
            throw new \Exception('No data was received from openapi, something went wrong!');
        }

        //Если город не найден возвращаем false
        if(empty($responseRU->data)){
            return false;
        }

        //Если город найден - успех
        $response[] = $responseRU->data[0]->fields;
        $response[] = $responseUA->data[0]->fields;
        $response[] = $responseEN->data[0]->fields;

        $city = new ServiceapiCity();
        $city = $city->setTitleRu($responseRU->data[0]->fields->descr);
        $city = $city->setTitleUa($responseUA->data[0]->fields->descr);
        $city = $city->setTitleEn($responseEN->data[0]->fields->descr);
        $city = $city->setUuid($responseEN->data[0]->fields->uuid);
        $city = $city->setRegionUuid($responseEN->data[0]->fields->objectOwner->uuid);
        $city = $city->setCode($responseRU->data[0]->fields->code);
        $city = $city->setScoatou($responseRU->data[0]->fields->SCOATOU);
        $city = $city->setUpdatedAt(new \DateTime());
        
        if(!$this->haveCityInLocalStorage($responseEN->data[0]->fields->uuid)){
            $this->entityManager->persist($city);
            $this->entityManager->flush();
        }

        return $city;
    }
    
    public function getCityByCriteria($criteria):? ServiceapiCity 
    {
        if (!array_key_exists('uuid', $criteria) 
                && !array_key_exists('name', $criteria)
                && !array_key_exists('descr', $criteria)
                && !array_key_exists('SCOATOU', $criteria)
        ) {
            throw new \Exception('Wrong incoming params were given! Suppoorted params  - uuid, SCOATOU');
        }

        $cityRu = $this->getCityByCriteriaAndLanguage($criteria, 'RU');
        $cityRu = json_decode($cityRu->getBody());
        $cityUa = $this->getCityByCriteriaAndLanguage($criteria, 'UA');
        $cityUa = json_decode($cityUa->getBody());
        $cityEn = $this->getCityByCriteriaAndLanguage($criteria, 'EN');
        $cityEn = json_decode($cityEn->getBody());

        //Если город не найден возвращаем false
        if(!$cityRu || !$cityUa || !$cityEn){
            return null;
        }

        if(!$cityRu->response->status){
            return null;
        }

        if(false == $cityRu->totalCountRecords){
            return null;
        }

        $cityRu = array_shift($cityRu->data);
        $cityUa = array_shift($cityUa->data);
        $cityEn = array_shift($cityEn->data);

        $city = $this->createCityFromRemoteData(
            $cityRu->fields->uuid,
            $cityRu->fields->code,
            $cityRu->fields->SCOATOU,
            $cityUa->fields->descr,
            $cityRu->fields->descr,
            $cityEn->fields->descr,
            00,
            $cityRu->fields->objectOwner->uuid,
            new \DateTime()
        );

        return $city;
    }

    /**
     * 
     * @param type $password
     */
    private function generateSign($password): string
    {
        return (string)sha1($password . ':' . date('Y-m-d'));
    }

    /**
     * Possible language values - RU/UA/EN - depending on this variable 
     * will be set result language.
     * 
     * @param type $cityName
     * @param type $language
     * @return GuzzleHttpResponse
     */
    private function getCity($cityName, $language='RU'): GuzzleHttpResponse {
        return $this
            ->httpClient
            ->request('POST', $this->remoteSideUrl, [
                RequestOptions::JSON => [
                    "keyAccount" => $this->openApiLogin,
                    "sign" => $this->generateSign($this->openApiPassword),
                    "request" => 'getData',
                    "type" => 'catalog',
                    "name" => 'cat_Cities',
                    "language" => $language,
                    'filter' => 
                        array (
                            array (
                              'name' => 'descr',
                              'comparison' => 'equal',
                              'leftValue' => $cityName,
                            ),
                        ),
                ]
            ]);
    }
    
    /**
     * 
     * @param type $criteria
     * @param type $language
     * @return GuzzleHttpResponse
     */
    private function getCityByCriteriaAndLanguage($criteria, $language='RU'): GuzzleHttpResponse
    {
        return $this
            ->httpClient
            ->request('POST', $this->remoteSideUrl, [
                RequestOptions::JSON => [
                    "keyAccount" => $this->openApiLogin,
                    "sign" => $this->generateSign($this->openApiPassword),
                    "request" => 'getData',
                    "type" => 'catalog',
                    "name" => 'cat_Cities',
                    "language" => $language,
                    'filter' => 
                        array (
                            array (
                              'name' => key($criteria),
                              'comparison' => 'equal',
                              'leftValue' => array_shift($criteria),
                            ),
                        ),
                ]
            ]);
    }
    
    private function createCityFromRemoteData(
            string $uuid,
            int $code,
            int $scoatou,
            string $titleUa,
            string $titleRu,
            string $titleEn,
            int $regionCode,
            string $regionUuid,
            \DateTime $updatedAt
    ):ServiceapiCity {
        $city = new ServiceapiCity();
        $city->setUuid($uuid);
        $city->setCode($code);
        $city->setScoatou($scoatou);
        $city->setTitleUa($titleUa);
        $city->setTitleRu($titleRu);
        $city->setTitleEn($titleEn);
        $city->setRegionUuid($uuid);
        $city->setUpdatedAt($updatedAt);
        
        $this->entityManager->persist($city);
        $this->entityManager->flush();

        return $city;
    }

    /**
     * 
     * @param type $cityUuid
     * @return type
     */
    private function haveCityInLocalStorage($cityUuid){
        return $this
            ->entityManager
            ->getRepository(ServiceapiCity::class)
            ->findOneBy([
                'uuid' => $cityUuid
            ]);
    }
}
