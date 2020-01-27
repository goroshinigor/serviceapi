<?php

namespace App\Infrastructure\Repository;

use App\Infrastructure\Entity\ServiceapiCity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ServiceapiPmsCities|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceapiPmsCities|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceapiPmsCities[]    findAll()
 * @method ServiceapiPmsCities[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceapiCityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceapiCity::class);
    }

    /**
     * 
     * @param type $cityName
     */
    public function getCityByName($cityName):? ServiceapiCity
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select(array('c'))
            ->from(ServiceapiCity::class, 'c')
            ->where($qb->expr()->orX(
                $qb->expr()->eq('c.title_ua', ':cityName'),
                $qb->expr()->eq('c.title_ru', ':cityName'),
                $qb->expr()->eq('c.title_en', ':cityName')
                 )
             )
             ->setParameter('cityName', $cityName)
             ->getQuery()
             ->getOneOrNullResult();
    }
}
