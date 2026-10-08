<?php

declare(strict_types=1);

namespace App\Donnees;

/**
 * Logement tel qu'il figure dans le fichier de données, rattaché à son hôte par idHote.
 */
final readonly class Logement
{
    public function __construct(
        public string $id,
        public string $titre,
        public string $typeLogement,
        public string $ville,
        public string $prixParNuit,
        public string $idHote,
        public string $dateCreation,
    ) {
    }
}
