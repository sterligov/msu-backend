<?php


namespace App\Logger;


use Monolog\Handler\HandlerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Telegram\Bot\Api;

class Telegram extends AbstractLogger implements HandlerInterface
{
    private string $chatID;

    private Api $api;

    public function __construct(string $chatID, Api $api)
    {
        $this->api = $api;
        $this->chatID = $chatID;
    }

    public function handle(array $record): bool
    {
        if (!$this->isHandling($record)) {
            return false;
        }

        $this->api->sendMessage([
            'chat_id' => $this->chatID,
            'text' => $record['message']
        ]);

        return true;
    }

    public function handleBatch(array $records): void
    {
    }

    public function close(): void
    {
    }
}