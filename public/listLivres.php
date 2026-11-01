<?php
/*
 * PAGE : Catalogue des livres
 *
 * Cette page affiche une liste de livres dans un tableau HTML.
 * Les données sont récupérées depuis MariaDB.
 * La fonction recupererLivres() transforme chaque ligne SQL
 * en objet de la classe Livre.
 */

// Définition du titre de la page, utilisé dans header.php.
$titre = "Catalogue - Ma médiathèque";

/*
 * Chargement des fonctions permettant de récupérer les livres.
 *
 * require_once charge le fichier qui contient recupererLivres().
 * Cette fonction interroge MariaDB et renvoie un tableau
 * contenant des objets de la classe Livre.
 *
 * Le tableau $livres est utilisé pour construire le catalogue.
 * Chaque livre conserve ses méthodes getTitre(), getAuteur(), etc.
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
             * Chaque élément $livre est un objet de la classe Livre.
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
                     * $livre->getTitre() appelle une méthode de l'objet Livre.
                     * La page manipule des objets, même si PDO récupère
                     * initialement les données sous forme de tableaux associatifs.
                     *
                     * htmlspecialchars() convertit les caractères
                     * spéciaux HTML pour éviter qu'ils soient interprétés
                     * comme du code HTML ou JavaScript.
                     *
                     * L'année est un entier renvoyé par getAnnee() :
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
