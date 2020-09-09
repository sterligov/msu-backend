<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Email;
use App\Message\UserAppeal;
use App\MessageHandler\AppealHandler;
use App\Repository\AppealRepository;
use App\Services\MediaObjectContentUrlResolver;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;
use Twig\Error\RuntimeError;

/**
 * @group unit
 * Class AppealHandlerTest
 * @package App\Tests
 */
class AppealHandlerTest extends ApiTestCase
{
    public function testInvoke_ok()
    {
        $repository = $this->getMockBuilder(AppealRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->addMethods(['getMediaObject', 'getDepartment', 'getEmails'])
            ->getMock();

        $repository
            ->method('find')
            ->willReturn($this->returnSelf());

        $repository
            ->method('getMediaObject')
            ->willReturn(null);

        $repository->method('getDepartment')
            ->willReturn($this->returnSelf());

        $toEmails = ['test@gmail.com', 'test2@mail.ru'];
        $emails = [];
        foreach ($toEmails as $e) {
            $email = new Email();
            $email->setEmail($e);
            $emails[] = $email;
        }

        $repository->method('getEmails')
            ->willReturn($this->returnValue($emails));

        $text = '<h1>Hello, world!</h1>';
        $twig = $this->createMock(Environment::class);
        $twig->method('render')->willReturn($text);

        $urlResolver = $this->createMock(MediaObjectContentUrlResolver::class);

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->once())->method('send');

        $handler = new AppealHandler($repository, $urlResolver, $mailer, $twig);
        $appeal = $this->createMock(UserAppeal::class);
        $email = $handler($appeal);

        $actualEmails = [];
        foreach ($email->getTo() as $e) {
            $actualEmails[] = $e->getAddress();
        }

        $this->assertEquals('support@msu.uz', $email->getFrom()[0]->getAddress());
        $this->assertEquals($toEmails, $actualEmails);
        $this->assertEquals('Обращение в виртуальную приемную', $email->getSubject());
        $this->assertEquals($text, $email->getTextBody());
    }

    public function testInvoke_exception()
    {
        $repository = $this->getMockBuilder(AppealRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->setMethods(['getMediaObject', 'getDepartment', 'getEmails'])
            ->getMock();

        $repository
            ->method('find')
            ->willReturn($this->returnSelf());

        $repository
            ->method('getMediaObject')
            ->willReturn(null);

        $twig = $this->createMock(Environment::class);
        $twig->method('render')->willThrowException(new RuntimeError('Twig runtime error'));

        $urlResolver = $this->createMock(MediaObjectContentUrlResolver::class);
        $mailer = $this->createMock(MailerInterface::class);

        $handler = new AppealHandler($repository, $urlResolver, $mailer, $twig);
        $appeal = $this->createMock(UserAppeal::class);

        $this->expectException(RuntimeError::class);
        $handler($appeal);
    }
}