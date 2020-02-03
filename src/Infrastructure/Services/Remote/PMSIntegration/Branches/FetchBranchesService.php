<?php

namespace App\Infrastructure\Services\Remote\PMSIntegration\Branches;

use Doctrine\ORM\EntityManagerInterface;
use App\Infrastructure\Services\Remote\PMSIntegration\PMSFetcher;
use App\Infrastructure\Services\Remote\PMSIntegration\Authorization\GenerateSignFromPassword;

/**
 * Description of GetBranchesService
 *
 * @author i.goroshyn
 * 
 * Class GetLocationService.
 */
class FetchBranchesService {

    /**
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     *
     * @var PMSFetcher
     */
    private $pmsFetcher;

    /**
     *
     * @var GenerateSignFromPassword
     */
    private $signGenerator;

    /**
     * 
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        PMSFetcher $pmsFetcher,
        GenerateSignFromPassword $signGenerator
    ) {
        $this->entityManager = $entityManager;
        $this->pmsFetcher = $pmsFetcher;
        $this->signGenerator = $signGenerator;
    }

    /**
     * 
     * @return type
     */
    public function fetchBranchesFromPMS()
    {
        $jsonPost = array(
            "keyAccount" => 'OPENAPI',
            "sign" => $this->generateSign('RIAneVEs'),
            "request" => 'getData',
            "type" => 'request',
            "name" => 'req_DepartmentsLang',
            'TOP' => 1000,
            "params" => [
                "language" => "RU"
            ],
        );

        return $this->pmsFetcher->fetch($jsonPost);
    }

    /**
     * 
     * @param type $password
     * @return type
     */
    private function generateSign($password)
    {
        return $this->signGenerator->generate($password);
    }
}
