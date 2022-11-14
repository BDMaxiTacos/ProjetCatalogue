<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @return Article[]
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
    private function findVisibleQuery($id_shop): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.shop=:id_shop')
            ->setParameter(':id_shop', $id_shop);
    }

    public function findById($id_shop, $id_article): array
    {
        return $this->findVisibleQuery($id_shop)
            ->andWhere('a.id=:id_article')
            ->setParameter('id_article', $id_article)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param mixed|null $search
     * @param mixed|null $shop
     * @return array
     */
    public function getArticlesRequested(mixed $search = null, mixed $shop = null): array
    {
         $qb = $this->createQueryBuilder('a');

        if(!is_null($shop)) {
            $qb->andWhere('a.shop=:shop')
                ->setParameter('shop', $shop);
        }


        if(!is_null($search)) {
            $qb->andWhere('a.titre LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery()
            ->getResult();
    }
}
