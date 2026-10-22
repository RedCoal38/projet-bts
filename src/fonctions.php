<?php
/**
 * Recherche un livre par son identifiant dans un tableau de livres.
 * Retourne le tableau associatif trouvé, ou null si aucun livre ne correspond.
 */
function rechercherParId(array $livres, int $id): ?array
{
    foreach ($livres as $livre) {
        if ($livre['id'] === $id) {
            return $livre;
        }
    }
    return null;
}
