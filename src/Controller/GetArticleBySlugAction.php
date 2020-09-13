<?php


namespace App\Controller;

use App\DataProvider\ArticleItemDataProvider;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
            throw new NotFoundHttpException();
        }

        return $article;
    }
}