<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Department;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.department.delete_multiple.validator')]
#[AutoconfigureTag('app.position.create.validator')]
#[AutoconfigureTag('app.position.update.validator')]
final readonly class DepartmentsExistsValidator implements ValidatorInterface
{
    public function __construct(private DepartmentReaderInterface $departmentReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return true;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        $uuids = $data->departmentsUUIDs ?? [];

        if (empty($uuids)) {
            return;
        }

        $foundDepartments = $this->departmentReaderRepository
            ->getDepartmentsByUUID($uuids)
            ->map(fn ($department) => $department->getUUID())
            ->toArray();

        $missing = array_diff($uuids, $foundDepartments);

        if (!empty($missing)) {
            $translatedErrors = array_map(
                fn (string $uuid) => $this->translator->trans('department.uuid.notExists', [':uuid' => $uuid], 'departments'),
                $missing
            );

            throw new \Exception(implode(', ', $translatedErrors), Response::HTTP_NOT_FOUND);
        }
    }
}
