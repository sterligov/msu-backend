<?php

namespace App\Repository;

use App\Entity\PreviewImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PreviewImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method PreviewImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method PreviewImage[]    findAll()
 * @method PreviewImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PreviewImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PreviewImage::class);
    }

    // /**
    //  * @return PreviewImage[] Returns an array of PreviewImage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PreviewImage
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
