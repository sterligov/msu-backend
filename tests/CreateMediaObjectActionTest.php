<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Controller\CreateMediaObjectAction;
use App\Entity\MediaObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @group unit
 * Class CreateMediaObjectActionTest
 * @package App\Tests
 */
class CreateMediaObjectActionTest extends ApiTestCase
{
    public function testInvoke_ok()
    {
        $request = $this->getMockBuilder(Request::class)
            ->onlyMethods(['get'])
            ->getMock();

        $filename = 'test_filename.txt';
        $request->expects($this->once())
            ->method('get')
            ->with($this->equalTo('file'))
            ->willReturn($filename);

        $request->files = $request;

        $objectAction = new CreateMediaObjectAction($request);
        $mediaObject = $objectAction($request);

        $this->assertInstanceOf(MediaObject::class, $mediaObject);
        $this->assertEquals($filename, $mediaObject->file);
    }

    public function testInvoke_exception()
    {
        $request = $this->getMockBuilder(Request::class)
            ->onlyMethods(['get'])
            ->getMock();

        $request->expects($this->once())
            ->method('get')
            ->with($this->equalTo('file'))
            ->willReturn('');

        $request->files = $request;

        $objectAction = new CreateMediaObjectAction($request);

        $this->expectException(BadRequestHttpException::class);

        $objectAction($request);
    }
}