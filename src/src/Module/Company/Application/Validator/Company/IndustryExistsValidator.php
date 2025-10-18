<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company;

use App\Common\Domain\Interface\CommandInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class IndustryExistsValidator
{
    public function __construct(private IndustryReaderInterface $industryReaderRepository, private TranslatorInterface $translator,)
    {
    }

    public function supports(CommandInterface $command): bool
    {
        return true;
    }

    public function validate(CommandInterface $command): void
    {
        $industryUUID = $command->industryUUID;
        $industryExists = $this->industryReaderRepository->isIndustryExists($industryUUID);
        if (!$industryExists) {
            throw new \Exception($this->translator->trans('industry.uuid.notExists', [':uuid' => $industryUUID], 'industries'), Response::HTTP_NOT_FOUND);
        }

    }
}