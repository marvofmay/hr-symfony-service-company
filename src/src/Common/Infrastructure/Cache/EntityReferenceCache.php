<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Cache;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Module\System\Application\Event\LogFileEvent;
use Psr\Log\LogLevel;
use Symfony\Component\Messenger\MessageBusInterface;

final class EntityReferenceCache
{
    private array $cache = [];

    public function __construct(private readonly MessageBusInterface $eventBus)
    {
    }

    public function get(string $className, string $uuid, callable $loader): object
    {
        $key = $className.':'.$uuid;
        if (!isset($this->cache[$key])) {
            $this->logInFile($className, $uuid);
            $this->cache[$key] = $loader($uuid);
        }

        return $this->cache[$key];
    }

    private function logInFile(string $className, string $uuid): void
    {
        $this->eventBus->dispatch(
            new LogFileEvent(
                $className.':'.$uuid.' not exists in cache - query to DB',
                LogLevel::INFO,
                MonologChanelEnum::IMPORT
            ));
    }
}
