<?php

declare(strict_types=1);

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PlanController extends AbstractController
{
    public function __construct(private readonly TranslatorInterface $translator) {}

    #[Route('/plan', name: 'plan')]
    public function index(): Response
    {
        return $this->render('plan/index.html.twig');
    }

    #[Route('/plan/export', name: 'plan_export', methods: ['POST'])]
    public function export(Request $request): Response
    {
        // Plan data is ephemeral — not stored, only used to render PDF
        $raw = $request->getContent();
        $data = [];
        if ($raw) {
            try {
                $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                $data = [];
            }
        }

        $html = $this->renderView('plan/pdf.html.twig', [
            'warning_signs' => array_filter((array) ($data['warningSigns'] ?? [])),
            'coping_strategies' => array_filter((array) ($data['copingStrategies'] ?? [])),
            'trusted_contacts' => array_filter((array) ($data['trustedContacts'] ?? []), static fn($c) => !empty($c['name'])),
            'reasons' => array_filter((array) ($data['reasons'] ?? [])),
        ]);

        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $this->translator->trans('pdf.filename') . '"',
            ]
        );
    }
}
