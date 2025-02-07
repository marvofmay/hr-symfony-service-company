<?php

declare(strict_types = 1);

namespace App\Module\Notice\Domain\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use ReflectionClass;
use ReflectionProperty;

#[ORM\Entity]
#[ORM\Table(name: "notice")]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: "deletedAt", timeAware: false, hardDelete: true)]
class Notice
{
    public const COLUMN_UUID = 'uuid';
    public const COLUMN_TITLE= 'title';
    public const COLUMN_CONTENT = 'content';
    public const COLUMN_CREATED_AT = 'createdAt';
    public const COLUMN_UPDATED_AT = 'updatedAt';
    public const COLUMN_DELETED_AT = 'deletedAt';

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups("notice_info")]
    private UuidInterface $uuid;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank()]
    #[Groups("notice_info")]
    private string $title;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\NotBlank()]
    private string $content;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    #[Groups("notice_info")]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups("notice_info")]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups("notice_info")]
    private ?\DateTimeInterface $deletedAt = null;

    public function getUuid(): UuidInterface
    {
        return $this->{self::COLUMN_UUID};
    }

    public function getTitle(): string
    {
        return $this->{self::COLUMN_TITLE};
    }

    public function getContent(): string
    {
        return $this->{self::COLUMN_CONTENT};
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->{self::COLUMN_CREATED_AT};
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->{self::COLUMN_UPDATED_AT};
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->{self::COLUMN_DELETED_AT};
    }

    public function setUuid(UuidInterface $uuid): void
    {
        $this->{self::COLUMN_UUID} = $uuid;
    }

    public function setTitle(string $title): void
    {
        $this->{self::COLUMN_TITLE} = $title;
    }

    public function setContent(string $content): void
    {
        $this->{self::COLUMN_CONTENT} = $content;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->{self::COLUMN_CREATED_AT} = new \DateTime();
    }

    public static function getAttributes(): array
    {
        $reflectionClass = new ReflectionClass(static::class);
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PRIVATE);

        $attributes = [];
        foreach ($properties as $property) {
            $attributes[] = $property->getName();
        }

        return $attributes;
    }
}