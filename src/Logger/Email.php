<?php


namespace App\Logger;


use Doctrine\ORM\EntityNotFoundException;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;


class Email implements HandlerInterface
{
    private static array $exceptionList = [
        NotFoundHttpException::class,
        EntityNotFoundException::class,
        \ApiPlatform\Core\Exception\InvalidArgumentException::class // TODO: fix it
    ];

    private string $toEmail;

    private MailerInterface $mailer;

    public function __construct(string $toEmail, MailerInterface $mailer)
    {
        $this->toEmail = $toEmail;
        $this->mailer = $mailer;
    }

    public function isHandling(array $record): bool
    {
        if (!in_array($record['level'], [Logger::CRITICAL, Logger::ERROR])) {
            return false;
        }

        $isException = !empty($record['context']['exception']) && is_object($record['context']['exception']);

        if (!$isException) {
            return true;
        }

        $exceptionClass = get_class($record['context']['exception']);

        return !in_array($exceptionClass, static::$exceptionList);
    }

    public function handle(array $record): bool
    {
        if (!$this->isHandling($record)) {
            return false;
        }

        $email = (new \Symfony\Component\Mime\Email())
            ->from('support@msu.uz')
            ->to($this->toEmail)
            ->subject('ERROR! msu.uz')
            ->text($record['message']);

        $this->mailer->send($email);

        return true;
    }

    public function handleBatch(array $records): void
    {
    }

    public function close(): void
    {
    }
}