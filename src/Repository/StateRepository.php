<?php

namespace App\Repository;

use App\Entity\State;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method State|null find($id, $lockMode = null, $lockVersion = null)
 * @method State|null findOneBy(array $criteria, array $orderBy = null)
 * @method State[]    findAll()
 * @method State[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, State::class);
    }

    /**
     * @return State[]
     */
    public function getPaginatedProperties($id_shop, $page, $limit): array
    {
        $query =  $this->findVisibleQuery($id_shop)
            ->setFirstResult(($page*$limit)-$limit)
            ->setMaxResults($limit);

        return $query->getQuery()->getResult();
    }

    /**
     * @return QueryBuilder
     */
    private function findVisibleQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('st');
    }

    public function findById($id_state): array
    {
        return $this->findVisibleQuery($id_state)
            ->andWhere('st.id=:id_state')
            ->setParameter(':id_state', $id_state)
            ->getQuery()
            ->getResult();
    }

    public function findByName($name_state): array
    {
        return $this->findVisibleQuery()
            ->andWhere('st.description=:name_state')
            ->setParameter(':name_state', $name_state)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return State[]
     */
    public function findAllStates(): array
    {
        return $this->findVisibleQuery()
            ->getQuery()
            ->getResult();
    }

    public function findFirstVisible(): array
    {
        return $this->findVisibleQuery()
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return State[] Returns an array of State objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?State
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
