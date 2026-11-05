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

// Nouveau : chargement de la fonction qui récupère les catégories
// depuis MariaDB pour alimenter la liste déroulante du formulaire.
require_once __DIR__ . '/../src/Database/categories.php';

/*
 * Le formulaire ci-dessous transmet les critères avec la méthode GET.
 * Exemple : listLivres.php?recherche=bons&auteur=Gaiman&categorie=1
 *
 * filter_input() lit les paramètres de la requête HTTP.
 * trim() enlève les espaces inutiles des champs textuels.
 * Si un paramètre texte manque ou n'est pas une chaîne, il est vide.
 */
$parametreRecherche = filter_input(INPUT_GET, 'recherche', FILTER_UNSAFE_RAW);
$recherche = is_string($parametreRecherche) ? trim($parametreRecherche) : '';

$parametreAuteur = filter_input(INPUT_GET, 'auteur', FILTER_UNSAFE_RAW);
$auteur = is_string($parametreAuteur) ? trim($parametreAuteur) : '';

/*
 * Pour la catégorie, nous attendons un identifiant entier positif.
 * FILTER_VALIDATE_INT renvoie un entier si la saisie est valide,
 * false si elle est invalide et null si le paramètre est absent.
 * La valeur vide du choix « Toutes les catégories » signifie
 * que l'on ne doit appliquer aucun filtre sur la catégorie.
 */
$parametreCategorie = filter_input(
    INPUT_GET,
    'categorie',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);
$categorie = is_int($parametreCategorie) ? $parametreCategorie : null;

// Les valeurs de la liste déroulante viennent de la table categorie.
$categories = recupererCategories();

// Aucun critère : tous les livres sont affichés.
// Les autres combinaisons correspondent aux conditions AND du SQL.
$livres = recupererLivres($recherche, $auteur, $categorie);

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
     * FORMULAIRE DE RECHERCHE
     *
     * method="get" ajoute les valeurs saisies à l'URL.
     * name="recherche", "auteur", "categorie" désignent les paramètres GET.
     * Les attributs value conservent le texte saisi après l'envoi.
     * htmlspecialchars() protège les valeurs réinsérées dans le HTML.
     * Les catégories sont récupérées depuis MariaDB et non écrites
     * directement dans le formulaire : elles peuvent évoluer.
     */
    ?>
    <form method="get" action="listLivres.php">
        <label for="recherche">Titre :</label>
        <input
            type="search"
            id="recherche"
            name="recherche"
            value="<?= htmlspecialchars($recherche, ENT_QUOTES, 'UTF-8') ?>"
            placeholder="Ex. : Dune"
        >

        <label for="auteur">Auteur :</label>
        <input
            type="search"
            id="auteur"
            name="auteur"
            value="<?= htmlspecialchars($auteur, ENT_QUOTES, 'UTF-8') ?>"
            placeholder="Ex. : Gaiman"
        >

        <label for="categorie">Catégorie :</label>
        <select id="categorie" name="categorie">
            <option value="">Toutes les catégories</option>
            <?php foreach ($categories as $uneCategorie): ?>
                <?php
                // L'identifiant vient de la base et sert de valeur GET.
                // selected garde le choix précédent après la recherche.
                $idCategorie = (int) $uneCategorie['id_categorie'];
                ?>
                <option
                    value="<?= $idCategorie ?>"
                    <?= $categorie === $idCategorie ? 'selected' : '' ?>
                ><?= htmlspecialchars($uneCategorie['libelle'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Rechercher</button>
        <a href="listLivres.php">Afficher tous les livres</a>
    </form>

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
            <?php if ($livres === []): ?>
                <?php // Aucun résultat : afficher un message à la place des lignes. ?>
                <tr>
                    <td colspan="4">Aucun livre trouvé pour ces critères.</td>
                </tr>
            <?php endif; ?>
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
