<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Domain\Interface\MappableEntityInterface;
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
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'industry')]
#[ORM\Index(name: 'name', columns: ['name'])]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Industry implements MappableEntityInterface
{
    use TimeStampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const string ALIAS = 'industry';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $uuid;

    #[ORM\Column(type: Types::STRING, length: 100, unique: true)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Company::class, mappedBy: 'industry')]
    private Collection $companies;

    public function __construct()
    {
        $this->companies = new ArrayCollection();
    }

    public function getUUID(): UuidInterface
    {
        return $this->{IndustryEntityFieldEnum::UUID->value};
    }

    public function setUuid(UuidInterface $uuid): void
    {
        $this->{IndustryEntityFieldEnum::UUID->value} = $uuid;
    }

    public function getName(): string
    {
        return $this->{IndustryEntityFieldEnum::NAME->value};
    }

    public function setName(string $name): void
    {
        $this->{IndustryEntityFieldEnum::NAME->value} = $name;
    }

    public function getDescription(): ?string
    {
        return $this->{IndustryEntityFieldEnum::DESCRIPTION->value};
    }

    public function setDescription(?string $description): void
    {
        $this->{IndustryEntityFieldEnum::DESCRIPTION->value} = $description;
    }

    public function getCompanies(): Collection
    {
        return $this->{IndustryEntityRelationFieldEnum::COMPANIES->value};
    }
}
