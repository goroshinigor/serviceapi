<?php

namespace App\Domain\Services\Client;

use App\Domain\DTO\ServiceApiResponseDTO;
use App\Domain\Aggregates\Client\Client;

/**
 * 
 */
class Info 
{
    /**
     * 
     * @param Client $client
     * @return \App\Domain\Services\Client\ServiceApiResponseDTOs
     */
    public function get(Client $client): ServiceApiResponseDTO
    {
        
    }
}
