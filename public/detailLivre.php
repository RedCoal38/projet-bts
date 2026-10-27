<?php
$titre = 'Détail d’un livre - Ma médiathèque';
require_once __DIR__ . '/../src/donnees.php';
require_once __DIR__ . '/../src/fonctions.php';

/*
 * $_GET contient les paramètres transmis dans l'URL.
 * filter_input vérifie ici que id est bien un entier valide.
 * Si le paramètre est absent ou invalide, $id vaut null ou false.
 */
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// On ne recherche un livre que si l'identifiant est un entier positif.
$livre = ($id !== null && $id !== false && $id > 0)
    ? rechercherParId($livres, $id)
    : null;

// L'en-tête est chargé avant d'afficher le contenu HTML.
require __DIR__ . '/../includes/header.php';
?>
<main class="fiche-livre">
    <?php if ($livre === null): ?>
        <?php // Gérer l'absence de résultat évite les erreurs d'accès à un objet inexistant. ?>
        <h1>Livre introuvable</h1>
        <p>Aucun livre ne correspond à cet identifiant.</p>
    <?php else: ?>
        <section class="carte-livre">
            <h1><?= htmlspecialchars($livre->getTitre()) ?></h1>

            <p>
                <strong>Auteur :</strong>
                <?= htmlspecialchars($livre->getAuteur()) ?>
            </p>

            <p>
                <strong>Année :</strong>
                <?= $livre->getAnnee() ?>
            </p>
        </section>
    <?php endif; ?>
    <p><a href="listLivres.php">← Retour au catalogue</a></p>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>