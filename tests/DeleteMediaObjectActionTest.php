<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Controller\DeleteMediaObjectAction;
use App\Repository\MediaObjectRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Request;

/**
 * @group unit
 * Class CreateMediaObjectActionTest
 * @package App\Tests
 */
class DeleteMediaObjectActionTest extends ApiTestCase
{
    public function testInvoke_ok()
    {
        $request = $this->getMockBuilder(Request::class)
            ->onlyMethods(['get'])
            ->getMock();

        $id = 9;
        $request->expects($this->once())
            ->method('get')
            ->with('id', 0)
            ->willReturn($id);

        $repository = $this->getMockBuilder(MediaObjectRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['hardDelete'])
            ->getMock();

        $repository->expects($this->once())
            ->method('hardDelete')
            ->with($this->equalTo($id))
            ->willReturn(true);

        $deleteAction = new DeleteMediaObjectAction();
        $deleteAction($request, $repository);
    }

    public function testInvoke_exception()
    {
        $request = $this->getMockBuilder(Request::class)
            ->onlyMethods(['get'])
            ->getMock();

        $id = 9;
        $request->expects($this->once())
            ->method('get')
            ->with('id', 0)
            ->willReturn($id);

        $repository = $this->getMockBuilder(MediaObjectRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();

        $repository->expects($this->once())
            ->method('find')
            ->willReturn(false);

        $deleteAction = new DeleteMediaObjectAction();

        $this->expectException(EntityNotFoundException::class);

        $deleteAction($request, $repository);
    }
}