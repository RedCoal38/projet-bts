<?php
/*
 * CONFIGURATION DE L'AFFICHAGE DES ERREURS PHP
 *
 * Par défaut, les erreurs PHP peuvent être enregistrées dans
 * les journaux (logs) du serveur sans apparaître dans le navigateur.
 *
 * Pendant le développement, afficher les erreurs permet de repérer
 * plus facilement un problème dans le code.
 *
 * Attention : ces réglages sont destinés au développement local.
 * Sur un site en production, les erreurs ne doivent pas être
 * affichées aux visiteurs.
 */

// Demande à PHP d'afficher les erreurs dans le navigateur.
ini_set('display_errors', '1');

// Demande l'affichage des erreurs survenant au démarrage de PHP.
// Certaines erreurs de démarrage ne peuvent toutefois pas être
// activées depuis un script déjà en cours d'exécution.
ini_set('display_startup_errors', '1');

// Demande à PHP de signaler toutes les catégories d'erreurs,
// y compris les avertissements (warnings) et les notices.
error_reporting(E_ALL);

// Chargement de notre classe métier Livre.
require_once __DIR__ . '/../src/Model/Livre.php';

$livre = new Livre(1, '1984', 1949, 12.50);

echo '<h2>Test de la classe Livre — encapsulation</h2>';
echo '<p>Titre : ' . htmlspecialchars($livre->getTitre(), ENT_QUOTES, 'UTF-8') . '</p>';
echo '<p>Prix : ' . $livre->getPrix() . ' €</p>';

echo '<h3>Inspection de l’objet avec var_dump()</h3>';
echo '<pre>';
var_dump($livre);
echo '</pre>';

$livre->setPrix(15.00);
echo '<p>Prix après modification : ' . $livre->getPrix() . ' €</p>';

// À décommenter pour constater l’erreur liée à l’encapsulation :
// $livre->prix = -50;

// À décommenter pour tester la validation du setter :
// $livre->setPrix(-50);