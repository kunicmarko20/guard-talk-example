<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserProvider implements UserProviderInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function loadUserByUsername($email): User
    {
        $user = $this->findUserBy(['email' => $email]);

        if (!$user) {
            throw new UsernameNotFoundException(
                sprintf(
                    'User with "%s" does not exist.',
                    $email
                )
            );
        }

        return $user;
    }

    private function findUserBy(array $options): ?User
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy($options);

        return $user;
    }

    public function refreshUser(UserInterface $user): User
    {
        /** @var User $user */
        if (null === $reloadedUser = $this->findUserBy(['id' => $user->getId()])) {
            throw new UsernameNotFoundException(
                sprintf(
                    'User with ID "%s" could not be reloaded.',
                    $user->getId()
                )
            );
        }

        return $reloadedUser;
    }

    public function supportsClass($class): bool
    {
        return $class === User::class;
    }
}
