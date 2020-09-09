<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Services\Postman;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @group unit
 * Class PostmanTest
 * @package App\Tests
 */
class PostmanTest extends ApiTestCase
{
    public function testSend_TransportException()
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_BAD_GATEWAY);

        $client = $this->createMock(HttpClientInterface::class);
        $client->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $logger = $this->createMock(LoggerInterface::class);

        $message = $this->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFrom', 'getSubject', 'getTo', 'toString'])
            ->setMethods(['getAddress'])
            ->getMock();

        $message->expects($this->once())
            ->method('getFrom')
            ->willReturn([$message]);

        $message->expects($this->once())
            ->method('getAddress')
            ->willReturn('');

        $postman = new Postman($client, $logger);
        
        $this->expectException(TransportException::class);
        $postman->send($message);
    }
}