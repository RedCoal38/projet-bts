<?php
$titre = "Catalogue - Ma médiathèque";

$livres = [
    ["titre" => "1984", "auteur" => "George Orwell", "annee" => 1949],
    ["titre" => "Germinal", "auteur" => "Émile Zola", "annee" => 1885],
    ["titre" => "Les Misérables", "auteur" => "Victor Hugo", "annee" => 1862]
];

require __DIR__ . '/../includes/header.php';
?>

<main>
    <h1>Catalogue des livres</h1>

    <p>Voici les livres enregistrés dans notre médiathèque.</p>

    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Année</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($livres as $livre): ?>
                <tr>
                    <td><?= htmlspecialchars($livre['titre']) ?></td>
                    <td><?= htmlspecialchars($livre['auteur']) ?></td>
                    <td><?= $livre['annee'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>