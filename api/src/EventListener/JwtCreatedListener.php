<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_created')]
class JwtCreatedListener
{
    public function __construct(private RequestStack $requestStack) {}

    public function __invoke(JWTCreatedEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return;
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['remember_me']) && $data['remember_me'] === true) {
            $payload = $event->getData();
            $payload['exp'] = time() + (60 * 60 * 24 * 30); // 30 days
            $event->setData($payload);
        }
    }
}
