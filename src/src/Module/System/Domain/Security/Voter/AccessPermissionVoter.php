<?php

namespace App\Module\System\Domain\Security\Voter;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\System\Domain\Service\RoleAccessPermission\RoleAccessPermissionChecker;
use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Entity\Module;
use App\Module\System\Domain\Entity\Permission;
use App\Module\System\Domain\Interface\Access\AccessReaderInterface;
use App\Module\System\Domain\Interface\Module\ModuleReaderInterface;
use App\Module\System\Domain\Interface\Permission\PermissionReaderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AccessPermissionVoter extends Voter
{
    public function __construct(
        protected ModuleReaderInterface $moduleReaderRepository,
        protected AccessReaderInterface $accessReaderRepository,
        protected PermissionReaderInterface $permissionReaderRepository,
        protected RoleAccessPermissionChecker $roleAccessPermissionChecker,
        protected ?Module $module = null,
        protected ?Access $access = null,
        protected ?Permission $permission = null,
    ) {}

    abstract protected function getAttributeName(): string;

    protected function supports(mixed $attribute, mixed $subject): bool
    {
        if ($attribute->value !== $this->getAttributeName()) {
            return false;
        }

        $data = explode('.', $subject->value);
        $moduleName = $data[0];
        $accessName = $data[1];

        $this->module = $this->moduleReaderRepository->getModuleByName($moduleName);
        if (null === $this->module || !$this->module->getActive()) {
            return false;
        }

        $this->access = $this->accessReaderRepository->getAccessByNameAndModuleUUID($accessName, $this->module);
        if (null === $this->access || !$this->access->getActive()) {
            return false;
        }

        $this->permission = $this->permissionReaderRepository->getPermissionByName($this->getAttributeName());
        if (null === $this->permission || !$this->permission->getActive()) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(mixed $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($user->getEmail() === 'admin.hrapp@gmail.com') {
            return true;
        }

        $employee = $user->getEmployee();
        if (!$employee instanceof Employee) {
            return false;
        }

        $role = $employee->getRole();
        // ToDo:: change in class User, return getRoles() : super_admin to ROLE_SUPER_ADMIN
        if ($role->getName() === 'super_admin') {
            return true;
        }

        return $this->roleAccessPermissionChecker->check($this->permission, $this->access, $role);
    }
}