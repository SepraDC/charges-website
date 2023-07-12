<?php

namespace App\Repository;

use App\Entity\Bank;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Bank|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bank|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bank[]    findAll()
 * @method Bank[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BankRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bank::class);
    }

    // /**
    //  * @return Bank[] Returns an array of Bank objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Bank
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @param User $user
     * @return Bank[]|null
     */
    public function findByUser(UserInterface $user): ?array
    {
        return $this->createQueryBuilder('b')
            ->join('b.charges', 'c')
            ->join('c.user', 'u')
            ->andWhere('u = :user')
            ->setParameter('user', $user)
            ->orderBy('b.name', 'ASC')
            ->groupBy('b.id')
            ->getQuery()->getResult();
    }
}
