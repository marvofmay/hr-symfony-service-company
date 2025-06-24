<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Abstract\EventLog;

use App\Module\System\Domain\Entity\EventLog;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractEventLoggerSubscriber
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected SerializerInterface $serializer,
        protected LoggerInterface $logger,
        protected Security $security,
    ) {}

    protected function log(string $eventClass, string $entityClass, mixed $data): void
    {
        $user = $this->security->getUser();
        $employee = method_exists($user, 'getEmployee') ? $user->getEmployee() : null;

        $jsonData = $this->serializer->serialize($data, 'json', [
            'circular_reference_handler' => fn($object) => method_exists($object, 'getUUID') ? $object->getUUID() : spl_object_id($object),
        ]);

        $this->logger->info('----------------- EVENT LOG -----------------');
        $this->logger->info("event: $eventClass");
        $this->logger->info("entity: $entityClass");
        $this->logger->info("data: $jsonData");
        $this->logger->info(
            $employee
                ? "employeeUUID: {$employee->getUUID()}"
                : "userUUID: {$user->getUUID()}"
        );
        $this->logger->info('---------------------------------------------');

        $this->em->persist(new EventLog($eventClass, $entityClass, $jsonData, $employee));
        $this->em->flush();
    }
}