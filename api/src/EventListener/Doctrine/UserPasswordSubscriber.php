<?php


namespace App\EventListener\Doctrine;


use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordSubscriber implements EventSubscriber
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
        $entity = $args->getObject();

        if ($entity instanceof User){
            $plainPassword = $entity->getPlainPassword();

            if($plainPassword && 0 < strlen($plainPassword)) {
                $this->encodePassword($entity, $plainPassword, $args->getObjectManager());
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
