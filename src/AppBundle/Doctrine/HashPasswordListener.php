<?php
/**
 * Created by PhpStorm.
 * User: Marko Kunic
 * Date: 9/13/17
 * Time: 11:12
 */

namespace AppBundle\Doctrine;

use AppBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HashPasswordListener implements EventSubscriber
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate'];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $user = $args->getEntity();
        
        if (!$user instanceof User) {
            return;
        }
        
        $this->encodePassword($user);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $user = $args->getEntity();

        if (!$user instanceof User) {
            return;
        }
        
        $this->encodePassword($user);

        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($user));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $user);
    }

    private function encodePassword(User $user)
    {
        if (!$user->getPlainPassword()) {
            return;
        }

        $encoded = $this->passwordEncoder->encodePassword(
            $user,
            $user->getPlainPassword()
        );

        $user->setPassword($encoded);
    }
}
