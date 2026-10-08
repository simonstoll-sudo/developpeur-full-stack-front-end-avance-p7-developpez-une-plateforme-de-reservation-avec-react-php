<?php

declare(strict_types=1);

namespace App\Controller;

use App\Donnees\ChargeurCsv;
use App\Donnees\Logement;
use App\Donnees\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class LogementController extends AbstractController
{
    public function __construct(private ChargeurCsv $chargeur)
    {
    }

    #[Route('/api/logements', name: 'logements_liste', methods: ['GET'])]
    public function liste(): JsonResponse
    {
        $donnees = $this->chargeur->charger();

        return $this->json($this->enrichirTous($donnees->logements, $donnees->utilisateurs));
    }

    #[Route('/api/logements/{id}', name: 'logements_detail', methods: ['GET'])]
    public function detail(string $id): JsonResponse
    {
        $donnees = $this->chargeur->charger();
        $enrichis = $this->enrichirTous($donnees->logements, $donnees->utilisateurs);
        $trouve = array_find(
            $enrichis,
            /** @param array<string, mixed> $logement */
            static fn (array $logement): bool => $logement['id'] === $id,
        );

        return $this->json($trouve);
    }

    /**
     * @param list<Logement>    $logements
     * @param list<Utilisateur> $utilisateurs
     *
     * @return list<array<string, mixed>>
     */
    private function enrichirTous(array $logements, array $utilisateurs): array
    {
        return array_map(
            fn (Logement $logement): array => $this->enrichir($logement, $utilisateurs),
            $logements,
        );
    }

    /**
     * Ajoute au logement le prénom et le nom de son hôte, retrouvés parmi les comptes.
     * Seules ces deux informations sur l'hôte sont exposées par l'API.
     *
     * @param list<Utilisateur> $utilisateurs
     *
     * @return array<string, mixed>
     */
    private function enrichir(Logement $logement, array $utilisateurs): array
    {
        $hote = array_find(
            $utilisateurs,
            static fn (Utilisateur $utilisateur): bool => $utilisateur->id === $logement->idHote,
        );

        return [
            'id' => $logement->id,
            'titre' => $logement->titre,
            'typeLogement' => $logement->typeLogement,
            'ville' => $logement->ville,
            'prixParNuit' => $logement->prixParNuit,
            'idHote' => $logement->idHote,
            'dateCreation' => $logement->dateCreation,
            'hote' => null === $hote ? null : ['prenom' => $hote->prenom, 'nom' => $hote->nom],
        ];
    }
}
