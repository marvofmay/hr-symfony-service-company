<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Entity;

use App\Common\Trait\AttributesEntityTrait;
use App\Common\Trait\RelationsEntityTrait;
use App\Common\Trait\TimestampableTrait;
use App\Module\Note\Domain\Enum\NotePriorityEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Module\Company\Domain\Entity\Employee;

#[ORM\Entity]
#[ORM\Table(name: 'note')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Note
{
    use TimestampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const COLUMN_UUID = 'uuid';
    public const COLUMN_EMPLOYEE_UUID = 'employee_uuid';
    public const COLUMN_TITLE = 'title';
    public const COLUMN_CONTENT = 'content';
    public const COLUMN_PRIORITY = 'priority';
    public const COLUMN_CREATED_AT = 'createdAt';
    public const COLUMN_UPDATED_AT = 'updatedAt';
    public const COLUMN_DELETED_AT = 'deletedAt';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups('note_info')]
    private UuidInterface $uuid;

    #[ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(name: 'employee_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    #[Groups('note_info')]
    private Employee $employee;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank]
    #[Groups('note_info')]
    private string $title;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups('note_info')]
    private ?string $content = null;

    #[ORM\Column(type: Types::STRING, length: 20, enumType: NotePriorityEnum::class)]
    private NotePriorityEnum $priority;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups('note_info')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('note_info')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('note_info')]
    private ?\DateTimeInterface $deletedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getUuid(): UuidInterface
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

    public static function getAttributes(): array
    {
        $reflectionClass = new \ReflectionClass(static::class);
        $properties = $reflectionClass->getProperties(\ReflectionProperty::IS_PRIVATE);

        $attributes = [];
        foreach ($properties as $property) {
            $attributes[] = $property->getName();
        }

        return $attributes;
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->getUuid()->toString(),
            'title' => $this->getTitle(),
            'content' => $this->getContent(),
            'priority' => $this->getPriority(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $this->getUpdatedAt()?->format('Y-m-d H:i:s'),
            'deletedAt' => $this->getDeletedAt()?->format('Y-m-d H:i:s'),
            'employee' => [
                'uuid' => $this->getEmployee()->getUuid()->toString(),
                'firstname' => $this->getEmployee()->getFirstname(),
                'lastname' => $this->getEmployee()->getLastname(),
            ]
        ];
    }
}
