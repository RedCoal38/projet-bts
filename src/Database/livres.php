<?php
require_once __DIR__ . '/connexion.php';

/**
 * Récupère les livres de la base sous forme de tableaux associatifs.
 */
function recupererLivres(): array
{
    $pdo = creerConnexion();

    $sql = 'SELECT * FROM livre';
    $requete = $pdo->query($sql);

    return $requete->fetchAll(PDO::FETCH_ASSOC);
}
