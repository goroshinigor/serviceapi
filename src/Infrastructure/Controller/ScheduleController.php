<?php

namespace App\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Domain\DTO\ServiceApiResponseDTO;
use App\Domain\DTO\ServiceApiResponseStatusDTO;
use App\Domain\DTO\ServiceApiResponseMessageDTO;
use App\Domain\DTO\ServiceApiResponseResultDTO;
use App\Domain\Exceptions\MethodException;
use App\Infrastructure\Services\Common\MethodNameFromRequest;
use App\Infrastructure\Services\Remote\PMSIntegration\Branches\FetchBranchesService as PMSFetchBranchesService;
use App\Infrastructure\Services\Remote\AttikaIntegration\Branches\FetchBranchesService as AttikaFetchBranchesService;
use App\Infrastructure\Services\Remote\PMSIntegration\Branches\UpdateBranchesService;

/**
 * ScheduleController.
 */
class ScheduleController extends AbstractController
{
    /**
     * RequestStack
     */
    private $request;
    
    /**
     * MethodNameFromRequest
     */
    private $methodNameService;

    /**
     *
     * @var PMSFetchBranchesService 
     */
    private $PMSFetchBranchesService;

    /**
     *
     * @var AttikaFetchBranchesService 
     */
    private $attikaFetchBranchesService;

    /**
     *
     * @var UpdateBranchesService 
     */
    private $updateBranchesService;
    
    /**
     * 
     */
    public function __construct(
        RequestStack $request,
        MethodNameFromRequest $methodNameService,
        PMSFetchBranchesService $PMSFetchBranchesService,
        AttikaFetchBranchesService $attikaFetchBranchesService,
        UpdateBranchesService $updateBranchesService
    ) {
        $this->request = $request->getCurrentRequest();
        $this->methodNameService = $methodNameService;
        $this->PMSFetchBranchesService = $PMSFetchBranchesService;
        $this->attikaFetchBranchesService = $attikaFetchBranchesService;
        $this->updateBranchesService = $updateBranchesService;
    }

    /**
     * @Route("/v2/schedule", name="schedule")
     */
    public function updateBranches()
    {
    $methodName = $this->methodNameService->get($this->request);

        if('update_branches' == $methodName)
        {
            $PMSBranches = $this->PMSFetchBranchesService->fetchBranchesFromPMS();
            $attikaBranches = $this->attikaFetchBranchesService->fetchBranchesFromAttika($PMSBranches);

            $this->updateBranchesService->saveToLocalStorage($PMSBranches, $attikaBranches);

            return new JsonResponse(
                new ServiceApiResponseDTO(
                    new ServiceApiResponseStatusDTO(201),
                    new ServiceApiResponseMessageDTO(),
                    new ServiceApiResponseResultDTO($result)
                )
            );
        }

        throw new MethodException();
    }
}
