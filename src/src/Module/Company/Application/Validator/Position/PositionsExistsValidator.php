<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Position;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.position.delete_multiple.validator')]
final readonly class PositionsExistsValidator implements ValidatorInterface
{
    public function __construct(private PositionReaderInterface $positionReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return true;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        $uuids = $data->positionsUUIDs ?? [];

        if (empty($uuids)) {
            return;
        }

        $foundPositions = $this->positionReaderRepository
            ->getPositionsByUUID($uuids)
            ->map(fn ($position) => $position->uuid)
            ->toArray();

        $missing = array_diff($uuids, $foundPositions);

        if (!empty($missing)) {
            $translatedErrors = array_map(
                fn (string $uuid) => $this->translator->trans('position.uuid.notExists', [':uuid' => $uuid], 'positions'),
                $missing
            );

            throw new \Exception(implode(', ', $translatedErrors), Response::HTTP_NOT_FOUND);
        }
    }
}
