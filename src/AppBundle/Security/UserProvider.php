<?php
/**
 * Created by PhpStorm.
 * User: Marko Kunic
 * Date: 9/13/17
 * Time: 11:58
 */

namespace AppBundle\Security;

use AppBundle\Entity\User;
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

    public function loadUserByUsername($email)
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

    private function findUserBy(array $options)
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy($options);

        return $user;
    }

    public function refreshUser(UserInterface $user)
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

    public function supportsClass($class)
    {
        return $class === User::class;
    }
}
