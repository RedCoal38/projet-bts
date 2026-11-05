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
 * éventuellement filtrés par une partie de leur titre.
 *
 * Paramètre :
 *   $recherche : texte recherché dans le titre (chaîne facultative).
 *   Si la recherche est vide, tous les livres sont renvoyés.
 *
 * Retour :
 *   Un tableau contenant des OBJETS Livre.
 *
 * Exemple d'utilisation :
 *   $livres = recupererLivres(); // tous les livres
 *   $livres = recupererLivres("Dune"); // titres contenant « Dune »
 *   echo $livres[0]->getTitre();
 *
 * Attention à la différence :
 *   - PDO::FETCH_ASSOC fournit des tableaux associatifs ;
 *   - la fonction renvoie des objets Livre après transformation.
 *
 * @return Livre[]
 */
function recupererLivres(string $recherche = ''): array
{
    // 1. Établir une connexion avec MariaDB.
    // La fonction creerConnexion() retourne un objet PDO.
    $pdo = creerConnexion();

    // trim() enlève les espaces inutiles en début et en fin de recherche.
    // Une recherche vide signifie « afficher tous les livres ».
    $recherche = trim($recherche);

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
    // ORDER BY a.nom donne un ordre aux auteurs.
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

    // Une clause WHERE peut être placée après les jointures et avant GROUP BY.
    // Nous ne l'ajoutons que si l'utilisateur a saisi une recherche.
    $rechercheActive = $recherche !== '';
    if ($rechercheActive) {
        $sql .= "\nWHERE l.titre LIKE :recherche";
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

    // 3. Exécuter la requête SQL selon la recherche demandée.
    //
    // Sans recherche, query() exécute directement le SELECT sans paramètre.
    // Avec recherche, prepare() prépare le SQL contenant :recherche,
    // puis execute() transmet séparément la valeur recherchée.
    // Cela évite d'injecter directement une valeur utilisateur dans SQL.
    //
    // Les signes % ajoutés à la valeur signifient « n'importe quelle
    // suite de caractères ». « %Dune% » correspond donc à « Dune ».
    // NB : les signes % et _ éventuellement saisis par l'utilisateur
    // restent ici des caractères spéciaux pour LIKE.
    if (!$rechercheActive) {
        $requete = $pdo->query($sql);
    } else {
        $requete = $pdo->prepare($sql);
        $requete->execute(['recherche' => '%' . $recherche . '%']);
    }

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
    //     'prix' => '9.50',
    //     'auteurs' => 'George Orwell'
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
        // Les noms des clés viennent des colonnes SQL ou de leurs alias :
        // id_livre, titre, annee_publication, prix et auteurs.
        //
        // (int) et (float) convertissent les valeurs vers
        // les types attendus par le constructeur de Livre.
        //
        // S'il n'existe aucun auteur associé au livre,
        // GROUP_CONCAT() renvoie NULL. L'opérateur ?? permet alors
        // de fournir la valeur de remplacement « Non renseigné ».
        // Cela respecte le type string attendu par Livre.
        $livres[] = new Livre(
            (int) $ligne['id_livre'],
            $ligne['titre'],
            $ligne['auteurs'] ?? 'Non renseigné',
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
    // Le constructeur de Livre attend toujours une chaîne
    // pour l'auteur. Elle contient désormais tous les auteurs
    // regroupés par SQL, ou « Non renseigné » si nécessaire.
    return new Livre(
        (int) $ligne['id_livre'],
        $ligne['titre'],
        $ligne['auteurs'] ?? 'Non renseigné',
        (int) $ligne['annee_publication'],
        (float) $ligne['prix']
    );
}
