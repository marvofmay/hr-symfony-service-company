<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use App\Module\System\Domain\Enum\Permission\PermissionEntityFieldEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'permission')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: false)]
class Permission
{
    use TimeStampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const string ALIAS = 'permission';

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

    public function getUUID(): UuidInterface
    {
        return $this->{PermissionEntityFieldEnum::UUID->value};
    }

    public function getName(): string
    {
        return $this->{PermissionEntityFieldEnum::NAME->value};
    }

    public function setName(string $name): void
    {
        $this->{PermissionEntityFieldEnum::NAME->value} = $name;
    }

    public function getDescription(): ?string
    {
        return $this->{PermissionEntityFieldEnum::DESCRIPTION->value};
    }

    public function setDescription(?string $description): void
    {
        $this->{PermissionEntityFieldEnum::DESCRIPTION->value} = $description;
    }

    public function getActive(): bool
    {
        return $this->{PermissionEntityFieldEnum::ACTIVE->value};
    }

    public function setActive(bool $active): void
    {
        $this->{PermissionEntityFieldEnum::ACTIVE->value} = $active;
    }
}
