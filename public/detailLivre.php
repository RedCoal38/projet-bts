<?php
$titre = 'Détail d’un livre - Ma médiathèque';
/*
 * Le fichier livres.php fournit recupererLivreParId().
 * Cette fonction recherche le livre directement dans MariaDB
 * et renvoie un objet Livre ou null si aucun livre ne correspond.
 * Elle charge également la classe Livre.
 */
require_once __DIR__ . '/../src/Database/livres.php';

/*
 * $_GET contient les paramètres transmis dans l'URL.
 * filter_input vérifie ici que id est bien un entier valide.
 * Si le paramètre est absent ou invalide, $id vaut null ou false.
 */
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// On ne recherche un livre que si l'identifiant est un entier positif.
// recupererLivreParId($id) exécute une requête SQL préparée.
// $livre est un objet Livre ou null si aucun résultat n'est trouvé.
$livre = ($id !== null && $id !== false && $id > 0)
    ? recupererLivreParId($id)
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
