<?php
/**
 * Recherche un livre par son identifiant dans un tableau de livres.
 * Retourne le tableau associatif trouvé, ou null si aucun livre ne correspond.
 */
require_once __DIR__ . '/Model/Livre.php';

function rechercherParId(array $livres, int $id): ?Livre
{
    foreach ($livres as $livre) {
        if ($livre->getId() === $id) {
            return $livre;
        }
    }

    return null;
}