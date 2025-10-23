<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Industry;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.industry.update.validator')]
#[AutoconfigureTag('app.industry.delete.validator')]
#[AutoconfigureTag('app.industry.query.get.validator')]
#[AutoconfigureTag('app.company.create.validator')]
#[AutoconfigureTag('app.company.update.validator')]
final readonly class IndustryExistsValidator
{
    public function __construct(private IndustryReaderInterface $industryReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return true;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        if (!property_exists($data, 'industryUUID')) {
            return;
        }

        $industryUUID = $data->industryUUID;
        $industryExists = $this->industryReaderRepository->isIndustryExistsWithUUID($industryUUID);
        if (!$industryExists) {
            throw new \Exception($this->translator->trans('industry.uuid.notExists', [':uuid' => $industryUUID], 'industries'), Response::HTTP_NOT_FOUND);
        }
    }
}
