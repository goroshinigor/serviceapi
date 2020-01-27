<?php

namespace App\Domain\ValueObjects\Route;

use App\Domain\ValueObjects\City\ICity;

/**
 * Description of LocalityPair
 *
 * @author i.goroshyn
 */
class PackageRoute {

    /**
     *
     * @var ICity
     */
    private $localityA;

    /**
     *
     * @var ICity
     */
    private $localityB;

    /**
     * 
     * @param ICity $localityA
     * @param ICity $localityB
     */
    public function __construct(ICity $localityA, ICity $localityB) {
        $this->localityA = $localityA;
        $this->localityB = $localityB;
    }

    /**
     * location from.
     * 
     * @return ICity
     */
    function getLocalityA(): ICity {
        return $this->localityA;
    }

    /**
     * location to.
     * 
     * @return ICity
     */
    function getLocalityB(): ICity {
        return $this->localityB;
    }


}
