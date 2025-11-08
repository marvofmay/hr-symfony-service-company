<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Note\Domain\Enum\NoteEntityFieldEnum;
use App\Module\Note\Domain\Enum\NoteEntityRelationFieldEnum;
use App\Module\Note\Domain\Enum\NotePriorityEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
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

    public const string ALIAS = 'note';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    public UuidInterface $uuid;

    #[ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(name: 'employee_uuid', referencedColumnName: 'uuid', nullable: true, onDelete: 'CASCADE')]
    private ?Employee $employee;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank]
    private string $title;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content;

    #[ORM\Column(type: Types::STRING, length: 20, enumType: NotePriorityEnum::class)]
    private NotePriorityEnum $priority;

    public static function create(string $title, ?string $content = null, NotePriorityEnum $priority = NotePriorityEnum::LOW, ?Employee $employee = null): self
    {
        $self = new self();
        $self->{NoteEntityFieldEnum::UUID->value} = Uuid::uuid4();
        $self->{NoteEntityFieldEnum::TITLE->value} = $title;
        $self->{NoteEntityFieldEnum::CONTENT->value} = $content;
        $self->{NoteEntityFieldEnum::PRIORITY->value} = $priority;
        $self->{NoteEntityRelationFieldEnum::EMPLOYEE->value} = $employee;

        return $self;
    }

    public function getUUID(): UuidInterface
    {
        return $this->{NoteEntityFieldEnum::UUID->value};
    }

    public function getEmployee(): ?Employee
    {
        return $this->{NoteEntityRelationFieldEnum::EMPLOYEE->value};
    }

    public function getTitle(): string
    {
        return $this->{NoteEntityFieldEnum::TITLE->value};
    }

    public function getContent(): ?string
    {
        return $this->{NoteEntityFieldEnum::CONTENT->value};
    }

    public function getPriority(): NotePriorityEnum
    {
        return $this->{NoteEntityFieldEnum::PRIORITY->value};
    }

    public function changeTitle(string $title): void
    {
        if ($title === $this->{NoteEntityFieldEnum::TITLE->value}) {
            return;
        }

        $this->{NoteEntityFieldEnum::TITLE->value} = $title;
    }

    public function changeContent(string $content): void
    {
        if ($content === $this->{NoteEntityFieldEnum::CONTENT->value}) {
            return;
        }

        $this->{NoteEntityFieldEnum::CONTENT->value} = $content;
    }

    public function changePriority(NotePriorityEnum $priority): void
    {
        if ($priority === $this->{NoteEntityFieldEnum::PRIORITY->value}) {
            return;
        }

        $this->{NoteEntityFieldEnum::PRIORITY->value} = $priority;
    }
}
