<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Position;

use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class PositionValidator
{
    public function __construct(private PositionReaderInterface $positionReaderRepository, private TranslatorInterface $translator) {}

    public function isPositionNameAlreadyExists(string $name, ?string $uuid = null): void
    {
        if ($this->positionReaderRepository->isPositionExists($name, $uuid)) {
            throw new \Exception($this->translator->trans('position.name.alreadyExists', [':name' => $name], 'positions'), Response::HTTP_CONFLICT);
        }
    }
}