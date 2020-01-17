<?php

namespace App\Infrastructure\Controller;

use App\Infrastructure\Services\Legacy\ServiceApiAddSendingToObserved;
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
use App\Domain\Exceptions\WrongPhoneNumberException;
use App\Infrastructure\Services\Legacy\ServiceAPIClientVerifyPhone;
use App\Infrastructure\Services\Api\ApiService;
use App\Infrastructure\Services\Cache\CacheService;
use App\Infrastructure\Services\Common\MethodNameFromRequest;
use App\Infrastructure\Services\Legacy\ServiceAPIGeocoding;
use App\Infrastructure\Services\Client\Info\GetClientInfoService;
use App\Domain\Exceptions\MethodException;

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
     * @var ServiceAPIClientVerifyPhone
     */
    private $verifyPhoneService;

    /**
     * @var ServiceApiAddSendingToObserved
     */
    private $addSendingToObservedService;

    /**
     *
     * @param LoggerInterface $logger
     * @param CacheService $cache
     * @param RequestStack $request
     * @param MethodNameFromRequest $methodNameService
     * @param ServiceAPIGeocoding $geoService
     * @param ServiceAPIClientVerifyPhone $verifyPhoneService
     * @param GetClientInfoService $clientInfoService
     * @param ServiceApiAddSendingToObserved $addSendingToObservedService
     */
    public function __construct(
        LoggerInterface $logger,
        CacheService $cache,
        RequestStack $request,
        MethodNameFromRequest $methodNameService,
        ServiceAPIGeocoding $geoService,
        ServiceAPIClientVerifyPhone $verifyPhoneService,
        GetClientInfoService $clientInfoService,
        ServiceApiAddSendingToObserved $addSendingToObservedService
    )
    {
        $this->logger = $logger;
        $this->cache = $cache;
        $this->request = $request->getCurrentRequest();
        $this->methodNameService = $methodNameService;
        $this->geoService = $geoService;
        $this->verifyPhoneService = $verifyPhoneService;
        $this->clientInfoService = $clientInfoService;
        $this->addSendingToObservedService = $addSendingToObservedService;
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
            // $responseFromCache = $this->cache->get($cachePath);
            //if (true == $responseFromCache) {
            //  $response = $responseFromCache->get($cachePath);
            //   } else {
            $response = $this->getData($apiService);
            //    }
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
     */
    private function getData(ApiService $apiService)
    {
        $methodName = $this->methodNameService->get($this->request);

        switch ($methodName) {
            case 'add_sending_to_observed':
                try {
                    $response =
                        new ServiceApiResponseDTO(
                            new ServiceApiResponseStatusDTO(true),
                            new ServiceApiResponseMessageDTO(),
                            new ServiceApiResponseResultDTO(
                                $this->addSendingToObservedService->run($apiService)
                            )
                        );
                } catch (WrongPhoneNumberException $e) {
                    return new JsonResponse(
                        new ServiceApiResponseDTO(
                            new ServiceApiResponseStatusDTO(false),
                            new ServiceApiResponseMessageDTO(
                                "Указанный телефон не соответствует формату +380999999999",
                                "Зазначений телефон не відповідає формату +380999999999",
                                "The specified phone does not match the format +380999999999",
                                60201
                            ),
                            new ServiceApiResponseResultDTO(null)
                        )
                    );
                } catch (\Exception $e) {
                    $response = new ServiceApiResponseDTO(
                        new ServiceApiResponseStatusDTO(0),
                        new ServiceApiResponseMessageDTO(null),
                        new ServiceApiResponseResultDTO(
                            (array)$e->getMessage()
                        )
                    );
                    return new JsonResponse($response);
                }
                return new JsonResponse($response, 200);
            case 'client_verify_phone':
                try {
                    $result = $this->verifyPhoneService->run($apiService);
                } catch (WrongPhoneNumberException $ex) {
                    return new JsonResponse(
                        new ServiceApiResponseDTO(
                            new ServiceApiResponseStatusDTO(false),
                            new ServiceApiResponseMessageDTO(
                                "Указанный телефон не соответствует формату +380999999999",
                                "Зазначений телефон не відповідає формату +380999999999",
                                "The specified phone does not match the format +380999999999",
                                60201
                            ),
                            new ServiceApiResponseResultDTO(null)
                        )
                    );
                } catch (\Exception $e) {
                    $response = new ServiceApiResponseDTO(
                        new ServiceApiResponseStatusDTO(0),
                        new ServiceApiResponseMessageDTO(null),
                        new ServiceApiResponseResultDTO(
                            (array)$e->getMessage()
                        )
                    );
                    return new JsonResponse($response);
                }
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

            case "client_info":
                return new JsonResponse(
                    new ServiceApiResponseDTO(
                        new ServiceApiResponseStatusDTO(1),
                        new ServiceApiResponseMessageDTO(),
                        $this->clientInfoService->get($apiService)
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
