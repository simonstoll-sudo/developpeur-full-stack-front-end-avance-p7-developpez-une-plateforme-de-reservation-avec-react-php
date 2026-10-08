<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Point d'entrée de supervision : répond dès que l'application est démarrée.
 */
final class SanteController extends AbstractController
{
    #[Route('/api/sante', name: 'sante', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return $this->json(['statut' => 'ok']);
    }
}
