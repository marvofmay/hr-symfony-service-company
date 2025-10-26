<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimeStampableTrait;
use App\Module\Company\Domain\Enum\PositionDepartment\PositionDepartmentEntityRelationFieldEnum;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
#[ORM\Table(name: 'position_department')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: false)]
class PositionDepartment
{
    use TimeStampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Position::class, inversedBy: 'positionDepartments')]
    #[ORM\JoinColumn(name: 'position_uuid', referencedColumnName: 'uuid', onDelete: 'CASCADE')]
    public ?Position $position {
        get => $this->{PositionDepartmentEntityRelationFieldEnum::POSITION->value};
        set => $this->{PositionDepartmentEntityRelationFieldEnum::POSITION->value} = $value;
    }

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'positionDepartments')]
    #[ORM\JoinColumn(name: 'department_uuid', referencedColumnName: 'uuid', onDelete: 'CASCADE')]
    public ?Department $department {
        get => $this->{PositionDepartmentEntityRelationFieldEnum::DEPARTMENT->value};
        set => $this->{PositionDepartmentEntityRelationFieldEnum::DEPARTMENT->value} = $value;
    }

    public function __construct(Position $position, Department $department)
    {
        $this->position = $position;
        $this->department = $department;
    }
}
