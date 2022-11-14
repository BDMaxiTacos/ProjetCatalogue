<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Shop;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @return QueryBuilder
     */
    private function findVisibleQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('o');
    }

    /**
     * @return Order[]
     */
    public function findAllVisible(): array
    {
        return $this->findVisibleQuery()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $user
     * @param bool $type
     * @return Order[]
     */
    public function findByUser($user, bool $type = true): array
    {
         $qb = $this->findVisibleQuery()
            ->andWhere('o.user=:user')
            ->setParameter('user', $user);

         if($type){
             $qb->andWhere('o.state!=1');
         }

         return $qb->orderBy('o.shop', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Order[]
     */
    public function findByState($state): array
    {
        $qb = $this->findVisibleQuery()
            ->andWhere('o.state!=1')
            ->andWhere('o.state!=5');

        if($state){
            $qb->andWhere('o.state=:state')
                ->setParameter('state', $state->getId());
        }

        return $qb->getQuery()
                ->getResult();
    }

    /**
     * @param $shop
     * @return Order[]
     */
    public function findByShop($shop): array
    {
        return $this->findVisibleQuery()
            ->andWhere('o.shop=:shop')
            ->setParameter('shop', $shop->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $shop
     * @param $user
     * @return Order[]
     */
    public function findCartByShop($shop, $user): array
    {
        return $this->findVisibleQuery()
            ->andWhere('o.user=:user')
            ->setParameter('user', $user->getId())
            ->andWhere('o.state=:state')
            ->setParameter('state', 1)
            ->andWhere('o.shop=:shop')
            ->setParameter('shop', $shop->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     * @return Order[]
     */
    public function findCartById($id): array
    {
        return $this->findVisibleQuery()
            ->andWhere('o.id=:id')
            ->setParameter('id', $id)
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
