<?php

declare(strict_types=1);

namespace App\Donnees;

/**
 * Compte tel qu'il figure dans le fichier de données.
 * Le rôle est l'une des valeurs : utilisateur, hote, administrateur.
 */
final readonly class Utilisateur
{
    public function __construct(
        public string $id,
        public string $role,
        public string $prenom,
        public string $nom,
        public string $email,
        public string $motDePasseClair,
        public string $dateCreation,
    ) {
    }
}
