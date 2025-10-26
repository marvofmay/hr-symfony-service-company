<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Industry;

use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class IndustryValidator
{
    public function __construct(private IndustryReaderInterface $industryReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function isIndustryNameAlreadyExists(string $name, ?string $uuid = null): void
    {
        if ($this->industryReaderRepository->isIndustryNameAlreadyExists($name, $uuid)) {
            throw new \Exception($this->translator->trans('industry.name.alreadyExists', [':name' => $name], 'industries'), Response::HTTP_CONFLICT);
        }
    }

    public function isIndustryExists(string $uuid): void
    {
        $this->industryReaderRepository->getIndustryByUUID($uuid);
    }
}
