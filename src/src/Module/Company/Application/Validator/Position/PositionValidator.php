<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Position;

use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class PositionValidator
{
    public function __construct(private PositionReaderInterface $positionReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function isPositionNameAlreadyExists(string $name, ?string $uuid = null): void
    {
        if ($this->positionReaderRepository->isPositionExists($name, $uuid)) {
            throw new \Exception($this->translator->trans('position.name.alreadyExists', [':name' => $name], 'positions'), Response::HTTP_CONFLICT);
        }
    }

    public function isPositionAlreadyAssignedToDepartments(Position $position, Collection $departments): void
    {
        $existingDepartments = [];
        $currentDepartments = $position->getDepartments();

        foreach ($departments as $department) {
            if ($currentDepartments->contains($department)) {
                $existingDepartments[] = $department->getName();
            }
        }

        if (count($existingDepartments) > 0) {
            throw new \Exception($this->translator->trans('position.departments.alreadyExists', [':name' => $position->getName(), ':departments' => implode(',', $existingDepartments)], 'positions'), Response::HTTP_CONFLICT);
        }
    }
}
