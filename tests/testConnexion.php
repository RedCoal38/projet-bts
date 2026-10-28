<?php
require_once __DIR__ . '/../src/Database/connexion.php';
try {
    $pdo = creerConnexion();
    echo "Connexion à MariaDB réussie.\n";
} catch (PDOException $e) {
    fwrite(STDERR, "Connexion impossible (vérifier les paramètres et MariaDB).\n");
    exit(1);
}
