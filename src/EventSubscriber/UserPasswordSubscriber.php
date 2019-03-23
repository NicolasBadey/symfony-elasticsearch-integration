<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserPasswordSubscriber implements EventSubscriber
{
    /**
     * @var UserPasswordEncoderInterface
     */
    protected $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->changePassword($args);
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->changePassword($args);
    }

    public function changePassword(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof User && null !== $entity->getPlainPassword()) {
            $entity->setPassword(
                $this->passwordEncoder->encodePassword(
                    $entity,
                    $entity->getPlainPassword()
                )
            );
        }
    }
}
