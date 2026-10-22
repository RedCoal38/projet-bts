
<?php
/*
 * PAGE : Catalogue des livres
 *
 * Cette page affiche une liste de livres dans un tableau HTML.
 * Pour le moment, les données sont stockées dans un tableau PHP.
 * Plus tard, elles seront récupérées dans une base de données.
 */

// Définition du titre de la page, utilisé dans header.php.
$titre = "Catalogue - Ma médiathèque";

/*
 * Création d'un tableau contenant plusieurs livres.
 *
 * $livres est un tableau indexé : ses éléments sont numérotés
 * automatiquement à partir de 0.
 *
 * Chaque élément est lui-même un tableau associatif :
 * on accède aux informations par les clés 'titre', 'auteur' et 'annee'.
 *
 * Exemple :
 * $livres[0]['titre'] contient "1984".
 */
$livres = [
    ["titre" => "1984", "auteur" => "George Orwell", "annee" => 1949],
    ["titre" => "Germinal", "auteur" => "Émile Zola", "annee" => 1885],
    ["titre" => "Les Misérables", "auteur" => "Victor Hugo", "annee" => 1862]
];

/*
 * require permet d'inclure et d'exécuter un autre fichier PHP.
 *
 * __DIR__ représente le répertoire du fichier actuel : public.
 * ../ permet de remonter au répertoire parent : mediatheque.
 *
 * On charge ici l'en-tête commun aux pages du site.
 * Si le fichier est introuvable, require provoque une erreur fatale.
 */
require __DIR__ . '/../includes/header.php';
?>

<?php
/*
 * Le HTML ci-dessous définit le contenu principal de la page.
 *
 * <main> représente le contenu principal.
 * <h1> définit le titre principal.
 * <p> représente un paragraphe.
 */
?>
<main>
    <h1>Catalogue des livres</h1>

    <p>Voici les livres enregistrés dans notre médiathèque.</p>

    <?php
    /*
     * Construction d'un tableau HTML.
     *
     * <table> : tableau
     * <thead> : groupe des lignes d'en-tête
     * <tr>    : ligne
     * <th>    : cellule d'en-tête
     * <tbody> : groupe des lignes de données
     * <td>    : cellule contenant une donnée
     */
    ?>
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Année</th>
            </tr>
        </thead>

        <tbody>
            <?php
            /*
             * foreach parcourt les éléments du tableau $livres.
             *
             * À chaque passage, $livre contient les informations
             * d'un livre différent.
             *
             * 1er passage : "1984"
             * 2e passage : "Germinal"
             * 3e passage : "Les Misérables"
             *
             * La syntaxe foreach (...): ... endforeach;
             * est une alternative aux accolades { }.
             * Elle est pratique lorsqu'on mélange PHP et HTML.
             *
             * Chaque passage génère une nouvelle ligne <tr>.
             */
            foreach ($livres as $livre):
            ?>
                <tr>
                    <?php
                    /*
                     * <?= ... ?> est une écriture courte de echo.
                     * Elle permet d'afficher une valeur dans le HTML.
                     *
                     * $livre['titre'] récupère le titre du livre courant.
                     *
                     * htmlspecialchars() convertit les caractères
                     * spéciaux HTML pour éviter qu'ils soient interprétés
                     * comme du code HTML ou JavaScript.
                     *
                     * L'année est un entier dans notre tableau :
                     * elle peut être affichée directement.
                     */
                    ?>
                    <td><?= htmlspecialchars($livre['titre']) ?></td>
                    <td><?= htmlspecialchars($livre['auteur']) ?></td>
                    <td><?= $livre['annee'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php
/*
 * Inclusion du pied de page commun.
 *
 * footer.php contient le pied de page et les balises
 * de fermeture </body> et </html>.
 */
require __DIR__ . '/../includes/footer.php';
?>
