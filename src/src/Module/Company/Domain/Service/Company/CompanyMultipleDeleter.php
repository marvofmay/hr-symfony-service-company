<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Module\Company\Domain\Interface\Company\CompanyWriterInterface;
use Doctrine\Common\Collections\Collection;

final readonly class CompanyMultipleDeleter
{
    public function __construct(private CompanyWriterInterface $roleWriterRepository,)
    {
    }

    public function multipleDelete(Collection $roles): void
    {
        $this->roleWriterRepository->deleteMultipleCompaniesInDB($roles);
    }
}
