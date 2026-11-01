<?php

/*
 * FICHIER : src/Database/livres.php
 *
 * Ce fichier regroupe les fonctions permettant de
 * récupérer les informations des livres depuis MariaDB.
 *
 * Il utilise PDO pour communiquer avec la base de données.
 *
 * PDO récupère les lignes sous forme de tableaux associatifs.
 * Nous transformons ensuite ces lignes en objets de la classe Livre.
 *
 * Ainsi, les pages de l'application peuvent continuer à utiliser
 * les getters (getTitre(), getAuteur(), etc.) de la classe Livre.
 */

// Chargement du fichier qui contient la fonction creerConnexion().
// require_once évite de charger plusieurs fois le même fichier.
require_once __DIR__ . '/connexion.php';

// Chargement de la classe Livre pour pouvoir créer des objets Livre.
// __DIR__ désigne ici le dossier src/Database ; ../ remonte vers src/.
require_once __DIR__ . '/../Model/Livre.php';


/*
 * FONCTION : recupererLivres()
 *
 * Objectif : récupérer tous les livres enregistrés dans la base.
 *
 * Retour :
 *   Un tableau contenant des OBJETS Livre.
 *
 * Exemple d'utilisation :
 *   $livres = recupererLivres();
 *   echo $livres[0]->getTitre();
 *
 * Attention à la différence :
 *   - PDO::FETCH_ASSOC fournit des tableaux associatifs ;
 *   - la fonction renvoie des objets Livre après transformation.
 *
 * @return Livre[]
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
    // $ligne['titre'] au lieu de $ligne[1].
    //
    // Exemple d'une ligne récupérée :
    // [
    //     'id_livre' => 1,
    //     'titre' => '1984',
    //     'annee_publication' => 1949,
    //     'prix' => '12.50'
    // ]
    //
    // Si aucun livre n'existe, fetchAll() retourne [].
    $lignes = $requete->fetchAll(PDO::FETCH_ASSOC);

    // 5. Transformer les lignes SQL en objets Livre.
    //
    // Nous créons un tableau vide qui accueillera les objets.
    // foreach parcourt chaque ligne renvoyée par PDO.
    $livres = [];

    foreach ($lignes as $ligne) {
        // new Livre(...) appelle le constructeur de la classe Livre.
        //
        // Les noms des clés viennent des colonnes SQL :
        // id_livre, titre, annee_publication et prix.
        //
        // (int) et (float) convertissent les valeurs vers
        // les types attendus par le constructeur de Livre.
        //
        // L'auteur est provisoirement « Non renseigné » :
        // la table livre ne contient pas directement son nom.
        // Les auteurs seront récupérés grâce aux jointures SQL
        // dans une prochaine étape du projet.
        $livres[] = new Livre(
            (int) $ligne['id_livre'],
            $ligne['titre'],
            'Non renseigné',
            (int) $ligne['annee_publication'],
            (float) $ligne['prix']
        );
    }

    // 6. Renvoyer le tableau d'objets au programme appelant.
    // Même si la base ne contient aucun livre, on renvoie [].
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
 *   - un OBJET Livre si le livre existe ;
 *   - null si aucun livre ne correspond.
 *
 * Le type ?Livre signifie que la fonction peut retourner
 * soit un objet Livre, soit null.
 */
function recupererLivreParId(int $id): ?Livre
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
    $ligne = $requete->fetch(PDO::FETCH_ASSOC);

    // 6. Gérer le cas où aucun livre ne correspond à l'identifiant.
    //
    // La fonction annonce ?Livre : elle renvoie donc null
    // plutôt que false quand aucun résultat n'existe.
    if ($ligne === false) {
        return null;
    }

    // 7. Transformer la ligne trouvée en objet Livre.
    //
    // Comme dans recupererLivres(), on utilise les valeurs
    // du tableau associatif pour appeler le constructeur.
    //
    // L'auteur sera complété lorsque nous aborderons
    // les jointures entre livre, ecrire et auteur.
    return new Livre(
        (int) $ligne['id_livre'],
        $ligne['titre'],
        'Non renseigné',
        (int) $ligne['annee_publication'],
        (float) $ligne['prix']
    );
}
