
<?php
/*
 * PAGE : Catalogue des livres
 *
 * Cette page affiche une liste de livres dans un tableau HTML.
 * Les données sont désormais récupérées depuis MariaDB
 * grâce à PDO.
 */

// Définition du titre de la page, utilisé dans header.php.
$titre = "Catalogue - Ma médiathèque";

/*
 * Chargement des livres depuis la base de données.
 *
 * Le fichier src/Database/livres.php contient la fonction
 * recupererLivres(), qui exécute une requête SQL avec PDO.
 *
 * La fonction retourne un tableau de tableaux associatifs.
 */
require_once __DIR__ . '/../src/Database/livres.php';

$livres = recupererLivres();

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
    <table class="tableau-livres">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Année</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php
            /*
             * foreach parcourt le tableau $livres.
             *
             * Chaque élément $livre est maintenant un tableau
             * associatif retourné par PDO.
             *
             * On accède donc aux valeurs avec :
             * $livre['titre']
             *
             * et non plus avec les méthodes d'un objet Livre.
             */
            foreach ($livres as $livre):
            ?>
                <tr>
                    <?php
                    /*
                     * <?= ... ?> est une écriture courte de echo.
                     *
                     * htmlspecialchars() empêche les caractères
                     * spéciaux d'être interprétés comme du HTML.
                     *
                     * L'auteur n'est pas encore récupéré :
                     * il faudra effectuer des jointures SQL
                     * avec les tables ecrire et auteur.
                     */
                    ?>
                    <td><?= htmlspecialchars($livre['titre']) ?></td>
                    <td>Non renseigné</td>
                    <td><?= htmlspecialchars((string) $livre['annee_publication']) ?></td>

                    <?php
                    /*
                     * Le lien transmet l'identifiant du livre
                     * dans l'URL.
                     *
                     * Exemple : detailLivre.php?id=2
                     *
                     * La page detailLivre.php utilise cet identifiant
                     * pour retrouver le livre dans MariaDB.
                     */
                    ?>
                    <td>
                        <a href="detailLivre.php?id=<?= $livre['id_livre'] ?>">
                            Voir le détail
                        </a>
                    </td>
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
