<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findByUsername(string $username): array
    {
        return $this->createQueryBuilder('u')
                ->andWhere('u.username=:username')
                ->setParameter(':username', $username)
                ->getQuery()
                ->getResult();
    }

    /**
     * @param $user
     * @return array
     */
    public function findAllUsers($user = null): array
    {
         $qb = $this->createQueryBuilder('u')
            ->andWhere("u.username!='anonym'");

        if(!is_null($user)){
            $qb->andWhere("u!=:user")
                ->setParameter('user', $user);
        }

        return $qb->getQuery()
                    ->getResult();
    }

    public function findByPartUsername($part, $user = null): array
    {
        $qb = $this->createQueryBuilder('u')
            ->andWhere('u.username LIKE :part')
            ->setParameter(':part', '%'.$part.'%');

            if(!is_null($user)){
                $qb->andWhere("u!=:user")
                    ->setParameter('user', $user);
            }

        return $qb->andWhere("u.username!='anonym'")
                ->getQuery()
                ->getResult();
    }
}
