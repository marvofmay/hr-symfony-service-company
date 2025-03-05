<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'industry')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
#[OA\Schema(
    schema: 'IndustryListResponse',
    title: 'Industry List Response',
    description: 'Lista branż'
)]
class Industry
{
    use TimestampableTrait;
    use AttributesEntityTrait;

    public const COLUMN_UUID = 'uuid';

    #[OA\Property(description: 'Nazwa branży', type: 'string')]
    public const COLUMN_NAME = 'name';

    #[OA\Property(description: 'Opis branży', type: 'string')]
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
    #[Groups('industry_info')]
    private UuidInterface $uuid;

    #[ORM\Column(type: Types::STRING, length: 100, unique: true)]
    #[Assert\NotBlank()]
    #[Groups('industry_info')]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups('industry_info')]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups('industry_info')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('industry_info')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('industry_info')]
    private ?\DateTimeInterface $deletedAt = null;

    #[ORM\OneToMany(targetEntity: Company::class, mappedBy: 'industry')]
    private Collection $companies;

    public function __construct()
    {
        $this->companies = new ArrayCollection();
    }

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

    public function getCompanies(): Collection
    {
        return $this->companies;
    }
}
