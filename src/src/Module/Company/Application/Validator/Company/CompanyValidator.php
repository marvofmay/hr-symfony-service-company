<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company;

use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CompanyValidator
{
    public function __construct(private CompanyReaderInterface $companyReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function isCompanyAlreadyExists(string $nip, string $regon, ?string $uuid = null): void
    {
        if ($this->companyReaderRepository->isCompanyExists($nip, $regon, $uuid)) {
            throw new \Exception($this->translator->trans('company.alreadyExists', [':nip' => $nip, ':regon' => $regon], 'companies'), Response::HTTP_CONFLICT);
        }
    }
}
