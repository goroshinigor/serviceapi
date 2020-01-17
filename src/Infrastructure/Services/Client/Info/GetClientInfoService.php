<?php

namespace App\Infrastructure\Services\Client\Info;

use App\Domain\DTO\ServiceApiResponseResultDTO;
use App\Infrastructure\Services\Api\ApiService;
use App\Infrastructure\Services\Remote\CrmApiIntegration\Client\GetInfoService;
/*
 *
 */
class GetClientInfoService {

    /**
     * 
     */
    private $infoService;
    
    /**
     * 
     * @param GetInfoService $infoService
     */
    public function __construct(GetInfoService $infoService) 
    {
        $this->infoService = $infoService;
    }

    /**
     * 
     */
    public function get(ApiService $apiService): ServiceApiResponseResultDTO
    {
        return new ServiceApiResponseResultDTO(
                $this->infoService->get(
                $apiService->getRequestParams()->data->memberId
            )
        );
    }
}
