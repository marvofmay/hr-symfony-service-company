<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Industry;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.industry.create.validator')]
#[AutoconfigureTag('app.industry.update.validator')]
final readonly class IndustryNameAlreadyExistsValidator implements ValidatorInterface
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
        if (!property_exists($data, 'name')) {
            return;
        }

        $name = $data->name;
        $industryUUID = $data->industryUUID ?? null;
        $industry = $this->industryReaderRepository->isIndustryNameAlreadyExists($name, $industryUUID);
        if ($industry) {
            throw new \Exception($this->translator->trans('industry.name.alreadyExists', [':name' => $name], 'industries'), Response::HTTP_CONFLICT);
        }
    }
}
