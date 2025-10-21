<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Department;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.department.create.validator')]
#[AutoconfigureTag('app.department.update.validator')]
final readonly class ParentDepartmentExistsValidator
{
    public function __construct(private DepartmentReaderInterface $departmentReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return property_exists($data, 'parentDepartmentUUID') && null !== $data->parentDepartmentUUID;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        if (!property_exists($data, 'parentDepartmentUUID')) {
            return;
        }

        $parentUUID = $data->parentDepartmentUUID;
        $departmentExists = $this->departmentReaderRepository->isDepartmentExistsWithUUID($parentUUID);
        if ($departmentExists) {
            throw new \Exception($this->translator->trans('department.uuid.notExists', [':uuid' => $parentUUID], 'departments'), Response::HTTP_CONFLICT);
        }
    }
}
