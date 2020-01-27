<?php


namespace App\Infrastructure\Services\Auth;

use App\Application\Services\Api\ApiService;
use App\Application\Services\Cache\CacheService;
use App\Domain\DTO\DtoInterface;
use App\Domain\Entity\AutorisationList;
use App\Domain\Entity\CatAreasRegion;
use App\Domain\Entity\CatCities;
use App\Domain\Entity\CatCityRegion;
use App\Domain\Entity\CatCityStreets;
use App\Domain\Entity\CatRegion;
use App\Domain\Entity\OrderStatus;
use App\Domain\Entity\OrderStatusHistory;
use App\Domain\Entity\CatBranchType;
use App\Domain\Entity\ReqDepartments;
use App\Domain\Entity\ReqDepartmentsLang;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use JMS\Serializer\SerializerBuilder;
use App\Domain\Queries\Query;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Domain\Queries\AbstractCommandQuery;

class AuthService
{
    private $em;
    private $cache;
    private $authUser = null;

    public function __construct(EntityManagerInterface $em, CacheService $cache)
    {
        $this->em = $em;
        $this->cache = $cache;
    }


    public function authenticate($login, $sign, $uuid = null): bool
    {
        if (is_null($uuid)) {
            $usersDecoratedByLogin = $this->getUsersDecoratedByLogin();
            $user = $usersDecoratedByLogin[$login] ?? null;
            if ($user instanceof AutorisationList  && ($sign === $user->getSign())) {
                $this->authUser = $user;
            }

        } else {
            $usersDecoratedByUUID = $this->getUsersDecoratedByUUID();
            $user = $usersDecoratedByUUID[$uuid] ?? null;
            if ($user instanceof AutorisationList) {
                $this->authUser = $user;
            }
        }

        return $this->authUser instanceof AutorisationList;
    }

    private function getUsersDecoratedByUUID()
    {
        $cacheKey = CacheService::CACHE_AUTHENTICATE_USERS_DECORATED_BY_UUID;
        $users = $this->cache->get($cacheKey);
        if ($users === false) {
            /** @var AutorisationList $user */
            foreach ($this->getAllUsers() as $user) {
                $users[$user->getUuidClient()] = $user;
            }

            $this->cache->set($cacheKey, $users, CacheService::CACHE_AUTHENTICATE_USERS_TTL);
        }
    }

    private function getUsersDecoratedByLogin()
    {
        $cacheKey = CacheService::CACHE_AUTHENTICATE_USERS_DECORATED_BY_LOGIN;
        $users = $this->cache->get($cacheKey);
        if ($users === false) {
            /** @var AutorisationList $user */
            foreach ($this->getAllUsers() as $user) {
                $users[$user->getLogin()] = $user;
            }

            $this->cache->set($cacheKey, $users, CacheService::CACHE_AUTHENTICATE_USERS_TTL);
        }

        return $users;
    }

    private function getAllUsers(): array
    {
        $cacheKey = CacheService::CACHE_AUTHENTICATE_USERS;
        $users = $this->cache->get($cacheKey);
        if ($users === false) {
            $builder = $this->em->createQueryBuilder();
            $users = $builder->select(array('auth'))
                ->from(AutorisationList::class, 'auth')
                ->getQuery()->getResult();

            $this->cache->set($cacheKey, $users, CacheService::CACHE_AUTHENTICATE_USERS_TTL);
        }

        return $users;
    }

}