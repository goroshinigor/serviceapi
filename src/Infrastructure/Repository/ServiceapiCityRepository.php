<?php

namespace App\Infrastructure\Repository;

use App\Infrastructure\Entity\ServiceapiCity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

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

    /**
     * Get all cities with region also if $filter 'activity'
     *
     * @param string $filter
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function get($filter = 'all'): ?array
    {
        $expr = $this->_em->getExpressionBuilder();

        $tableRegionName = $this->_em->getClassMetadata('App\Infrastructure\Entity\ServiceapiRegion')->getTableName();
        $subquery_filter = $this->_em->createQueryBuilder()
            ->select('MIN(r2.code) as code')
            ->from($tableRegionName, 'r2')
            ->groupBy('r2.uuid')
            ->getDQL();

        $tableName = $this->_em->getClassMetadata('App\Infrastructure\Entity\ServiceapiCity')->getTableName();
        $query = $this->_em->createQueryBuilder()
            ->select('c.uuid as uuid,
                             c.scoatou as SCOATOU,
                             c.region_uuid as parent_uuid,
                             c.title_ua as title_ua, 
                             r.title_ua as parent_title_ua,
                             c.title_ru as title_ru,
                             r.title_ru as parent_title_ru,
                             c.title_en as title_en, 
                             r.title_en as parent_title_en
                             ')
            ->from($tableName, 'c')
            ->leftJoin($tableRegionName, 'r', Expr\Join::ON, 'r.uuid = c.region_uuid')
            ->join(sprintf('(%s)', $subquery_filter), 'f_2', Expr\Join::ON, 'f_2.code = r.code');

        // Filter: get cities only when active Departament
        if (trim($filter) == 'activity') {
            $tableName = $this->_em->getClassMetadata('App\Infrastructure\Entity\ServiceapiBranches')->getTableName();
            $subquery = $this->_em->createQueryBuilder()
                ->select('1')
                ->from($tableName, 'b')
                ->where('b.city_uuid = c.uuid')
                ->orderBy('c.title_ua', 'DESC')
                ->getQuery()
                ->getDQL();

            $query->where($expr->exists($subquery));
        }

        $stmt = $this->_em->getConnection()->prepare($query->getDQL());
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
