<?php

namespace App\Infrastructure\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Infrastructure\Entity\ServiceapiBranch;

/**
 * 
 */
class ServiceapiBranchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceapiBranch::class);
    }

    /**
     * 
     * @param type $banch
     * @return bool
     */
    public function hasBranch($branchId): bool
    {
        return (bool)$this
            ->getEntityManager()
            ->createQueryBuilder('b')
            ->select(['count(b.deliveryBranchId)'])
            ->from(ServiceapiBranch::class, 'b')
            ->where('b.deliveryBranchId = :branchId')
            ->setParameter('branchId', $branchId)
            ->getQuery()
            ->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_SINGLE_SCALAR) > 0;
    }

    /**
     * 
     */
    public function clearAll(): bool
    {
        $classMetaData = $this->getEntityManager()->getClassMetadata(ServiceapiBranch::class);
        $connection = $this->getEntityManager()->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSql($classMetaData->getTableName());
            $connection->executeUpdate($q);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();

            return true;
        }
        catch (\Exception $e) {
            $connection->rollback();
        }

        return false;
    }
}
