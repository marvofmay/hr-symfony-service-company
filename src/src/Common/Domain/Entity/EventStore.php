<?php

declare(strict_types=1);

namespace App\Common\Domain\Entity;

use App\Module\Company\Domain\Entity\Employee;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'event_store')]
#[ORM\Index(name: 'idx_aggregate_uuid', columns: ['aggregate_uuid'])]
#[ORM\Index(name: 'idx_aggregate_type', columns: ['aggregate_type'])]
#[ORM\Index(name: 'idx_employee_uuid', columns: ['employee_uuid'])]
class EventStore
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $uuid;

    #[ORM\Column(name: 'aggregate_uuid', type: 'uuid')]
    private string $aggregateUUID;

    #[ORM\Column(name: 'aggregate_type', type: 'string', length: 255)]
    private string $aggregateType;

    #[ORM\Column(name: 'aggregate_class', type: 'string', length: 255)]
    private string $aggregateClass;

    #[ORM\Column(type: 'json')]
    private string $payload;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    #[ORM\JoinColumn(name: 'employee_uuid', referencedColumnName: 'uuid', nullable: true)]
    private ?Employee $employee = null;

    public function __construct(
        string $aggregateUUID,
        string $aggregateType,
        string $aggregateClass,
        string $payload,
        ?Employee $employee = null
    ) {
        $this->aggregateUUID = $aggregateUUID;
        $this->aggregateType = $aggregateType;
        $this->aggregateClass = $aggregateClass;
        $this->payload = $payload;
        $this->employee = $employee;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function getAggregateUUID(): string
    {
        return $this->aggregateUUID;
    }

    public function getAggregateType(): string
    {
        return $this->aggregateType;
    }

    public function getAggregateClass(): string
    {
        return $this->aggregateClass;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

}