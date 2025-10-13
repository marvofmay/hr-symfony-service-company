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

    public function get(string $className, string $identifier, callable $loader): ?object
    {
        $key = $className.':'.$identifier;
        if (!isset($this->cache[$key]) || empty($this->cache[$key])) {
            $this->logInFile($className, $identifier, 'getCache');
            $this->cache[$key] = $loader($identifier);
        }

        return $this->cache[$key];
    }

    public function set(object $entity): void
    {
        $className = get_class($entity);
        $uuid = $entity->getUuid()->toString();

        $key = $className.':'.$uuid;
        if (!isset($this->cache[$key])) {
            $this->logInFile($className, $uuid, 'setCache');
            $this->cache[$key] = $entity;
        }
    }

    private function logInFile(string $className, string $uuid, ?string $additionalInfo = null): void
    {
        $this->eventBus->dispatch(
            new LogFileEvent(
                $className.':'.$uuid.' not exists in cache - '.$additionalInfo,
                LogLevel::INFO,
                MonologChanelEnum::IMPORT
            ));
    }

    public function clear(): void
    {
        unset($this->cache);
    }
}
