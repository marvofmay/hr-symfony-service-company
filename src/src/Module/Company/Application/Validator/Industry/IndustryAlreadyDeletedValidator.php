<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Industry;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Application\Command\Industry\DeleteIndustryCommand;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.industry.restore.validator')]
final readonly class IndustryAlreadyDeletedValidator implements ValidatorInterface
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
        $industryDeleted = $this->industryReaderRepository->getDeletedIndustryByUUID($industryUUID);
        if (null === $industryDeleted) {
            throw new \Exception($this->translator->trans('industry.deleted.notExists', [':uuid' => $industryUUID], 'industries'), Response::HTTP_CONFLICT);
        }
    }
}
