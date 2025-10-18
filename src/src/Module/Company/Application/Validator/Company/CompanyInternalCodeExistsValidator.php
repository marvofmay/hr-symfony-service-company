<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CompanyInternalCodeExistsValidator implements ValidatorInterface
{
    public function __construct(private CompanyReaderInterface $companyReaderRepository, private TranslatorInterface $translator,)
    {
    }

    public function supports(CommandInterface $command): bool
    {
        return null !== $command->internalCode;
    }

    public function validate(CommandInterface $command, ?string $uuid = null): void
    {
        $companyInternalCode = $command->internalCode;
        if ($this->companyReaderRepository->isCompanyExistsWithInternalCode($companyInternalCode, $uuid)) {
            throw new \Exception($this->translator->trans('company.internalCode.alreadyExists', [':internalCode' => $companyInternalCode], 'companies'), Response::HTTP_CONFLICT);
        }
    }
}