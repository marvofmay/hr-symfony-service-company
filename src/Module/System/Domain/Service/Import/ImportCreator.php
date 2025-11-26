<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Service\Import;

use App\Module\System\Domain\Entity\File;
use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Enum\Import\ImportKindEnum;
use App\Module\System\Domain\Enum\Import\ImportStatusEnum;
use App\Module\System\Domain\Interface\Import\ImportWriterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class ImportCreator
{
    public function __construct(private ImportWriterInterface $importWriterRepository)
    {
    }

    public function create(ImportKindEnum $kindEnum, ImportStatusEnum $statusEnum, File $file, UserInterface $user): void
    {
        $import = Import::create($kindEnum, $statusEnum, $file, $user);

        $this->importWriterRepository->saveImportInDB($import);
    }
}
