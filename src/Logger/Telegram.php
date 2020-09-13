<?php


namespace App\Logger;


use App\Services\Postman;
use Doctrine\ORM\EntityNotFoundException;
use Longman\TelegramBot\Request;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Longman\TelegramBot\Telegram as TelegramBot;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Telegram implements HandlerInterface
{
    private string $chatID;

    private TelegramBot $bot;

    private MailerInterface $mailer;

    public function __construct(string $chatID, TelegramBot $bot, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->bot = $bot;
        $this->chatID = $chatID;
    }

    public function isHandling(array $record): bool
    {
        if (!in_array($record['level'], [Logger::CRITICAL, Logger::ERROR])) {
            return false;
        }

        $isException = !empty($record['context']['exception']) && is_object($record['context']['exception']);

        $isNotFoundException = $isException
            && ($record['context']['exception'] instanceof NotFoundHttpException
                || $record['context']['exception'] instanceof EntityNotFoundException);

        return !$isNotFoundException;
    }

    public function handle(array $record): bool
    {
        if (!$this->isHandling($record)) {
            return false;
        }

//        $email = (new Email())
//            ->from('support@msu.uz')
//            ->to('denis0324@gmail.com')
//            ->subject('ERROR! msu.uz')
//            ->text($record['message']);
//
//        $this->mailer->send($email);
        $res = Request::sendMessage([
            'chat_id' => $this->chatID,
            'text' => $record['message']
        ]);
        dd($res);
        return true;
    }

    public function handleBatch(array $records): void
    {
    }

    public function close(): void
    {
    }
}