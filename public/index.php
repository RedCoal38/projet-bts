<?php
$titre = "Accueil - Ma médiathèque";

require __DIR__ . '/../includes/header.php';
?>

<main>
    <h1>Bienvenue dans ma médiathèque</h1>

    <p>
        Cette application permet de gérer une médiathèque :
        son catalogue de livres, ses adhérents et les emprunts.
    </p>

    <section>
        <h2>Catalogue des livres</h2>
        <p>
            Consultez les livres disponibles dans la médiathèque.
        </p>
        <a href="listLivres.php">Consulter le catalogue</a>
    </section>

    <section>
        <h2>Gestion des adhérents</h2>
        <p>
            Retrouvez les informations des adhérents.
        </p>
    </section>

    <section>
        <h2>Gestion des emprunts</h2>
        <p>
            Consultez les emprunts et les retours de livres.
        </p>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>