<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Position;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class PositionAlreadyAssignedToDepartmentsValidator implements ValidatorInterface
{
    public function __construct(
        private PositionReaderInterface $positionReaderRepository,
        private DepartmentReaderInterface $departmentReaderRepository,
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return property_exists($data, 'departmentsUUIDs') && !empty($data->departmentsUUIDs);
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        if (!property_exists($data, 'positionUUID') || !property_exists($data, 'departmentsUUIDs')) {
            return;
        }

        $positionUUID = $data->positionUUID;
        $departmentsUUIDs = $data->departmentsUUIDs;

        $position = $this->positionReaderRepository->getPositionByUUID($positionUUID);
        $departments = $this->departmentReaderRepository->getDepartmentsByUUID($departmentsUUIDs);

        $existingDepartments = [];
        $currentDepartments = $position->getDepartments();

        foreach ($departments as $department) {
            if ($currentDepartments->contains($department)) {
                $existingDepartments[] = $department->getName();
            }
        }

        if (count($existingDepartments) > 0) {
            throw new \Exception($this->translator->trans('position.departments.alreadyExists', [':name' => $position->name, ':departments' => implode(',', $existingDepartments)], 'positions'), Response::HTTP_CONFLICT);
        }
    }
}
