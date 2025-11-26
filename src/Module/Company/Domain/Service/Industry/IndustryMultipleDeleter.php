<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry;

use App\Module\Company\Domain\Interface\Industry\IndustryMultipleDeleterInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryWriterInterface;
use Doctrine\Common\Collections\Collection;

readonly class IndustryMultipleDeleter implements IndustryMultipleDeleterInterface
{
    public function __construct(private IndustryWriterInterface $roleWriterRepository)
    {
    }

    public function multipleDelete(Collection $roles): void
    {
        $this->roleWriterRepository->deleteMultipleIndustriesInDB($roles);
    }
}
