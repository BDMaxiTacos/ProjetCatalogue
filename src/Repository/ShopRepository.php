<?php

namespace App\Repository;

use App\Entity\Shop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Shop|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shop|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shop[]    findAll()
 * @method Shop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shop::class);
    }

    /**
     * @return Shop[]
     */
    public function getPaginatedProperties($page, $limit): array
    {
        $query =  $this->findVisibleQuery()
            ->setFirstResult(($page*$limit)-$limit)
            ->setMaxResults($limit);

        return $query->getQuery()->getResult();
    }

    /**
     * @return QueryBuilder
     */
    private function findVisibleQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('s');
    }

    /**
     * @return Shop[]
     */
    public function findAllVisible(): array
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

    public function findById($id){
        return $this->findVisibleQuery()
            ->andWhere('s.id=:id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getResult();
    }

    public function findByPartName($part): array
    {
        return $this->findVisibleQuery()
            ->andWhere('s.name LIKE :part')
            ->setParameter(':part', '%'.$part.'%')
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return Shop[] Returns an array of Shop objects
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
    public function findOneBySomeField($value): ?Shop
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
