<?php


namespace App\Infrastructure\Services\Api;

use mysql_xdevapi\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use JMS\Serializer\SerializerBuilder;
use App\Domain\Queries\Query;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Domain\Queries\AbstractCommandQuery;
use function Doctrine\ORM\QueryBuilder;

class ApiService
{
    public const API_METHOD_V1_DOCUMENTS_ORDERS_CANCEL = "documents/orders_cancel";
    public const API_METHOD_V2_CAT_REGION = "cat_Region";
    public const API_METHOD_V2_CAT_AREAS_REGION = "cat_areasRegion";
    public const API_METHOD_V2_ORDER_STATUSES = "orderStatuses";
    public const API_METHOD_V2_ORDER_STATUSES_HISTORY = "getOrderStatusesHistory";
    public const API_METHOD_V2_CAT_CITIES = "cat_Cities";
    public const API_METHOD_V2_CAT_CITY_STREETS = "cat_cityStreets";
    public const API_METHOD_V2_CAT_CITY_REGIONS = "cat_cityRegions";
    public const API_METHOD_V2_CAT_BRANCH_TYPE = "cat_branchType";
    public const API_METHOD_V2_REQ_DEPARTMENTS = "req_Departments";
    public const API_METHOD_V2_REQ_DEPARTMENTS_LANG = "req_DepartmentsLang";

    private const API_BASE_PATTERN_URI_OTHER_VERSION = '/hs/api/v1';
    private const API_BASE_PATTERN_URI = '/';

    private const API_PARAMS = [
        self::API_METHOD_V1_DOCUMENTS_ORDERS_CANCEL => [
            'path' => '/api_pms/hs/api/v1/documents/orders_cancel',
            'allowedParams' => [],
            'entity' => 'set hear',
        ],
        self::API_METHOD_V2_CAT_REGION => [
            'path' => '/justin_pms/hs/v2/runRequest',
            'allowedParams' => [],
            'entity' => CatRegion::class,
        ],
        self::API_METHOD_V2_CAT_AREAS_REGION => [
            'path' => '/justin_pms/hs/v2/runRequest',
            'allowedParams' => [],
            'entity' => CatAreasRegion::class,
        ],
        self::API_METHOD_V2_CAT_CITY_STREETS => [
            'path' => '/justin_pms/hs/v2/runRequest',
            'allowedParams' => [],
            'entity' => CatCityStreets::class,
        ],
        self::API_METHOD_V2_ORDER_STATUSES => [
            'path' => '/justin_pms/hs/v2/runRequest',
            'allowedParams' => [],
            'entity' => OrderStatus::class,
        ],
        self::API_METHOD_V2_ORDER_STATUSES_HISTORY => [
            'path' => '/justin_pms/hs/v2/runRequest',
            'allowedParams' => [],
            'entity' => OrderStatusHistory::class,
        ],
        self::API_METHOD_V2_CAT_CITIES => [
            'path' => '/justin_pms/hs/v2/runRequest',
            'allowedParams' => [],
            'entity' => CatCities::class,
        ],
        self::API_METHOD_V2_CAT_CITY_REGIONS => [
            'path' => '/justin_pms/hs/v2/runRequest',
            'allowedParams' => [],
            'entity' => CatCityRegion::class,
        ],
        self::API_METHOD_V2_CAT_BRANCH_TYPE => [
            'path' => '/justin_pms/hs/v2/runRequest',
            'allowedParams' => [],
            'entity' => CatBranchType::class,
        ],
        self::API_METHOD_V2_REQ_DEPARTMENTS => [
            'path' => '/justin_pms/hs/v2/runRequest',
            'allowedParams' => [],
            'entity' => ReqDepartments::class,
        ],
        self::API_METHOD_V2_REQ_DEPARTMENTS_LANG => [
            'path' => '/justin_pms/hs/v2/runRequest',
            'allowedParams' => [],
            'entity' => ReqDepartmentsLang::class,
        ],
    ];

    /**
     *
     * @var type 
     */
    private $validateMsg = [];

    /**
     *
     * @var type 
     */
    private $isValid = null;

    /**
     *
     * @var type 
     */
    private $requestPath = null;

    /**
     *
     * @var type 
     */
    private $requestParams = null;

    /**
     *
     * @var type 
     */
    private $apiMethod = null;

    /**
     *
     * @var type Request
     */
    private $httpRequest;
    
    public function __construct(RequestStack $requestStack)
    {
        $this->httpRequest = $requestStack->getCurrentRequest();
        $this->requestPath = $this->httpRequest->getRequestUri(); // todo: fail on console
        $this->requestParams = json_decode($this->httpRequest->getContent());
    }

    public function getAllowedApiMethods()
    {
        return array_keys(self::API_PARAMS);
    }

    public function getAllowedApiV1Methods()
    {
        return [];
    }

    private function getAllowedApiV2Methods()
    {
        return [
            self::API_METHOD_V2_CAT_REGION,
            self::API_METHOD_V2_CAT_AREAS_REGION,
            self::API_METHOD_V2_ORDER_STATUSES,
            self::API_METHOD_V2_ORDER_STATUSES_HISTORY,
            self::API_METHOD_V2_CAT_CITIES,
            self::API_METHOD_V2_CAT_CITY_STREETS,
            self::API_METHOD_V2_CAT_CITY_REGIONS,
            self::API_METHOD_V2_CAT_BRANCH_TYPE,
            self::API_METHOD_V2_REQ_DEPARTMENTS,
            self::API_METHOD_V2_REQ_DEPARTMENTS_LANG,
        ];
    }

    /**
     * @return mixed|null
     */
    public function getRequestParams(): \stdClass
    {
        return $this->requestParams;
    }

    /**
     * 
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->httpRequest;
    }

    /**
     * 
     * @param AbstractCommandQuery $commandQuery
     * @return bool
     */
    public function validateCommandQuery(AbstractCommandQuery $commandQuery): bool
    {
        return $commandQuery->validate();
    }

    /**
     * @return array
     */
    public function getValidateMsg(): array
    {
        return $this->validateMsg;
    }

    /**
     * 
     * @return bool
     */
    protected function validate(): bool
    {
        $this->isValid = true;
        return true;
        $this->isValid = false;
       if (stristr($this->requestPath, SELF::API_BASE_PATTERN_URI)) {
            $this->isValid = true; // todo: rm after complete validation
            if ($this->requestParams instanceof \stdClass) {
                if (!isset($this->requestParams->name)) {
                    $this->isValid = false;
                    $this->validateMsg[] = "invalid data parameters in request: absent parameter <name>";
                } else {
                    foreach ($this->getAllowedApiV2Methods() as $apiV2MethodName) {
                        if (strtolower($this->requestParams->name) === strtolower($apiV2MethodName)) {
                            $this->apiMethod = $apiV2MethodName;
                            $this->isValid = true;
                            break;
                        }
                    }
                    if (!$this->isValid) {
                        $this->validateMsg[] = 'unexpected value in parameter <name>';
                    } else {
                        if (!isset($this->requestParams->keyAccount)) {
                            $this->isValid = false;
                            $this->validateMsg[] = "ERROR!!! Absent keyAccount";
                        } elseif (!isset($this->requestParams->sign)) {
                            $this->isValid = false;
                            $this->validateMsg[] = "ERROR!!! Absent sign";
                        }
                    }
                }

            } else {
                $this->isValid = true; // todo: rm after complete validation
                $this->validateMsg[] = "{HTTPСервис.v2.Модуль(84)}: Value is not of object type (Свойство)";
            }
       } elseif (stristr($this->requestPath, SELF::API_BASE_PATTERN_URI_OTHER_VERSION)) {

       } else {
            $this->isValid = false;
            $this->validateMsg[] = "Not detect api.";

            if (!isset($this->requestParams->api_key)) {
                $this->isValid = false;
                $this->validateMsg[] = "ERROR!!! Absent api_key"; // TODO: check msg
            }
        }

        return $this->isValid;
    }

    /**
     * 
     * @return bool
     */
    public function isValid(): bool
    {
        return true;
        if (is_null($this->isValid)) {
            $this->validate();
        }
        return $this->isValid;
    }

    /**
     * 
     * @return type
     * @throws \Exception
     */
    public function getEntity()
    {
        if ($this->isValid() && isset(self::API_PARAMS[$this->apiMethod]['entity'])) {
            return self::API_PARAMS[$this->apiMethod]['entity'];
        }

        throw new \Exception("Not valid apiService configure.");
    }

    /**
     * 
     * @return type
     */
    public function getApiMethod()
    {
        return $this->apiMethod;
    }
}