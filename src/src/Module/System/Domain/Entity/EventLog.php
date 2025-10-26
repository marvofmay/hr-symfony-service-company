<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Entity;

use App\Common\Domain\Trait\TimeStampableTrait;
use App\Module\Company\Domain\Entity\Employee;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'event_log')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: false)]
class EventLog
{
    use TimeStampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    public UuidInterface $uuid;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    public string $event;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    public string $entity;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $data;

    #[ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'eventLogs')]
    #[ORM\JoinColumn(name: 'employee_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    public ?Employee $employee;

    public function __construct(string $event, string $entity, ?string $data = null, ?Employee $employee = null)
    {
        $this->event = $event;
        $this->entity = $entity;
        $this->data = $data;
        $this->employee = $employee;
    }
}
