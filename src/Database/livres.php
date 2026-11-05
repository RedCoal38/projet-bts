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
 * Objectif : récupérer les livres enregistrés dans la base,
 * éventuellement filtrés par leur titre, un auteur et une catégorie.
 *
 * Paramètres facultatifs :
 *   $recherche : partie du titre recherchée ;
 *   $auteur : partie du nom ou du prénom d'un auteur ;
 *   $categorie : identifiant d'une catégorie (null = toutes).
 *
 * Les critères renseignés se combinent avec AND :
 * le livre doit satisfaire chacun d'eux.
 * Si aucun critère n'est renseigné, tous les livres sont renvoyés.
 *
 * Retour : un tableau contenant des OBJETS Livre.
 *
 * Exemples :
 *   $livres = recupererLivres();
 *   $livres = recupererLivres('Dune');
 *   $livres = recupererLivres('', 'Gaiman');
 *   $livres = recupererLivres('bons', 'Gaiman', 1);
 *
 * Attention à la différence :
 *   - PDO::FETCH_ASSOC fournit des tableaux associatifs ;
 *   - la fonction renvoie des objets Livre après transformation.
 *
 * @return Livre[]
 */
function recupererLivres(string $recherche = '', string $auteur = '', ?int $categorie = null): array
{
    // 1. Établir une connexion avec MariaDB.
    // La fonction creerConnexion() retourne un objet PDO.
    $pdo = creerConnexion();

    // trim() enlève les espaces inutiles en début et en fin de recherche.
    // Une chaîne vide désactive le critère correspondant.
    $recherche = trim($recherche);
    $auteur = trim($auteur);

    // 2. Préparer le texte de notre requête SQL.
    //
    // Nous conservons les colonnes nécessaires à la création d'un objet Livre.
    // Les alias l, e et a abrègent les noms des tables :
    // - livre l : informations générales sur le livre ;
    // - ecrire e : association entre un livre et ses auteurs ;
    // - auteur a : prénom et nom des auteurs.
    //
    // LEFT JOIN conserve également les livres sans auteur associé.
    // Avec INNER JOIN, ces livres disparaîtraient du résultat.
    //
    // Une jointure simple peut renvoyer plusieurs lignes pour un livre
    // ayant plusieurs auteurs. GROUP_CONCAT() réunit leurs noms dans
    // une seule chaîne, par exemple « Neil Gaiman, Terry Pratchett ».
    //
    // CONCAT_WS(' ', a.prenom, a.nom) assemble prénom et nom avec
    // un espace, en ignorant les valeurs NULL.
    // ORDER BY a.nom, a.prenom donne un ordre aux auteurs.
    // SEPARATOR ', ' sépare leurs noms par une virgule et un espace.
    // GROUP BY regroupe les lignes qui appartiennent au même livre.
    //
    // AS auteurs nomme la colonne calculée : on pourra ensuite
    // lire sa valeur avec $ligne['auteurs'].
    $sql = <<<'SQL'
        SELECT
            l.id_livre,
            l.titre,
            l.annee_publication,
            l.prix,
            GROUP_CONCAT(
                CONCAT_WS(' ', a.prenom, a.nom)
                ORDER BY a.nom, a.prenom
                SEPARATOR ', '
            ) AS auteurs
        FROM livre l
        LEFT JOIN ecrire e ON e.id_livre = l.id_livre
        LEFT JOIN auteur a ON a.id_auteur = e.id_auteur
        SQL;

    // Nous construisons les conditions et leurs paramètres séparément.
    // Il n'y a donc aucune concaténation de texte saisi dans le SQL.
    $conditions = [];
    $parametres = [];

    // 3. Ajouter seulement les critères renseignés.
    //
    // LIKE permet de chercher un texte à l'intérieur du titre.
    // Les caractères % encadrant la saisie signifient « zéro,
    // un ou plusieurs caractères quelconques ».
    if ($recherche !== '') {
        $conditions[] = 'l.titre LIKE :recherche';
        $parametres['recherche'] = '%' . $recherche . '%';
    }

    // Filtrer sur l'auteur demande une précaution :
    //
    // Si l'on écrit directement « WHERE a.nom LIKE :auteur »,
    // les lignes des autres coauteurs seraient écartées avant
    // GROUP_CONCAT(). Un livre écrit par Gaiman et Pratchett
    // n'afficherait alors plus qu'un des deux noms.
    //
    // EXISTS teste s'il existe AU MOINS UN auteur correspondant
    // au critère pour ce livre, sans retirer les autres auteurs
    // des LEFT JOIN utilisés pour construire l'affichage.
    // Les alias ea et aa sont locaux à cette sous-requête.
    if ($auteur !== '') {
        $conditions[] = <<<'SQL'
            EXISTS (
                SELECT 1
                FROM ecrire ea
                INNER JOIN auteur aa ON aa.id_auteur = ea.id_auteur
                WHERE ea.id_livre = l.id_livre
                  AND CONCAT_WS(' ', aa.prenom, aa.nom) LIKE :auteur
            )
            SQL;
        $parametres['auteur'] = '%' . $auteur . '%';
    }

    // La liste déroulante transmet l'identifiant de la catégorie.
    // Le paramètre est optionnel : null signifie « toutes ».
    if ($categorie !== null) {
        $conditions[] = 'l.id_categorie = :categorie';
        $parametres['categorie'] = $categorie;
    }

    // implode(' AND ', ...) rassemble les conditions facultatives.
    // La clause WHERE n'est ajoutée que s'il existe au moins
    // un critère : autrement, la requête liste tous les livres.
    if ($conditions !== []) {
        $sql .= "\nWHERE " . implode("\n  AND ", $conditions);
    }

    // GROUP BY rassemble les auteurs de chaque livre.
    // ORDER BY trie les résultats affichés par titre.
    $sql .= <<<'SQL'

        GROUP BY
            l.id_livre,
            l.titre,
            l.annee_publication,
            l.prix
        ORDER BY l.titre
        SQL;

    // 4. Exécuter la requête SQL.
    //
    // prepare() prépare la requête, tandis que execute()
    // transmet séparément les valeurs des paramètres nommés.
    // Cela fonctionne aussi lorsque le tableau de paramètres
    // est vide (aucun filtre).
    //
    // Les % et _ éventuellement saisis par l'utilisateur restent
    // ici des caractères spéciaux de LIKE : nous ne traitons pas
    // encore leur échappement littéral dans cet exercice.
    $requete = $pdo->prepare($sql);
    $requete->execute($parametres);

    // 5. Récupérer toutes les lignes du résultat.
    //
    // fetchAll() retourne un tableau contenant les lignes.
    // PDO::FETCH_ASSOC représente chaque ligne par un tableau
    // associatif, par exemple $ligne['titre'].
    //
    // Exemple de ligne :
    // [
    //     'id_livre' => 1,
    //     'titre' => '1984',
    //     'annee_publication' => 1949,
    //     'prix' => '9.50',
    //     'auteurs' => 'George Orwell'
    // ]
    //
    // Si aucun livre ne correspond, fetchAll() retourne [].
    $lignes = $requete->fetchAll(PDO::FETCH_ASSOC);

    // 6. Transformer les lignes SQL en objets Livre.
    // Nous créons un tableau vide et parcourons chaque ligne.
    $livres = [];
    foreach ($lignes as $ligne) {
        // new Livre(...) appelle le constructeur de la classe Livre.
        // Les clés proviennent des colonnes SQL ou de leurs alias.
        // (int) et (float) convertissent les valeurs vers
        // les types attendus par le constructeur.
        //
        // Si un livre n'a pas d'auteur, GROUP_CONCAT() renvoie NULL.
        // L'opérateur ?? fournit alors « Non renseigné », une chaîne.
        $livres[] = new Livre(
            (int) $ligne['id_livre'],
            $ligne['titre'],
            $ligne['auteurs'] ?? 'Non renseigné',
            (int) $ligne['annee_publication'],
            (float) $ligne['prix']
        );
    }

    // 7. Renvoyer les livres au programme appelant.
    // Même si aucun résultat n'est trouvé, on renvoie [].
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
    //
    // Les LEFT JOIN récupèrent les auteurs éventuels du livre.
    // GROUP_CONCAT() regroupe leurs noms afin de ne renvoyer
    // qu'une seule ligne par livre, même s'il a plusieurs auteurs.
    $sql = <<<'SQL'
        SELECT
            l.id_livre,
            l.titre,
            l.annee_publication,
            l.prix,
            GROUP_CONCAT(
                CONCAT_WS(' ', a.prenom, a.nom)
                ORDER BY a.nom, a.prenom
                SEPARATOR ', '
            ) AS auteurs
        FROM livre l
        LEFT JOIN ecrire e ON e.id_livre = l.id_livre
        LEFT JOIN auteur a ON a.id_auteur = e.id_auteur
        WHERE l.id_livre = :id
        GROUP BY
            l.id_livre,
            l.titre,
            l.annee_publication,
            l.prix
        SQL;

    // 3. Préparer la requête SQL.
    // Contrairement à query(), prepare() n'exécute pas
    // immédiatement la requête.
    // Elle retourne un objet PDOStatement.
    $requete = $pdo->prepare($sql);

    // 4. Exécuter la requête en fournissant la valeur de :id.
    // Exemple : si $id vaut 3, MariaDB recherche
    // le livre dont id_livre = 3.
    // PDO associe la valeur au paramètre nommé.
    $requete->execute(['id' => $id]);

    // 5. Récupérer une seule ligne du résultat.
    // fetch() récupère une ligne, contrairement à
    // fetchAll() qui récupère toutes les lignes.
    // PDO::FETCH_ASSOC retourne un tableau associatif.
    // Si aucun livre n'est trouvé, fetch() retourne false.
    $ligne = $requete->fetch(PDO::FETCH_ASSOC);

    // 6. Gérer le cas où aucun livre ne correspond à l'identifiant.
    // La fonction annonce ?Livre : elle renvoie donc null
    // plutôt que false quand aucun résultat n'existe.
    if ($ligne === false) {
        return null;
    }

    // 7. Transformer la ligne trouvée en objet Livre.
    // Le constructeur attend une chaîne pour l'auteur.
    // Elle contient tous les auteurs regroupés par SQL,
    // ou « Non renseigné » si nécessaire.
    return new Livre(
        (int) $ligne['id_livre'],
        $ligne['titre'],
        $ligne['auteurs'] ?? 'Non renseigné',
        (int) $ligne['annee_publication'],
        (float) $ligne['prix']
    );
}
