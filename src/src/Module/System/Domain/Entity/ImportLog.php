<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\TimestampableTrait;
use App\Module\System\Domain\Enum\ImportLogKindEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'import_log')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class ImportLog
{
    use TimestampableTrait;
    use AttributesEntityTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $uuid;

    #[ORM\ManyToOne(targetEntity: Import::class, inversedBy: 'logs')]
    #[ORM\JoinColumn(name: 'import_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    private Import $import;

    #[ORM\Column(type: 'string', enumType: ImportLogKindEnum::class)]
    private ImportLogKindEnum $kind;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $data = null;
}