<?php
// Demande à PHP d'afficher les erreurs dans le navigateur.
ini_set('display_errors', '1');

// Demande l'affichage des erreurs survenant au démarrage de PHP.
// Certaines erreurs de démarrage ne peuvent toutefois pas être
// activées depuis un script déjà en cours d'exécution.
ini_set('display_startup_errors', '1');

// Demande à PHP de signaler toutes les catégories d'erreurs,
// y compris les avertissements (warnings) et les notices.
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($titre) ?></title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header>
        <h1>Ma médiathèque</h1>

        <nav>
            <a href="index.php">Accueil</a>
            <a href="listLivres.php">Livres</a>
            <a href="#">Adhérents</a>
            <a href="#">Emprunts</a>
        </nav>
    </header>