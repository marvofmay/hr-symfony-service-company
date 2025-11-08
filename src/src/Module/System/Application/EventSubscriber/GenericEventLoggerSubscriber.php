<?php

namespace App\Module\System\Application\EventSubscriber;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Module\System\Domain\Interface\EventLog\EventLogCreatorInterface;
use App\Module\System\Domain\Interface\EventLog\LoggableEventInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

final readonly class GenericEventLoggerSubscriber
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected SerializerInterface $serializer,
        private ServiceProviderInterface $loggers,
        protected Security $security,
        private EventLogCreatorInterface $eventLogCreator,
        protected EventDispatcherInterface $dispatcher
    ) {
    }

    public function onLoggableEvent(LoggableEventInterface $event): void
    {
        $this->log($event::class, $event->getEntityClass(), $event->getData());
    }

    protected function log(string $eventClass, string $entityClass, mixed $data): void
    {
        $jsonData = $this->serializeData($data);
        $employee = $this->getEmployee();

        $this->logToFile($eventClass, $entityClass, $jsonData, $employee);
        $this->saveToDatabase($eventClass, $entityClass, $jsonData, $employee);
    }

    private function serializeData(mixed $data): string
    {
        return $this->serializer->serialize($data, 'json', [
            'circular_reference_handler' => fn ($object) => method_exists($object, 'getUUID') ? $object->getUUID() : spl_object_id($object),
        ]);
    }

    private function getEmployee(): ?object
    {
        $user = $this->security->getUser();
        if (null === $user) {
            return null;
        }

        return method_exists($user, 'getEmployee') ? $user->getEmployee() : null;
    }

    private function logToFile(string $eventClass, string $entityClass, string $jsonData, ?object $employee): void
    {
        $logger = $this->loggers->has(MonologChanelEnum::EVENT_LOG->value)
            ? $this->loggers->get(MonologChanelEnum::EVENT_LOG->value)
            : $this->loggers->get(MonologChanelEnum::MAIN->value);

        $logger->info('----------------- EVENT LOG -----------------');
        $logger->info("event: $eventClass");
        $logger->info("entity: $entityClass");
        $logger->info("data: $jsonData");
        $logger->info($employee ? "employeeUUID: " . $employee->getUUID() : 'userUUID: ' . $this->security->getUser()?->getUUID());
        $logger->info('---------------------------------------------');
    }

    private function saveToDatabase(string $eventClass, string $entityClass, string $jsonData, ?object $employee): void
    {
        $this->eventLogCreator->create(
            eventClass: $eventClass,
            entityClass: $entityClass,
            jsonData: $jsonData,
            employee: $employee
        );
    }
}
