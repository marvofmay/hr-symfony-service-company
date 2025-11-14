<?php

declare(strict_types=1);

namespace App\Module\System\Application\Command\Import;

use App\Module\System\Domain\Entity\File;
use App\Module\System\Domain\Enum\Import\ImportKindEnum;
use App\Module\System\Domain\Enum\Import\ImportStatusEnum;
use Symfony\Component\Security\Core\User\UserInterface;

class CreateImportCommand
{
    public function __construct(
        public ImportKindEnum $kindEnum,
        public ImportStatusEnum $statusEnum,
        public File $file,
        public UserInterface $user
    )
    {
    }
}
