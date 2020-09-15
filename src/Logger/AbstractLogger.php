<?php


namespace App\Logger;

use Doctrine\ORM\EntityNotFoundException;
use Monolog\Logger;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractLogger
{
    private static array $exceptionList = [
        NotFoundHttpException::class,
        EntityNotFoundException::class,
        \ApiPlatform\Core\Exception\InvalidArgumentException::class // TODO: fix it
    ];

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

    public function handleBatch(array $records): void
    {
    }

    public function close(): void
    {
    }
}