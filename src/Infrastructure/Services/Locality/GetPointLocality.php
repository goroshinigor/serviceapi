<?php

namespace App\Infrastructure\Services\Locality;

use App\Domain\Queries\CalculateEWPriceQuery;
use App\Infrastructure\Entity\ServiceapiCity;
use \App\Domain\ValueObjects\City\ICity;
use App\Infrastructure\Services\Api\ApiService;
use Doctrine\ORM\EntityManagerInterface;
use App\Infrastructure\Services\Remote\PMSIntegration\GetLocationService;
use App\Domain\ValueObjects\Route\PackageRoute;
use App\Domain\Exceptions\LocationNotFoundException;

/**
 * Description of GetPointLocality
 *
 * @author i.goroshyn
 */
class GetPointLocality {

    /**
     * LOCATION_FROM_DOES_NOT_EXIST_RU.
     */
    public const LOCATION_FROM_DOES_NOT_EXIST_RU = 'Нет данных о населенном пункте отправки.';

    /**
     * LOCATION_FROM_DOES_NOT_EXIST_UA.
     */
    public const LOCATION_FROM_DOES_NOT_EXIST_UA = 'Відсутні дані про населений пункт відправки.';

    /**
     * LOCATION_FROM_DOES_NOT_EXIST_EN.
     */
    public const LOCATION_FROM_DOES_NOT_EXIST_EN = 'No data about the place of departure.';

    /**
     * LOCATION_FROM_DOES_NOT_EXIST_RU.
     */
    public const LOCATION_TO_DOES_NOT_EXIST_RU = 'Нет данных о населенном пункте доставки.';

    /**
     * LOCATION_FROM_DOES_NOT_EXIST_UA.
     */
    public const LOCATION_TO_DOES_NOT_EXIST_UA = 'Відсутні дані про населений пункт доставки.';

    /**
     * LOCATION_FROM_DOES_NOT_EXIST_EN.
     */
    public const LOCATION_TO_DOES_NOT_EXIST_EN = 'No location of delivery.';

    /**
     *
     * @var type EntityManagerInterface.
     */
    private $entityManager;

    /**
     *
     * @var type EntityManagerInterface.
     */
    private $locationService;

    /**
     * 
     * LOCATION_ERROR - текст ошибки.
     */
    private const LOCATION_ERROR = 'Одно из трех должно быть указано!';

    /**
     *
     * @var ServiceapiCity
     */
    private $locality_A;

    /**
     *
     * @var ServiceapiCity
     */
    private $locality_B;

    /**
     * 
     * @param \App\Infrastructure\Services\Locality\EntityManagerInterface 
     * $entityManager
     */
    public function __construct(
            EntityManagerInterface $entityManager,
            GetLocationService $locationService
    ) {
        $this->entityManager = $entityManager;
        $this->locationService = $locationService;
    }
    /**
     * 
     * @param CalculateEWPriceQuery $query
     * @return ServiceapiCity
     */
    public function getLocality(ApiService $apiService): PackageRoute
    {
        $data = $apiService->getRequestParams()->data;
        $point_a_locality = false;
        $point_b_locality = false;
        if(array_key_exists('point_a_locality_name', $data)){
            $point_a_locality = $this->searchLocally([
                'title_ru' => $data->point_a_locality_name
            ]);
            if(false == $point_a_locality){
                $point_a_locality = $this->searchLocally([
                    'title_ua' => $data->point_a_locality_name
                ]);   
            }
            if(false == $point_a_locality){
                $point_a_locality = $this->searchLocally([
                    'title_en' => $data->point_a_locality_name
                ]);
            }
            if(false == $point_a_locality){
                $point_a_locality = $this->searchRemotely([
                    'descr' => $data->point_a_locality_name
                ]);
            }
        } elseif (array_key_exists('point_a_locality_uuid', $data)){
            $point_a_locality = $this->searchLocally([
                'uuid' => $data->point_a_locality_uuid
            ]);
            if(false == $point_a_locality){
                $point_a_locality = $this->searchRemotely([
                    'uuid' => $data->point_a_locality_uuid
                ]);
            }
        } elseif (array_key_exists('point_a_locality_scoatou', $data)){
            $point_a_locality = $this->searchLocally([
                'scoatou' => $data->point_a_locality_scoatou
            ]);
            if(false == $point_a_locality){
                $point_a_locality = $this->searchRemotely([
                    'SCOATOU' => $data->point_a_locality_scoatou
                ]);
            }
        } else {
            throw new \Exception('Locality point A not identified');
        }

        if(array_key_exists('point_b_locality_name', $data)){
            $point_b_locality = $this->searchLocally([
                'title_ru' => $data->point_b_locality_name
            ]);
            if(false == $point_b_locality){
                $point_b_locality = $this->searchLocally([
                    'title_ua' => $data->point_b_locality_name
                ]);
            }
            if(false == $point_b_locality){
                $point_b_locality = $this->searchLocally([
                    'title_en' => $data->point_b_locality_name
                ]);
            }
            if(false == $point_b_locality){
                $point_b_locality = $this->searchRemotely([
                    'name' => $data->point_b_locality_name
                ]);
            }
        } elseif (array_key_exists('point_b_locality_uuid', $data)){
            $point_b_locality = $this->searchLocally([
                'uuid' => $data->point_b_locality_uuid
            ]);
            if(false === $point_a_locality){
                $point_b_locality = $this->searchRemotely([
                    'uuid' => $data->point_b_locality_uuid
                ]);
            }
        } elseif (array_key_exists('point_b_locality_scoatou', $data)){
            $point_b_locality = $this->searchLocally([
                'scoatou' => $data->point_b_locality_scoatou
            ]);
            if(false == $point_a_locality){
                $point_b_locality = $this->searchRemotely([
                    'SCOATOU' => $data->point_b_locality_scoatou
                ]);
            }
        } else {
            throw new \Exception('Locality point B not identified');
        }

        if(!$point_a_locality instanceof ServiceapiCity){
            throw new LocationNotFoundException(
                self::LOCATION_FROM_DOES_NOT_EXIST_RU . '++' .
                self::LOCATION_FROM_DOES_NOT_EXIST_UA . '++' .
                self::LOCATION_FROM_DOES_NOT_EXIST_EN, 
                60403
            );
        }

        if(!$point_b_locality instanceof ServiceapiCity){
            throw new LocationNotFoundException(
                self::LOCATION_TO_DOES_NOT_EXIST_RU . '++' .
                self::LOCATION_TO_DOES_NOT_EXIST_UA . '++' .
                self::LOCATION_TO_DOES_NOT_EXIST_EN, 
                60404
            );
        }

        return new PackageRoute($point_a_locality, $point_b_locality);
    }

    /**
     * 
     * @param array $criteria - example ['key'=>'value']
     * @return ICity|null
     */
    private function searchLocally(array $criteria):? ICity
    {
        return $this
            ->entityManager
            ->getRepository(ServiceapiCity::class)
            ->findOneBy($criteria);
    }

    /**
     * 
     * @param array $criteria - example ['key'=>'value']
     * @return ICity|null
     */
    private function searchRemotely(array $criteria):? ICity
    {
        return $this->locationService->getCityByCriteria($criteria);
    }
}
