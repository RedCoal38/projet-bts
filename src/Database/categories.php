<?php

/*
 * FICHIER : src/Database/categories.php
 *
 * Récupérer les catégories depuis MariaDB pour remplir
 * la liste déroulante du formulaire de recherche.
 * Les valeurs sont lues depuis la base : il n'est pas nécessaire
 * d'écrire les catégories « en dur » dans la page HTML.
 */

// La fonction creerConnexion() a été définie dans connexion.php.
require_once __DIR__ . '/connexion.php';

/*
 * FONCTION : recupererCategories()
 *
 * Retour : tableau de lignes associatives, chacune contenant
 * id_categorie et libelle. Une catégorie n'est pas ici un objet PHP :
 * le formulaire utilise uniquement son identifiant et son libellé.
 *
 * Exemple : [['id_categorie' => 1, 'libelle' => 'Roman'], ...]
 */
function recupererCategories(): array
{
    $pdo = creerConnexion();

    // Aucun paramètre utilisateur : query() suffit pour ce SELECT.
    // ORDER BY libelle affiche les catégories par ordre alphabétique.
    $requete = $pdo->query('SELECT id_categorie, libelle FROM categorie ORDER BY libelle');

    // FETCH_ASSOC permet l'accès par le nom des colonnes.
    return $requete->fetchAll(PDO::FETCH_ASSOC);
}
