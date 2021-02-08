<?php

namespace Vibbe\ACL\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Vibbe\ACL\Entity\ACLRole;

/**
 * @method ACLRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method ACLRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method ACLRole[]    findAll()
 * @method ACLRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ACLRoleRepository  extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ACLRole::class);
    }

//    public function findOneBySlug(string $slug): ?ACLRole
//    {
//        return $this->createQueryBuilder('role')
//            ->andWhere('role.slug = :val')
//            ->setParameter('val', $slug)
//            ->getQuery()
//            ->getOneOrNullResult();
//    }

    public function findBySlug(string $slug)
    {
        return $this->createQueryBuilder('role')
            ->andWhere('role.slug = :val')
            ->setParameter('val', $slug)
            ->getQuery()->getResult();
    }

    public function checkWhetherSuchRoleExists(string $slug, int $excludeId = null)
    {
        $query = $this->createQueryBuilder('role')
            ->andWhere('role.slug = :val')
            ->setParameter('val', $slug);
        if($excludeId) {
            $query->andWhere('role.id <> :idExclude')->setParameter('idExclude', $excludeId);

        }
        $results = $query->getQuery()->getResult();

        return ($results);
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function hydrateQuery(QueryBuilder $queryBuilder)
    {
//        if($this->disableDeletedAtFilter === false) {
//            $queryBuilder
//                ->where($this->alias.'.deletedAt IS NULL');
//        }
    }

}
