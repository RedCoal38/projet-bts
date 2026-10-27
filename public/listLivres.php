
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
 * Chargement du tableau de livres depuis src/donnees.php.
 *
 * Le tableau est désormais partagé entre la liste des livres
 * et leur fiche détaillée, afin d'éviter de dupliquer les données.
 */
require_once __DIR__ . '/../src/donnees.php';

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
            * Chaque élément $livre est désormais un objet de la classe Livre.
            * Les getters permettent d'accéder à ses propriétés privées.
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
                    <td><?= htmlspecialchars($livre->getTitre()) ?></td>
                    <td><?= htmlspecialchars($livre->getAuteur()) ?></td>
                    <td><?= $livre->getAnnee() ?></td>

                    <?php
                    /*
                     * Le lien transmet l'identifiant du livre dans l'URL.
                     *
                     * Exemple : detailLivre.php?id=2
                     *
                     * La page detailLivre.php récupérera cet identifiant
                     * pour rechercher et afficher le livre correspondant.
                     */
                    ?>
                    <td>
                    <a href="detailLivre.php?id=<?= $livre->getId() ?>">
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
