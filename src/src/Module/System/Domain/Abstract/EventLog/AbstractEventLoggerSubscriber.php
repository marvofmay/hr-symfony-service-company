<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Abstract\EventLog;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Module\System\Domain\Entity\EventLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

abstract class AbstractEventLoggerSubscriber
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected SerializerInterface $serializer,
        private ServiceProviderInterface $loggers,
        protected Security $security,
    ) {
    }

    protected function log(string $eventClass, string $entityClass, mixed $data): void
    {
        $user = $this->security->getUser();
        $employee = method_exists($user, 'getEmployee') ? $user->getEmployee() : null;

        $jsonData = $this->serializer->serialize($data, 'json', [
            'circular_reference_handler' => fn ($object) => method_exists($object, 'getUUID') ? $object->getUUID() : spl_object_id($object),
        ]);

        $logger = $this->loggers->has(MonologChanelEnum::EVENT_LOG->value)
            ? $this->loggers->get(MonologChanelEnum::EVENT_LOG->value)
            : $this->loggers->get(MonologChanelEnum::MAIN->value);

        $logger->info('----------------- EVENT LOG -----------------');
        $logger->info("event: $eventClass");
        $logger->info("entity: $entityClass");
        $logger->info("data: $jsonData");
        $logger->info(
            $employee
                ? "employeeUUID: {$employee->getUUID()}"
                : "userUUID: {$user->getUUID()}"
        );
        $logger->info('---------------------------------------------');

        $this->em->persist(new EventLog($eventClass, $entityClass, $jsonData, $employee));
        $this->em->flush();
    }
}
