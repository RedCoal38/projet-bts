<?php

// Charger les fonctions PDO de lecture des livres.
// __DIR__ pointe vers le dossier tests/ du projet.
require_once __DIR__ . '/../src/Database/livres.php';

// Cette fonction renvoie un tableau d'objets Livre, et non
// un tableau de tableaux associatifs.
$livres = recupererLivres();

// Observer la structure du résultat avant de l'utiliser.
// var_dump() permet de voir les objets Livre et leurs propriétés.
var_dump($livres);

echo PHP_EOL . 'Titres des livres et auteurs :' . PHP_EOL;

foreach ($livres as $livre) {
    // Les propriétés de Livre sont privées : on utilise les getters.
    // L'ancien accès $livre['titre'] ne fonctionne pas avec un objet.
    echo '- ' . $livre->getTitre() . ' — ' . $livre->getAuteur() . PHP_EOL;
}
