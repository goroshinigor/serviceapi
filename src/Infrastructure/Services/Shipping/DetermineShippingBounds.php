<?php

namespace App\Infrastructure\Services\Shipping;

use App\Domain\Queries\CalculateEWPriceQuery;
use App\Infrastructure\Entity\ServiceapiCity;
use Doctrine\ORM\EntityManagerInterface;
use App\Infrastructure\Services\Remote\PMSIntegration\GetLocationService;

/**
 * Description of DetermineShippingBounds
 *
 * @author i.goroshyn
 */
class DetermineShippingBounds {

    /**
     *
     * @var type 
     */
    private $entityManager;

    /**
     *
     * @var GetLocationService 
     */
    private $locationService;

    /**
     * 
     * @param EntityManagerInterface $entitymanager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        GetLocationService $locationService
    ) {
        $this->entityManager = $entityManager;
        $this->locationService = $locationService;
    }

    /**
     * WITHIN_COUNTRY (Huge prices). 
     */
    public const WITHIN_COUNTRY = 1;

    /**
     * WITHIN_CITY (Tiny prices).
     */
    public const WITHIN_CITY = 2;

    /**
     * WITHIN_CITY (Tiny prices).
     */
    public const DEFAULT_BOUNDS = 1;

    /**
     * function getBounds.
     */
    public function getBounds(CalculateEWPriceQuery $query): int
    {
        $locationA = false;
        $locationB = false;
        $locationA = $this
            ->entityManager
            ->getRepository(ServiceapiCity::class)
                ->getCityByName($query->getLocationA());

        $locationB = $this
            ->entityManager
            ->getRepository(ServiceapiCity::class)
                ->getCityByName($query->getLocationB());

        if(false == $locationA){
            $locationA = $this->locationService->getByName($query->getLocationA());
        }

        if(false == $locationB){
            $locationB = $this->locationService->getByName($query->getLocationB());
        }

        if(false == $locationA || false == $locationB){
            throw new \Exception('No such city exists!');
        } elseif (true == $locationA->equalsTo($locationB)) {
            return self::WITHIN_CITY;
        } else {
            return self::WITHIN_COUNTRY;   
        }
    }
}
