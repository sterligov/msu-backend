<?php


namespace App\Services;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\LogicException;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Postman implements MailerInterface
{
    private const URL = '';

    private const AUTH_TOKEN = '';

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Postman constructor.
     * @param HttpClientInterface $httpClient
     * @param LoggerInterface $logger
     */
    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    /**
     * @param RawMessage $message
     * @param Envelope|null $envelope
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function send(RawMessage $message, Envelope $envelope = null): void
    {
        if (!$message instanceof Email) {
            throw new LogicException('Cannot set a Transport on a RawMessage instance.');
        }

        $to = array_reduce($message->getTo(), function ($carry, $item) {
            return $carry . ',' . $item->getAddress();
        }, '');

        $response = $this->httpClient->request('POST', self::URL, [
            'body' => [
                'subject' => $message->getSubject(),
                'message' => $message->getTextBody(),
                'to' => substr($to, 1),
                'from' => $message->getFrom()[0]->getAddress(),
            ],
            'headers' => [
                'X-Authorization' => self::AUTH_TOKEN,
            ],
        ]);

        if ($response->getStatusCode() != Response::HTTP_OK) {
            $this->logger->alert(
                sprintf(
                    'Email API return status %s. Content: %s',
                    $response->getStatusCode(),
                    $message->toString()
                )
            );

            throw new TransportException(
                sprintf('Email API return status %s', $response->getStatusCode())
            );
        }
    }
}