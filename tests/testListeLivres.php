<?php
require_once __DIR__ . '/../src/Database/livres.php';

$livres = recupererLivres();

// Observer la structure du résultat avant de l'utiliser.
var_dump($livres);

echo PHP_EOL . 'Titres des livres :' . PHP_EOL;

foreach ($livres as $livre) {
    echo '- ' . $livre['titre'] . PHP_EOL;
}
