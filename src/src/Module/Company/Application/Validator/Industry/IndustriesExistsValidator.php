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

#[AutoconfigureTag('app.industry.delete_multiple.validator')]
final readonly class IndustriesExistsValidator implements ValidatorInterface
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
        $uuids = $data->industriesUUIDs ?? [];

        if (empty($uuids)) {
            return;
        }

        $foundIndustries = $this->industryReaderRepository
            ->getIndustriesByUUID($uuids)
            ->map(fn ($industry) => $industry->getUUID())
            ->toArray();

        $missing = array_diff($uuids, $foundIndustries);

        if (!empty($missing)) {
            $translatedErrors = array_map(
                fn (string $uuid) => $this->translator->trans('industry.uuid.notExists', [':uuid' => $uuid], 'industries'),
                $missing
            );

            throw new \Exception(implode(', ', $translatedErrors), Response::HTTP_NOT_FOUND);
        }
    }
}
