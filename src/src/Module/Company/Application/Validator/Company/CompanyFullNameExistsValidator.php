<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.company.create.validator')]
#[AutoconfigureTag('app.company.update.validator')]
final readonly class CompanyFullNameExistsValidator implements ValidatorInterface
{
    public function __construct(private CompanyReaderInterface $companyReaderRepository, private TranslatorInterface $translator,)
    {
    }

    public function supports(CommandInterface $command): bool
    {
        return true;
    }

    public function validate(CommandInterface $command, ?string $uuid = null): void
    {
        $fullName = $command->fullName;
        if ($this->companyReaderRepository->isCompanyExistsWithFullName($fullName, $uuid)) {
            throw new \Exception($this->translator->trans('company.fullName.alreadyExists', [':name' => $fullName], 'companies'), Response::HTTP_CONFLICT);
        }
    }
}