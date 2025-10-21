<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.company.restore.validator')]
final readonly class CompanyAlreadyDeletedValidator implements ValidatorInterface
{
    public function __construct(private CompanyReaderInterface $companyReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return true;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        if (!property_exists($data, 'companyUUID')) {
            return;
        }

        $uuid = $data->companyUUID;
        $companyDeleted = $this->companyReaderRepository->getDeletedCompanyByUUID($uuid);
        if (null === $companyDeleted) {
            throw new \Exception($this->translator->trans('company.deleted.notExists', [':uuid' => $uuid], 'companies'), Response::HTTP_CONFLICT);
        }
    }
}
