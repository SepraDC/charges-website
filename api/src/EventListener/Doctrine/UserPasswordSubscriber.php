<?php


namespace App\EventListener\Doctrine;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordSubscriber implements \Doctrine\Common\EventSubscriber
{
    /**
     * UserPasswordSubscriber constructor.
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }


    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args){
        $this->proceedEncodePassword($args);
    }

    public function preUpdate(LifecycleEventArgs $args){
        $this->proceedEncodePassword($args);
    }

    private function proceedEncodePassword(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User){
            $plainPassword = $entity->getPlainPassword();

            if($plainPassword && 0 < strlen($plainPassword)) {
                $this->encodePassword($entity, $plainPassword, $args->getEntityManager());
            }
        }
    }

    private function encodePassword(User $user, string $plainPassword, EntityManagerInterface $getEntityManager)
    {
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $plainPassword)
        );

        $user->eraseCredentials();
    }
}
