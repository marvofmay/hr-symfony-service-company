<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'import_report')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class ImportReport
{
    use TimeStampableTrait;
    use AttributesEntityTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $uuid;

    #[ORM\OneToOne(targetEntity: Import::class, inversedBy: 'report')]
    #[ORM\JoinColumn(name: 'import_uuid', referencedColumnName: 'uuid', onDelete: 'CASCADE')]
    private Import $import;

    #[ORM\Column(type: Types::INTEGER)]
    private int $newRecords = 0;

    #[ORM\Column(type: Types::INTEGER)]
    private int $updatedRecords = 0;

    #[ORM\Column(type: Types::INTEGER)]
    private int $errors = 0;

    public function getImport(): Import
    {
        return $this->import;
    }

    public function setImport(Import $import): void
    {
        $this->import = $import;
    }

    public function getNewRecords(): int
    {
        return $this->newRecords;
    }

    public function setNewRecords(int $newRecords): void
    {
        $this->newRecords = $newRecords;
    }

    public function getUpdatedRecords(): int
    {
        return $this->updatedRecords;
    }

    public function setUpdatedRecords(int $updatedRecords): void
    {
        $this->updatedRecords = $updatedRecords;
    }

    public function getErrors(): int
    {
        return $this->errors;
    }

    public function setErrors(int $errors): void
    {
        $this->errors = $errors;
    }
}
