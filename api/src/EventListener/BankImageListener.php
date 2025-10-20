<?php

namespace App\EventListener;

use App\Entity\Bank;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Symfony\Component\Routing\RequestContext;
use Vich\UploaderBundle\Storage\StorageInterface;

class BankImageListener
{
    public function __construct(
        private readonly StorageInterface $storage,
        private readonly RequestContext $requestContext
    ) {
    }

    public function postLoad(PostLoadEventArgs $event): void
    {
        $bank = $event->getObject();
        
        if ($bank instanceof Bank && $bank->getImageName() !== null) {
            $uri = $this->storage->resolveUri($bank, 'imageFile');
            $imageUrl = 'https://' . $this->requestContext->getHost() . $uri;
            $bank->setImageUrl($imageUrl);
        }
    }
}
