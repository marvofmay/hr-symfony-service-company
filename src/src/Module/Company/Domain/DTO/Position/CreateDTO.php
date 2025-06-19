<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Position;

use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;
use App\Module\Company\Structure\Validator\Constraints\Department\ExistingDepartmentUUID;
use App\Module\Company\Structure\Validator\Constraints\Position\UniquePositionName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateDTO
{
    #[NotBlank(message: [
        'text' => 'position.name.required',
        'domain' => 'positions',
    ])]
    #[MinMaxLength(min: 3, max: 200, message: [
        'tooShort' => 'position.name.minimumLength',
        'tooLong' => 'position.name.maximumLength',
        'domain' => 'positions',
    ])]
    //#[UniquePositionName]
    public string $name = '' {
        get {
            return $this->name;
        }
    }

    public ?string $description = null {
        get {
            return $this->description;
        }
    }

    #[Assert\Type(
        type: 'bool',
    )]
    public ?bool $active = true {
        get {
            return $this->active;
        }
    }

    //#[Assert\NotBlank(message: 'position.add.departmentUUIDRequired')]
    //#[Assert\All([
    //    new Assert\Uuid(message: 'validate.invalidUUID'),
    //    new ExistingDepartmentUUID(
    //        message: ['uuidNotExists' => 'department.uuid.notExists', 'domain' => 'departments'],
    //    ),
    //])]
    public ?array $departmentsUUID = [];

    public function getDepartmentsUUID(): ?array
    {
        return $this->departmentsUUID;
    }
}
