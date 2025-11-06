<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use App\Module\System\Domain\Enum\Import\ImportLogKindEnum;
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
    use TimeStampableTrait;
    use AttributesEntityTrait;

    public const COLUMN_UUID = 'uuid';
    public const COLUMN_KIND = 'kind';
    public const COLUMN_DATA = 'data';
    public const COLUMN_CREATED_AT = 'createdAt';
    public const COLUMN_UPDATED_AT = 'updatedAt';
    public const COLUMN_DELETED_AT = 'deletedAt';

    public const RELATION_IMPORT = 'import';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $uuid;

    #[ORM\ManyToOne(targetEntity: Import::class, inversedBy: 'importLogs')]
    #[ORM\JoinColumn(name: 'import_uuid', referencedColumnName: 'uuid', onDelete: 'CASCADE')]
    private Import $import;

    #[ORM\Column(type: 'string', enumType: ImportLogKindEnum::class)]
    private ImportLogKindEnum $kind;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $data = null;

    public function getImport(): Import
    {
        return $this->import;
    }

    public function setImport(Import $import): void
    {
        $this->import = $import;
    }

    public function getKind(): ImportLogKindEnum
    {
        return $this->kind;
    }

    public function setKind(ImportLogKindEnum $kind): void
    {
        $this->kind = $kind;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): void
    {
        $this->data = $data;
    }

    public function toArray(): array
    {
        return [
            self::COLUMN_UUID => $this->uuid->toString(),
            self::COLUMN_KIND => $this->kind,
            self::COLUMN_DATA => $this->data,
        ];
    }
}
