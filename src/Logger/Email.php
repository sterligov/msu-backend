<?php


namespace App\Logger;


use App\Logger\AbstractLogger;
use Doctrine\ORM\EntityNotFoundException;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;



class Email extends AbstractLogger implements HandlerInterface
{
    private string $toEmail;

    private MailerInterface $mailer;

    public function __construct(string $toEmail, MailerInterface $mailer)
    {
        $this->toEmail = $toEmail;
        $this->mailer = $mailer;
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

        return false;
    }

    public function handleBatch(array $records): void
    {
    }

    public function close(): void
    {
    }
}