<?php

namespace App\Infrastructure\Repository;

use App\Infrastructure\Entity\ServiceapiRegion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ServiceapiRegion|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceapiRegion|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceapiRegion[]    findAll()
 * @method ServiceapiRegion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceapiRegionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceapiRegion::class);
    }

    public function get()
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.cities', 'c')
            ->addSelect('c')
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
