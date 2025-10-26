<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Note\Domain\Enum\NotePriorityEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'note')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Note
{
    use TimeStampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const string COLUMN_UUID = 'uuid';
    public const string COLUMN_EMPLOYEE_UUID = 'employee_uuid';
    public const string COLUMN_TITLE = 'title';
    public const string COLUMN_CONTENT = 'content';
    public const string COLUMN_PRIORITY = 'priority';
    public const string COLUMN_CREATED_AT = 'createdAt';
    public const string COLUMN_UPDATED_AT = 'updatedAt';
    public const string COLUMN_DELETED_AT = 'deletedAt';
    public const string RELATION_EMPLOYEE = 'employee';
    public const string ALIAS = 'note';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $uuid;

    #[ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(name: 'employee_uuid', referencedColumnName: 'uuid', onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private Employee $employee;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank]
    private string $title;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(type: Types::STRING, length: 20, enumType: NotePriorityEnum::class)]
    private NotePriorityEnum $priority;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getUUID(): UuidInterface
    {
        return $this->uuid;
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getPriority(): NotePriorityEnum
    {
        return $this->priority;
    }

    public function setEmployee(Employee $employee): void
    {
        $this->employee = $employee;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function setPriority(NotePriorityEnum $priority): void
    {
        $this->priority = $priority;
    }
}
