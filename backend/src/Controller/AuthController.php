<?php

declare(strict_types=1);

namespace App\Controller;

use App\Donnees\ChargeurCsv;
use App\Donnees\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController
{
    public function __construct(private ChargeurCsv $chargeur)
    {
    }

    #[Route('/api/auth/login', name: 'auth_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $email = $request->getPayload()->get('email');
        $motDePasse = $request->getPayload()->get('motDePasse');
        $donnees = $this->chargeur->charger();

        $utilisateur = array_find(
            $donnees->utilisateurs,
            static fn (Utilisateur $candidat): bool => $candidat->email === $email && $candidat->motDePasseClair === $motDePasse,
        );

        if (null === $utilisateur) {
            return $this->json(['message' => 'Identifiants invalides'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'id' => $utilisateur->id,
            'role' => $utilisateur->role,
            'prenom' => $utilisateur->prenom,
            'nom' => $utilisateur->nom,
            'email' => $utilisateur->email,
        ]);
    }
}
