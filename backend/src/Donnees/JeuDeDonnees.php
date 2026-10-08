<?php

declare(strict_types=1);

namespace App\Donnees;

/**
 * Ensemble des données lues dans le fichier CSV : les comptes et les logements.
 */
final readonly class JeuDeDonnees
{
    /**
     * @param list<Utilisateur> $utilisateurs
     * @param list<Logement>    $logements
     */
    public function __construct(
        public array $utilisateurs,
        public array $logements,
    ) {
    }
}
