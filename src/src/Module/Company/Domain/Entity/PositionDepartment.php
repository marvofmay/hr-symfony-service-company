<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Common\Domain\Trait\AttributesEntityTrait;
use App\Common\Domain\Trait\RelationsEntityTrait;
use App\Common\Domain\Trait\TimestampableTrait;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
#[ORM\Table(name: 'position_department')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: false)]
class PositionDepartment
{
    use TimestampableTrait;
    use AttributesEntityTrait;
    use RelationsEntityTrait;

    public const RELATION_POSITION = 'position';
    public const RELATION_DEPARTMENT = 'department';

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Position::class, inversedBy: 'positionDepartments')]
    #[ORM\JoinColumn(name: 'position_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    private Position $position;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'positionDepartments')]
    #[ORM\JoinColumn(name: 'department_uuid', referencedColumnName: 'uuid', nullable: false, onDelete: 'CASCADE')]
    private Department $department;

    public function __construct(Position $position, Department $department,)
    {
        $this->position = $position;
        $this->department = $department;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function getDepartment(): Department
    {
        return $this->department;
    }
}
