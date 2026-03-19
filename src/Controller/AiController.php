<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ClaudeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;

final class AiController extends AbstractController
{
    public function __construct(
        private readonly ClaudeService $claude,
        private readonly RateLimiterFactory $reasonsApiLimiter,
    ) {}

    #[Route('/api/reasons', name: 'api_reasons', methods: ['POST'])]
    public function generate(Request $request): Response
    {
        $limiter = $this->reasonsApiLimiter->create($request->getClientIp() ?? 'unknown');
        if (!$limiter->consume(1)->isAccepted()) {
            return new Response(
                $this->renderView('plan/_letter.html.twig', [
                    'letter' => '',
                    'rate_limited' => true,
                ]),
                429,
            );
        }

        $name = (string) $request->request->get('name', '');
        $locale = $request->getLocale();

        $letter = $this->claude->generateReasonLetter($name, $locale);

        return $this->render('plan/_letter.html.twig', [
            'letter' => $letter,
            'rate_limited' => false,
        ]);
    }
}
