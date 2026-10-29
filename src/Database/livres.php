
<?php

/*
 * FICHIER : src/Database/livres.php
 *
 * Ce fichier regroupe les fonctions permettant de
 * récupérer les informations des livres depuis MariaDB.
 *
 * Il utilise PDO pour communiquer avec la base de données.
 *
 * Les résultats sont retournés sous forme de tableaux
 * associatifs, et non d'objets de la classe Livre.
 */

// Chargement du fichier qui contient la fonction creerConnexion().
// require_once évite de charger plusieurs fois le même fichier.
require_once __DIR__ . '/connexion.php';


/*
 * FONCTION : recupererLivres()
 *
 * Objectif : récupérer tous les livres enregistrés dans la base.
 *
 * Retour :
 *   Un tableau contenant les livres.
 *   Chaque livre est représenté par un tableau associatif.
 *
 * Exemple :
 * [
 *     [
 *         'id_livre' => 1,
 *         'titre' => '1984',
 *         'annee_publication' => 1949,
 *         'prix' => '12.50'
 *     ],
 *     ...
 * ]
 */
function recupererLivres(): array
{
    // 1. Établir une connexion avec MariaDB.
    // La fonction creerConnexion() retourne un objet PDO.
    $pdo = creerConnexion();

    // 2. Préparer le texte de notre requête SQL.
    // SELECT * permet de récupérer toutes les colonnes
    // de la table livre.
    $sql = 'SELECT * FROM livre';

    // 3. Envoyer la requête SQL à MariaDB.
    // query() exécute directement la requête.
    // Elle retourne un objet PDOStatement qui représente
    // le résultat de la requête.
    $requete = $pdo->query($sql);

    // 4. Récupérer toutes les lignes du résultat.
    //
    // fetchAll() retourne un tableau contenant les lignes.
    //
    // PDO::FETCH_ASSOC indique que chaque ligne doit être
    // représentée par un tableau associatif :
    //
    // $livre['titre'] au lieu de $livre[1].
    //
    // Si aucun livre n'existe, fetchAll() retourne [].
    $livres = $requete->fetchAll(PDO::FETCH_ASSOC);

    // 5. Renvoyer les livres au programme appelant.
    return $livres;
}


/*
 * FONCTION : recupererLivreParId()
 *
 * Objectif : récupérer un seul livre à partir de son identifiant.
 *
 * Paramètre :
 *   $id : identifiant du livre recherché (entier).
 *
 * Retour :
 *   - un tableau associatif si le livre existe ;
 *   - null si aucun livre ne correspond.
 *
 * Le type ?array signifie que la fonction peut retourner
 * soit un tableau (array), soit null.
 */
function recupererLivreParId(int $id): ?array
{
    // 1. Établir une connexion avec MariaDB.
    $pdo = creerConnexion();

    // 2. Écrire la requête SQL.
    //
    // :id est un paramètre nommé.
    // Sa valeur sera transmise séparément à execute().
    //
    // Cela permet d'éviter de construire une requête SQL
    // en concaténant directement une valeur fournie
    // par l'utilisateur.
    $sql = 'SELECT * FROM livre WHERE id_livre = :id';

    // 3. Préparer la requête SQL.
    //
    // Contrairement à query(), prepare() n'exécute pas
    // immédiatement la requête.
    // Elle retourne un objet PDOStatement.
    $requete = $pdo->prepare($sql);

    // 4. Exécuter la requête en fournissant la valeur de :id.
    //
    // Exemple : si $id vaut 3, MariaDB recherche
    // le livre dont id_livre = 3.
    //
    // PDO associe la valeur au paramètre nommé.
    $requete->execute(['id' => $id]);

    // 5. Récupérer une seule ligne du résultat.
    //
    // fetch() récupère une ligne, contrairement à
    // fetchAll() qui récupère toutes les lignes.
    //
    // PDO::FETCH_ASSOC retourne un tableau associatif.
    //
    // Si aucun livre n'est trouvé, fetch() retourne false.
    $livre = $requete->fetch(PDO::FETCH_ASSOC);

    // 6. Renvoyer le résultat.
    //
    // Si fetch() a retourné false, on retourne null.
    // Sinon, on retourne le tableau associatif du livre.
    return $livre === false ? null : $livre;
}
