<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class UserCommandUtil
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create($email, $password, $role = null): void
    {
        $user = new User();

        $user->setEmail($email)
            ->setPlainPassword($password)
            ->addRole($role);

        $this->saveUser($user);
    }

    private function saveUser(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function promote(string $email, string $role): void
    {
        $user = $this->findUserByEmail($email);

        $user->addRole($role);

        $this->saveUser($user);
    }

    private function findUserByEmail(string $email): User
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (!$user) {
            throw new InvalidArgumentException(sprintf('User identified by "%s" email does not exist.', $email));
        }

        return $user;
    }

    public function demote(string $email, string $role): void
    {
        $user = $this->findUserByEmail($email);

        $user->removeRole($role);

        $this->saveUser($user);
    }
}
