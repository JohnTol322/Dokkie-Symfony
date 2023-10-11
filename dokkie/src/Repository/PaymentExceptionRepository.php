<?php

namespace App\Repository;

use App\Entity\PaymentException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaymentException>
 *
 * @method PaymentException|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentException|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentException[]    findAll()
 * @method PaymentException[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentExceptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentException::class);
    }

//    /**
//     * @return PaymentException[] Returns an array of PaymentException objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PaymentException
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
