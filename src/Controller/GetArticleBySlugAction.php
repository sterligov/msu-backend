<?php


namespace App\Controller;

use App\DataProvider\ArticleItemDataProvider;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Security;

class GetArticleBySlugAction
{
    /**
     * @param Request $request
     * @param ArticleItemDataProvider $dataProvider
     * @param Security $security
     * @return Article|null
     */
    public function __invoke(Request $request, ArticleItemDataProvider $dataProvider, Security $security): ?Article
    {
        $slug = $request->get('slug', '');
        $article = $dataProvider->getItem(Article::class, $slug);

        if (!$article) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Not found');
        }

        $security->isGranted('ARTICLE_VIEW', $article);

        return $article;
    }
}