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

    public function isCompanyWithFullNameAlreadyExists(string $fullName, ?string $uuid = null): void
    {
        if ($this->companyReaderRepository->isCompanyExistsWithFullName($fullName, $uuid)) {
            throw new \Exception($this->translator->trans('company.fullName.alreadyExists', [':name' => $fullName], 'companies'), Response::HTTP_CONFLICT);
        }
    }

    public function isCompanyWithInternalCodeAlreadyExists(string $companyInternalCode, ?string $uuid = null): void
    {
        if ($this->companyReaderRepository->isCompanyExistsWithInternalCode($companyInternalCode, $uuid)) {
            throw new \Exception($this->translator->trans('company.companyInternalCode.alreadyExists', [':name' => $companyInternalCode], 'companies'), Response::HTTP_CONFLICT);
        }
    }

    public function isCompanyExists(string $uuid): void
    {
        $this->companyReaderRepository->getCompanyByUUID($uuid);
    }

    public function isCompaniesExists(array $uuids): void
    {
        $errors = [];
        foreach ($uuids as $uuid) {
            if (!$this->companyReaderRepository->isCompanyExistsWithUUID($uuid)) {
                $errors[] = $this->translator->trans('company.uuid.notExists', [':uuid' => $uuid], 'companies');
            }
        }

        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors), Response::HTTP_NOT_FOUND);
        }
    }
}
