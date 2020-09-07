<?php


namespace App\Controller;


use App\Repository\MediaObjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class DeleteMediaObjectAction
{
    /**
     * @param Request $request
     * @param MediaObjectRepository $repository
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function __invoke(Request $request, MediaObjectRepository $repository)
    {
        $id = $request->get('id', 0);
        $repository->hardDelete($id);
    }
}