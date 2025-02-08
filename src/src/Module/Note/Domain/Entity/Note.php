<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use App\Module\Note\Domain\Enum\NotePriorityEnum;
use DateTimeInterface;
use DateTime;

#[ORM\Entity]
#[ORM\Table(name: "note")]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: "deletedAt", timeAware: false, hardDelete: true)]
class Note
{
    public const COLUMN_UUID = 'uuid';
    public const COLUMN_TITLE= 'title';
    public const COLUMN_CONTENT = 'content';
    public const COLUMN_PRIORITY = 'priority';
    public const COLUMN_CREATED_AT = 'createdAt';
    public const COLUMN_UPDATED_AT = 'updatedAt';
    public const COLUMN_DELETED_AT = 'deletedAt';

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups("note_info")]
    private UuidInterface $uuid;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank]
    #[Groups("note_info")]
    private string $title;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups("note_info")]
    private ?string $content = null;

    #[ORM\Column(type: Types::STRING, length: 20, enumType: NotePriorityEnum::class)]
    private NotePriorityEnum $priority;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    #[Groups("role_info")]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups("role_info")]
    private ?DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups("role_info")]
    private ?DateTimeInterface $deletedAt = null;

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
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

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->{self::COLUMN_CREATED_AT};
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->{self::COLUMN_UPDATED_AT};
    }

    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->{self::COLUMN_DELETED_AT};
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

    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function setDeletedAt(?DateTimeInterface $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->{self::COLUMN_CREATED_AT} = new DateTime();
    }

    public static function getAttributes(): array
    {
        return array_keys(get_class_vars(self::class));
    }
}
