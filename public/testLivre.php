<?php
require_once __DIR__ . '/../src/Model/Livre.php';

$livre = new Livre();

$livre->id = 1;
$livre->titre = '1984';
$livre->annee = 1949;
$livre->prix = 12.50;

echo '<h2>Test de la classe Livre</h2>';

echo '<p>Titre : ' . htmlspecialchars($livre->titre) . '</p>';
echo '<p>Prix : ' . $livre->prix . ' €</p>';

echo '<h3>Inspection de l\'objet avec var_dump()</h3>';

echo '<pre>';
var_dump($livre);
echo '</pre>';