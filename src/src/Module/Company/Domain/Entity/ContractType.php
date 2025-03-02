<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Trait\AttributesEntityTrait;
use App\Common\Trait\TimestampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'contract_type')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class ContractType
{
    use TimestampableTrait;
    use AttributesEntityTrait;

    public const COLUMN_UUID = 'uuid';
    public const COLUMN_NAME = 'name';
    public const COLUMN_DESCRIPTION = 'description';
    public const COLUMN_ACTIVE = 'active';

    public const COLUMN_CREATED_AT = 'createdAt';
    public const COLUMN_UPDATED_AT = 'updatedAt';
    public const COLUMN_DELETED_AT = 'deletedAt';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups('contract_type_info')]
    private UuidInterface $uuid;

    #[ORM\Column(type: Types::STRING, length: 200)]
    #[Assert\NotBlank]
    #[Groups('contract_type_info')]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups('contract_type_info')]
    private ?string $description = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Assert\NotBlank]
    #[Groups('contract_type_info')]
    private bool $active;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups('contract_type_info')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('contract_type_info')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('contract_type_info')]
    private ?\DateTimeInterface $deletedAt = null;

    public function getUuid(): UuidInterface
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
