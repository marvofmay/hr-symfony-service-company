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

#[AutoconfigureTag('app.department.create.validator')]
final readonly class DepartmentNameAlreadyExistsValidator implements ValidatorInterface
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
        if (!property_exists($data, 'name')) {
            return;
        }

        $departmentUUID = $data->departmentUUID ?? null;
        $name = $data->name;
        if ($this->departmentReaderRepository->isDepartmentExistsWithName($name, $departmentUUID)) {
            throw new \Exception($this->translator->trans('department.name.alreadyExists', [':name' => $name], 'departments'), Response::HTTP_CONFLICT);
        }
    }
}
