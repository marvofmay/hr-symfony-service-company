<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use App\Module\Company\Domain\Enum\Industry\IndustryEntityFieldEnum;
use App\Module\Company\Domain\Enum\Industry\IndustryEntityRelationFieldEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'industry')]
#[ORM\Index(name: 'name', columns: ['name'])]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Industry
{
    use TimeStampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const string ALIAS = 'industry';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $uuid;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description;

    #[ORM\OneToMany(targetEntity: Company::class, mappedBy: 'industry')]
    private Collection $companies;

    private function __construct()
    {
        $this->companies = new ArrayCollection();
    }


    public static function create(string $name, ?string $description = null): self
    {
        $self = new self();
        $self->uuid = Uuid::uuid4();
        $self->name = $name;
        $self->description = $description;

        return $self;
    }

    public function getUUID(): UuidInterface
    {
        return $this->{IndustryEntityFieldEnum::UUID->value};
    }

    public function getName(): string
    {
        return $this->{IndustryEntityFieldEnum::NAME->value};
    }

    public function getDescription(): ?string
    {
        return $this->{IndustryEntityFieldEnum::DESCRIPTION->value};
    }

    public function getCompanies(): Collection
    {
        return $this->{IndustryEntityRelationFieldEnum::COMPANIES->value};
    }


    public function rename(string $newName): void
    {
        if ($newName === $this->name) {
            return;
        }

        $this->name = $newName;
    }

    public function updateDescription(?string $description): void
    {
        $this->description = $description;
    }
}
