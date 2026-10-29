
<?php
/*
 * PAGE : Détail d'un livre
 *
 * Cette page affiche les informations d'un livre
 * récupéré depuis MariaDB grâce à son identifiant.
 */

$titre = 'Détail d’un livre - Ma médiathèque';

// Chargement des fonctions permettant d'interroger MariaDB.
require_once __DIR__ . '/../src/Database/livres.php';

/*
 * $_GET contient les paramètres transmis dans l'URL.
 *
 * Exemple : detailLivre.php?id=2
 *
 * filter_input vérifie ici que id est bien un entier valide.
 * Si le paramètre est absent ou invalide, $id vaut null ou false.
 */
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

/*
 * On ne recherche un livre que si l'identifiant est
 * un entier positif.
 *
 * recupererLivreParId() effectue une requête préparée
 * et retourne un tableau associatif si le livre existe,
 * ou null si aucun livre ne correspond.
 */
$livre = ($id !== null && $id !== false && $id > 0)
    ? recupererLivreParId($id)
    : null;

// L'en-tête est chargé avant d'afficher le contenu HTML.
require __DIR__ . '/../includes/header.php';
?>

<main class="fiche-livre">
    <?php if ($livre === null): ?>
        <?php
        /*
         * Si aucun livre n'est trouvé, on affiche un message.
         * Cela évite d'essayer d'accéder à des données inexistantes.
         */
        ?>
        <h1>Livre introuvable</h1>
        <p>Aucun livre ne correspond à cet identifiant.</p>

    <?php else: ?>
        <section class="carte-livre">

            <h1><?= htmlspecialchars($livre['titre']) ?></h1>

            <p>
                <strong>Auteur :</strong>
                Non renseigné
            </p>

            <p>
                <strong>Année :</strong>
                <?= htmlspecialchars((string) $livre['annee_publication']) ?>
            </p>

            <p>
                <strong>Prix :</strong>
                <?= number_format((float) $livre['prix'], 2, ',', ' ') ?> €
            </p>

        </section>
    <?php endif; ?>

    <p><a href="listLivres.php">← Retour au catalogue</a></p>
</main>

<?php
// Inclusion du pied de page commun.
require __DIR__ . '/../includes/footer.php';
?>
