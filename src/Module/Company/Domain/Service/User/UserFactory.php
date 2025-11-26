<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\User;

use App\Module\Company\Domain\Entity\User;
use App\Module\Company\Domain\Interface\User\UserFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UserFactory implements UserFactoryInterface
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function create(string $email, string $plainPassword): User
    {
        $user = User::create($email);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        return $user;
    }
}
