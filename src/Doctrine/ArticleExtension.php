<?php


namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

final class ArticleExtension implements QueryCollectionExtensionInterface
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ArticleExtension constructor.
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param string|null $operationName
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        $this->addWhere($queryBuilder, $resourceClass, $queryNameGenerator);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $resourceClass
     * @param QueryNameGeneratorInterface $queryNameGenerator
     */
    private function addWhere(
        QueryBuilder $queryBuilder,
        string $resourceClass,
        QueryNameGeneratorInterface $queryNameGenerator
    ): void {
        if (
            Article::class !== $resourceClass
            || $this->security->isGranted('ROLE_USER')
        ) {
            return;
        }

        $parameterName = $queryNameGenerator->generateParameterName('tag_name');

        $subqueryDQL = $this->entityManager->createQueryBuilder()
            ->select('a.id')
            ->from('App\Entity\Article', 'a')
            ->leftJoin('a.tags', 't')
            ->where("t.name = :$parameterName")
            ->getDQL();

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->andWhere($queryBuilder->expr()->notIn("$rootAlias.id", $subqueryDQL))
            ->setParameter($parameterName, 'Закрытая страница');
    }
}