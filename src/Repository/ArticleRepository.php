<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
     * @param string $slug
     * @return Article|null
     */
    public function findBySlug(string $slug): ?Article
    {
        return $this->findOneBy([
            'slug' => $slug
        ]);
    }

    /**
     * @param string $type
     * @param int $limit
     * @param int $offset
     * @return Article[] Returns an array of Article objects
     */
    public function findByType(string $type, int $limit = 0, int $offset = 0): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.types', 't')
            ->where('t.type = :type')
            ->setParameter(':type', $type)
            ->orderBy('p.published_at', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult()
        ;
    }
}
