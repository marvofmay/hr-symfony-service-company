<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'role')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
#[OA\Schema(
    schema: 'RoleListResponse',
    title: 'Role List Response',
    description: 'Lista ról'
)]
class Role
{
    public const COLUMN_UUID = 'uuid';

    #[OA\Property(description: 'Nazwa roli', type: 'string')]
    public const COLUMN_NAME = 'name';

    #[OA\Property(description: 'Opis roli', type: 'string')]
    public const COLUMN_DESCRIPTION = 'description';

    #[OA\Property(description: 'Data utworzenia', type: 'string', format: 'date-time')]
    public const COLUMN_CREATED_AT = 'createdAt';

    #[OA\Property(description: 'Data aktualizacji', type: 'string', format: 'date-time', nullable: true)]
    public const COLUMN_UPDATED_AT = 'updatedAt';

    #[OA\Property(description: 'Data usunięcia', type: 'string', format: 'date-time', nullable: true)]
    public const COLUMN_DELETED_AT = 'deletedAt';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups('role_info')]
    private UuidInterface $uuid;

    #[ORM\Column(type: Types::STRING, length: 100, unique: true)]
    #[Assert\NotBlank()]
    #[Groups('role_info')]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups('role_info')]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups('role_info')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('role_info')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('role_info')]
    private ?\DateTimeInterface $deletedAt = null;

    public function getUuid(): UuidInterface
    {
        return $this->{self::COLUMN_UUID};
    }

    public function setUuid(UuidInterface $uuid): void
    {
        $this->{self::COLUMN_UUID} = $uuid;
    }

    public function getName(): string
    {
        return $this->{self::COLUMN_NAME};
    }

    public function setName(string $name): void
    {
        $this->{self::COLUMN_NAME} = $name;
    }

    public function getDescription(): ?string
    {
        return $this->{self::COLUMN_DESCRIPTION};
    }

    public function setDescription(?string $description): void
    {
        $this->{self::COLUMN_DESCRIPTION} = $description;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->{self::COLUMN_CREATED_AT} = new \DateTime();
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->{self::COLUMN_CREATED_AT};
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->{self::COLUMN_UPDATED_AT};
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->{self::COLUMN_UPDATED_AT} = $updatedAt;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->{self::COLUMN_DELETED_AT};
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): void
    {
        $this->{self::COLUMN_DELETED_AT} = $deletedAt;
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
}
