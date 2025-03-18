<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimestampableTrait;

#[ORM\Entity]
#[ORM\Table(name: 'permission')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: false)]
#[OA\Schema(
    schema: 'PermissionListResponse',
    title: 'Permission List Response',
    description: 'Lista uprawnień'
)]
class Permission
{
    use TimestampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const string COLUMN_UUID = 'uuid';
    public const string COLUMN_NAME = 'name';
    public const string COLUMN_DESCRIPTION = 'description';
    public const string COLUMN_ACTIVE = 'active';
    public const string COLUMN_CREATED_AT = 'createdAt';
    public const string COLUMN_UPDATED_AT = 'updatedAt';
    public const string COLUMN_DELETED_AT = 'deletedAt';
    public const string ALIAS = 'access';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $uuid;

    #[ORM\Column(type: Types::STRING, length: 100, unique: true)]
    #[Assert\NotBlank()]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Assert\NotBlank]
    private bool $active;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    public function getUUID(): UuidInterface
    {
        return $this->{self::COLUMN_UUID};
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

    public function getActive(): bool
    {
        return $this->{self::COLUMN_ACTIVE};
    }

    public function setActive(bool $active): void
    {
        $this->{self::COLUMN_ACTIVE} = $active;
    }
}