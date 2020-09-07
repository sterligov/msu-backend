<?php

namespace App\Tests;

use \ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\MediaObject;
use App\Services\MediaObjectContentUrlResolver;
use Vich\UploaderBundle\Storage\StorageInterface;


/**
 * @group unit
 * Class MediaObjectContentUrlResolverTest
 * @package App\Tests
 */
class MediaObjectContentUrlResolverTest extends ApiTestCase
{
    public function testResolve()
    {
        $path = '/media/2020/01/image.png';

        $mock = $this->getMockForAbstractClass(StorageInterface::class);
        $mock->method('resolveUri')
            ->willReturn($path);

        $resolver = new MediaObjectContentUrlResolver($mock);
        $mediaObject = $this->createMock(MediaObject::class);
        $url = $resolver->resolve($mediaObject);

        $this->assertEquals("{$_ENV['URL']}$path", $url);
    }
}