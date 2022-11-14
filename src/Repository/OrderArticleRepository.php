<?php

namespace App\Repository;

use App\Entity\OrderArticle;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderArticle[]    findAll()
 * @method OrderArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderArticle::class);
    }

    /**
     * @return QueryBuilder
     */
    private function findVisibleQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('oa');
    }

    /**
     * @return OrderArticle[]
     */
    public function findAllVisible(): array
    {
        return $this->findVisibleQuery()
            ->getQuery()
            ->getResult();
    }

    public function findByOrderAndArticle($order, $article): array
    {
        return $this->findVisibleQuery()
            ->andWhere('oa.article=:article')
            ->setParameter(':article', $article)
            ->andWhere('oa.order=:order')
            ->setParameter(':order', $order)
            ->getQuery()
            ->getResult();
    }

    public function getOAs($order): array
    {
        return $this->findVisibleQuery()
            ->andWhere('oa.order=:order')
            ->setParameter(':order', $order)
            ->getQuery()
            ->getResult();
    }

    public function getOAExists($order, $article): array
    {
        return $this->findVisibleQuery()
            ->andWhere('oa.article=:article')
            ->setParameter(':article', $article)
            ->andWhere('oa.order=:order')
            ->setParameter(':order', $order)
            ->getQuery()
            ->getResult();
    }

    public function getLastCreatedOA(){
        return $this->findVisibleQuery()
            ->orderBy('oa.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Order[] Returns an array of Order objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
