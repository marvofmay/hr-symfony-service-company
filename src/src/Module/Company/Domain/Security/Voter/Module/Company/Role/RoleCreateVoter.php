<?php

namespace App\Module\Company\Domain\Security\Voter\Module\Company\Role;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class RoleCreateVoter extends Voter
{
    public const CREATE = 'create';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::CREATE;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // ToDo:: Pobierz "employee" dla "user" lub dodaj relacje i pobierz getRole z "user"

        // ToDo:: Sprawdź, czy "employee" ma odpowiedni "access" do "role" z module "company"

        // ToDo:: Sprawdź, czy "employee" ma odpowiednie "permission" do tworzenia "role"

        return false;
    }
}
