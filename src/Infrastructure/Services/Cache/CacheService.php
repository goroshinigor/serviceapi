<?php


namespace App\Infrastructure\Services\Cache;

use App\Infrastructure\Services\Common\MethodNameFromRequest;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\HttpFoundation\Request;
/**
 * Class CacheService.
 */
class CacheService
{
    private const ONE_MINUTE_IN_SECONDS = 60;

    private const TTL_DEFAULT = 'ttl_default';
    private const TTL_REGION = 'ttl_region';
    private const TTL_AREAS_REGION = 'ttl_areas_region';
    private const TTL_ORDER_STATUSES = 'ttl_order_statuses';
    private const TTL_ORDER_STATUSES_HISTORY = 'ttl_order_statuses_history';
    private const TTL_CITIES = 'ttl_cities';
    private const TTL_CITY_STREETS = 'ttl_city_streets';
    private const TTL_CITY_REGIONS = 'ttl_city_regions';
    private const TTL_DOCUMENTS_ORDERS_CANCEL = 'ttl_documents_order_cancel';
    private const TTL_BRANCH_TYPE = 'ttl_branch_type';
    private const TTL_REQ_DEPARTMENTS = 'ttl_req_departments';
    private const TTL_REQ_DEPARTMENTS_LANG = 'ttl_req_departments_lang';

    /**
     * @var RedisAdapter
     */
    private $cache;

    public const CACHE_REQUEST_PATH_PREFIX = 'api.cache.requests.by.content';
    public const CACHE_AUTHENTICATE_USERS = 'api.cache.authenticate.users';
    public const CACHE_AUTHENTICATE_USERS_DECORATED_BY_LOGIN = 'api.cache.authenticate.users.by.login';
    public const CACHE_AUTHENTICATE_USERS_DECORATED_BY_UUID = 'api.cache.authenticate.users.by.uuid';

    public const TTL_DEFAULT_MINUTE = 60;
    public const CACHE_AUTHENTICATE_USERS_TTL = 600;

    private $prefix = 'dev';

    /**
     * @var ttl_param_array;
     */
    private $ttl_param_array;

    /**
     *
     * @var type MethodNameFromRequest
     */
    private $nameService;
    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @param string $cache_uri
     * @param string $prefix
     * @param array $ttl_param_array
     */
    public function __construct(string $cache_uri, string $prefix, array $ttl_param_array, MethodNameFromRequest $nameService)
    {
        $this->nameService = $nameService;
        $this->cache = new RedisAdapter(
                RedisAdapter::createConnection('redis://localhost'),
                $this->prefix,
                self::TTL_DEFAULT_MINUTE
            );
    }

    /**
     * 
     * @param Request $request
     * @return string
     */
    public function getCachePathByRequest(Request $request): string
    {
        return self::CACHE_REQUEST_PATH_PREFIX . '.' . sha1($request->getContent());
    }

    /**
     * 
     * @param type $apiMethod
     * @return int
     */
    public function getTtlByApiMethod($apiMethod): int
    {
        return 600;
        $ttl = $this->ttl_param_array[SELF::TTL_DEFAULT] * SELF::ONE_MINUTE_IN_SECONDS;

        return intval($ttl);
    }

    /**
     * 
     * @param string $key
     * @return type
     */
    public function get(string $key)
    {
        $responseFromCache = $this->cache->getItem($key);
        if($responseFromCache->isHit()) {
            return $this->cache->getItem($key);
        }
    }

    /**
     * 
     * @param string $key
     * @param type $value
     * @param type $ttl
     * @return type
     */
    public function set(string $key, $value, $ttl = 60)
    {
        /** @var CacheItem $item */
        $item = $this->cache->getItem($key);
        $item->set($value);
        $item->expiresAfter($ttl);

        return $this->cache->save($item);
    }

    /**
     * 
     * @param Request $request
     * @return string
     */
    public function createKeyNameByRequest(Request $request): string
    {
        $key = json_decode($request->getContent());
        if(!$key){
            throw new \Exception('No Such Key present');
        }
        if ($key instanceof \stdClass) {
            unset($key->login);
            unset($key->sign);
            unset($key->datetime);
            return implode("-", [
                str_replace('\\','_',
                get_class($request)),
                sha1(serialize($key)),
                $this->nameService->get($request)
            ]);
        }
    }
}