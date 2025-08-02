<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Department;

use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DepartmentValidator
{
    public function __construct(private DepartmentReaderInterface $departmentReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function isDepartmentExistsWithName(string $name, ?string $uuid = null): void
    {
        if ($this->departmentReaderRepository->isDepartmentExistsWithName($name, $uuid)) {
            throw new \Exception($this->translator->trans('department.name.alreadyExists', [':name' => $name], 'departments'), Response::HTTP_CONFLICT);
        }
    }

    public function isDepartmentExists(string $uuid): void
    {
        $this->departmentReaderRepository->getDepartmentByUUID($uuid);
    }

    public function isDepartmentsExists(array $uuids): void
    {
        $errors = [];
        foreach ($uuids as $uuid) {
            if (!$this->departmentReaderRepository->isDepartmentExistsWithUUID($uuid)) {
                $errors[] = $this->translator->trans('department.uuid.notExists', [':uuid' => $uuid], 'departments');
            }
        }

        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors), Response::HTTP_NOT_FOUND);
        }
    }
}
