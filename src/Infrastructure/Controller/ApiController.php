<?php

namespace App\Infrastructure\Controller;

use App\Domain\Exceptions\ClientNotFoundException;
use App\Infrastructure\Services\Legacy\ServiceAPILocalities;
use App\Infrastructure\Services\Legacy\ServiceApiAddEwToObserved;
use App\Infrastructure\Services\Legacy\ServiceApiGetObserversList;
use App\Infrastructure\Services\Legacy\ServiceApiRemoveEwFromObserved;
use App\Infrastructure\Services\Legacy\ServiceApiSendMessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use App\Domain\DTO\ServiceApiResponseDTO;
use App\Domain\DTO\ServiceApiResponseMessageDTO;
use App\Domain\DTO\ServiceApiResponseResultDTO;
use App\Domain\DTO\ServiceApiResponseStatusDTO;
use App\Domain\Exceptions\AddressNotFoundException;
use App\Infrastructure\Services\Legacy\ServiceAPIClientVerifyPhone;
use App\Infrastructure\Services\Api\ApiService;
use App\Infrastructure\Services\Cache\CacheService;
use App\Infrastructure\Services\Common\MethodNameFromRequest;
use App\Infrastructure\Services\Legacy\ServiceAPIGeocoding;
use App\Infrastructure\Services\Client\Info\GetClientInfoService;
use App\Infrastructure\Services\EW\EWCalculatorService;
use App\Domain\Exceptions\MethodException;
use App\Infrastructure\Services\Legacy\ServiceAPITracking;

/**
 * ApiController.
 */
class ApiController extends AbstractController
{
    /**
     * @var
     */
    private $logger;

    /**
     * @var
     */
    private $requestId;

    /**
     * @var  CacheService
     */
    private $cache;

    /**
     *
     * @var type Request
     */

    private $request;

    /**
     *
     * @var type MethodNameFromRequest
     */
    private $methodNameService;

    /**
     *
     * @var type geoService.
     */
    private $geoService;

    /**
     *
     * @var type GetClientInfoService.
     */
    private $clientInfoService;

    /**
     *
     * @var type EwCalculatorService.
     */
    private $ewCalculatorService;

    /**
     *
     * @var ServiceAPIClientVerifyPhone
     */
    private $verifyPhoneService;

    /**
     * @var ServiceApiAddEwToObserved
     */
    private $addEwToObservedService;

    /**
     * @var ServiceApiRemoveEwFromObserved
     */
    private $removeEwFromObservedService;

    /**
     * @var ServiceAPILocalities
     * @since 21.01.2020
     */
    private $serviceAPILocalities;

    /**
     * @var ServiceAPITracking
     */
    private $serviceAPITracking;

    private $getObserversListService;

    private $sendMessageService;

    /**
     *
     * @param LoggerInterface $logger
     * @param CacheService $cache
     * @param RequestStack $request
     * @param MethodNameFromRequest $methodNameService
     * @param ServiceAPIGeocoding $geoService
     * @param ServiceAPIClientVerifyPhone $verifyPhoneService
     * @param GetClientInfoService $clientInfoService
     * @param ServiceAPITracking $serviceAPITracking
     * @param EWCalculatorService $ewCalculatorService
     * @param EWCalculatorService $ewCalculatorService
     * @param ServiceApiAddEwToObserved $addEwToObservedService
     * @param ServiceApiRemoveEwFromObserved $removeEwFromObservedService
     * @param ServiceAPILocalities $serviceAPILocalities
     * @param ServiceApiGetObserversList $getObserversListService
     * @param ServiceApiSendMessageService $sendMessageService
     */
    public function __construct(
        LoggerInterface $logger,
        CacheService $cache,
        RequestStack $request,
        MethodNameFromRequest $methodNameService,
        ServiceAPIGeocoding $geoService,
        ServiceAPIClientVerifyPhone $verifyPhoneService,
        GetClientInfoService $clientInfoService,
        ServiceAPITracking $serviceAPITracking,
        EWCalculatorService $ewCalculatorService,
        ServiceApiAddEwToObserved $addEwToObservedService,
        ServiceApiRemoveEwFromObserved $removeEwFromObservedService,
        ServiceApiGetObserversList $getObserversListService,
        ServiceApiSendMessageService $sendMessageService,
        ServiceAPILocalities $serviceAPILocalities
    ){
        $this->logger = $logger;
        $this->cache = $cache;
        $this->request = $request->getCurrentRequest();
        $this->methodNameService = $methodNameService;
        $this->geoService = $geoService;
        $this->verifyPhoneService = $verifyPhoneService;
        $this->clientInfoService = $clientInfoService;
        $this->serviceAPITracking = $serviceAPITracking;
        $this->serviceAPILocalities = $serviceAPILocalities;
        $this->ewCalculatorService = $ewCalculatorService;
        $this->addEwToObservedService = $addEwToObservedService;
        $this->removeEwFromObservedService = $removeEwFromObservedService;
        $this->getObserversListService = $getObserversListService;
        $this->sendMessageService = $sendMessageService;
        $logger->info('Api.Create', [
            'requestID' => $this->requestId,
            'route' => $_SERVER['REQUEST_URI'],
            'started_ts' => round($_SERVER['REQUEST_TIME_FLOAT'], 2),
            'duration' => round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 2),
            'request' => $_REQUEST,
        ]);
    }

    /**
     * @Route("/v2", name="api")
     */
    public function index(ApiService $apiService)
    {
        $response = $this->wrongName(
            implode(',', $apiService->getValidateMsg())
        );
        $cachePath = $this->cache->createKeyNameByRequest($this->request);

        if ($apiService->isValid()) {
             $responseFromCache = $this->cache->get($cachePath);
            if (true == $responseFromCache) {
               $response = $responseFromCache->get($cachePath);
              } else {
            $response = $this->getData($apiService);
               }
            $ttl = $this->cache->getTtlByApiMethod($apiService->getApiMethod());
            $this->cache->set($cachePath, $response, $ttl);
        } else {
            $response = $this->wrongName(
                implode(',', $apiService->getValidateMsg())
            );
        }

        $this->logger->info('Api.Exit', [
            'requestID' => $this->requestId,
            'status' => $apiService->isValid() ? 'success' : 'fail',
            'duration' => round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 2),
            'response' => $response->getContent(),
        ]);

        return $response;
    }

    /**
     *
     * @param ApiService $apiService
     * @return Response
     * @throws \Exception
     */
    private function getData(ApiService $apiService)
    {
        $methodName = $this->methodNameService->get($this->request);

        switch ($methodName) {
            case 'send_message':
                $result = $this->sendMessageService->run($apiService);
                if ($result) $code = 1;
                return new JsonResponse(
                    new ServiceApiResponseDTO(
                        new ServiceApiResponseStatusDTO($code ?? 0),
                        new ServiceApiResponseMessageDTO(),
                        new ServiceApiResponseResultDTO(
                            []
                        )
                    ));
            case 'get_observers_list':
                return new JsonResponse(
                    new ServiceApiResponseDTO(
                        new ServiceApiResponseStatusDTO(true),
                        new ServiceApiResponseMessageDTO(),
                        new ServiceApiResponseResultDTO(
                            $this->getObserversListService->run($apiService)
                        )
                    ));
            case 'remove_ew_from_observed':
                return new JsonResponse(
                    new ServiceApiResponseDTO(
                        new ServiceApiResponseStatusDTO(true),
                        new ServiceApiResponseMessageDTO(),
                        new ServiceApiResponseResultDTO(
                            $this->removeEwFromObservedService->run($apiService)
                        )
                    ));
            case 'add_ew_to_observed':
                return new JsonResponse(
                    new ServiceApiResponseDTO(
                        new ServiceApiResponseStatusDTO(true),
                        new ServiceApiResponseMessageDTO(),
                        new ServiceApiResponseResultDTO(
                            $this->addEwToObservedService->run($apiService)
                        )
                    ));
            case 'client_verify_phone':
                $result = $this->verifyPhoneService->run($apiService);
                return new JsonResponse($result, 200);
            case "branches_locator":
                try {
                    return new JsonResponse(
                        new ServiceApiResponseDTO(
                            new ServiceApiResponseStatusDTO(true),
                            new ServiceApiResponseMessageDTO(),
                            new ServiceApiResponseResultDTO(
                                $this->geoService->run($apiService)
                            )
                        )
                    );
                } catch (AddressNotFoundException $ex) {
                    return new JsonResponse(
                        new ServiceApiResponseDTO(
                            new ServiceApiResponseStatusDTO(false),
                            new ServiceApiResponseMessageDTO(
                                "Не удалось найти адрес",
                                "Не вдалося знайти адресу",
                                "Could not find address",
                                10204
                            ),
                            new ServiceApiResponseResultDTO(null)
                        )
                    );
                } catch (\Exception $ex) {
                    return new JsonResponse(
                        new ServiceApiResponseDTO(
                            new ServiceApiResponseStatusDTO(false),
                            new ServiceApiResponseMessageDTO(),
                            new ServiceApiResponseResultDTO([$ex->getMessage()])
                        )
                    );
                }

            case "calculate_ew_price":
                return new JsonResponse(
                    new ServiceApiResponseDTO(
                        new ServiceApiResponseStatusDTO(1),
                        new ServiceApiResponseMessageDTO(),
                        $this->ewCalculatorService->get($apiService)
                    )
                );

            case "client_info":
                return new JsonResponse(
                    new ServiceApiResponseDTO(
                        new ServiceApiResponseStatusDTO(1),
                        new ServiceApiResponseMessageDTO(),
                        $this->clientInfoService->get($apiService)
                    )
                );

            case "localities":
                return new JsonResponse(
                    new ServiceApiResponseDTO(
                        new ServiceApiResponseStatusDTO(1),
                        new ServiceApiResponseMessageDTO(),
                        $this->serviceAPILocalities->get($apiService)
                    ));

            case "tracking":
                return new JsonResponse(
                    new ServiceApiResponseDTO(
                        new ServiceApiResponseStatusDTO(1),
                        new ServiceApiResponseMessageDTO(),
                        $this->serviceAPITracking->run($apiService)
                    )
                );
        }

        throw new \Exception('Object and/or action not found', 60001);
    }

    /**
     * @return Response
     */
    private function wrongName($msg = ''): Response
    {
        $msg = 'Error 280 + 20';
        return new Response($msg, Response::HTTP_OK);
    }
}
