<?php


namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AccessDeniedListener implements EventSubscriberInterface
{
    private FlashBagInterface $flash;
    private AuthorizationCheckerInterface $authorization;

    public function __construct(FlashBagInterface $flash, AuthorizationCheckerInterface $authorization) {
        $this->flash = $flash;
        $this->authorization = $authorization;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 2],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof AccessDeniedException) {
            return;
        }

        $this->flash->add('error', 'Vous n\'avez pas pas le droit d\'accéder à cette page.');

        if ($exception->getCode() === 403 && !$this->authorization->isGranted('IS_AUTHENTICATED_FULLY')) {
            $event->setResponse(new RedirectResponse('/login'));

            return;
        }

        if ($exception->getCode() === 403 && $this->authorization->isGranted('IS_AUTHENTICATED_FULLY')) {
            $event->setResponse(new RedirectResponse('/'));

            return;
        }
    }
}
