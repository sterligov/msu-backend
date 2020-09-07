<?php


namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\RequestStack;

final class ArticleItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * ArticleItemDataProvider constructor.
     * @param ArticleRepository $articleRepository
     * @param RequestStack $requestStack
     */
    public function __construct(ArticleRepository $articleRepository, RequestStack $requestStack)
    {
        $this->articleRepository = $articleRepository;
        $this->requestStack = $requestStack;
    }

    /**
     * @param string $resourceClass
     * @param string|null $operationName
     * @param array $context
     * @return bool
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Article::class === $resourceClass && $operationName === 'get_by_slug';
    }

    /**
     * @param string $resourceClass
     * @param string $slug
     * @param string|null $operationName
     * @param array $context
     * @return object|void|null
     */
    public function getItem(string $resourceClass, $slug, string $operationName = null, array $context = []): ?Article
    {
        return $this->articleRepository->findBySlug($slug);
    }
}