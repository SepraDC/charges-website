<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AccessDeniedListener
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private string $frontUrl
    ) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        
        // Check if it's an access denied exception and the request is for admin
        if ($exception instanceof AccessDeniedHttpException) {
            $request = $event->getRequest();
            $path = $request->getPathInfo();
            
            // If trying to access admin area without proper role
            if (str_starts_with($path, '/admin')) {
                // Redirect to frontend
                $frontendUrl = 'http://' . rtrim($this->frontUrl, '/');
                $event->setResponse(new RedirectResponse($frontendUrl));
            }
        }
    }
}
