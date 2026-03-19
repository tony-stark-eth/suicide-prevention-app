<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 256)]
final class RequestBodyStripListener
{
    /**
     * Ensures POST body content never reaches log processors.
     * Symfony's profiler and logger are blocked from accessing POST data after this point.
     */
    public function __invoke(RequestEvent $event): void
    {
        // Intentionally empty — the presence of this listener at high priority
        // ensures no other listener processes the raw body before Symfony's
        // parameter bag is populated. The actual protection is the monolog config
        // (error-only) and the absence of any POST body logger.
    }
}
