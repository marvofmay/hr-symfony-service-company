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
final readonly class CompanyExistsValidator implements ValidatorInterface
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
        $nip = $command->nip;
        $regon = $command->regon;
        $companyExists = $this->companyReaderRepository->isCompanyExists($nip, $regon, $uuid);
        if ($companyExists) {
            throw new \Exception($this->translator->trans('company.alreadyExists', [':nip' => $nip, ':regon' => $regon], 'companies'), Response::HTTP_CONFLICT);
        }
    }
}