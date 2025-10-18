<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company;

use App\Common\Domain\Interface\CommandInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.company.create.validator')]
class ParentCompanyExistsValidator
{
    public function __construct(private CompanyReaderInterface $companyReaderRepository, private TranslatorInterface $translator,)
    {
    }

    public function supports(CommandInterface $command): bool
    {
        return null !== $command->parentCompanyUUID;
    }

    public function validate(CommandInterface $command, ?string $uuid = null): void
    {
        $parentUUID = $command->parentCompanyUUID;
        $companyExists = $this->companyReaderRepository->isCompanyExistsWithUUID($parentUUID);
        if ($companyExists) {
            throw new \Exception($this->translator->trans('company.uuid.notExists', [':uuid' => $parentUUID], 'companies'), Response::HTTP_CONFLICT);
        }
    }
}