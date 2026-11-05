-- TP5 : requête de référence à expérimenter dans DBeaver.
-- Les tables et colonnes correspondent à database/schema.sql du dépôt.
-- LEFT JOIN conserve les livres sans auteur.
-- GROUP_CONCAT regroupe plusieurs auteurs dans une seule ligne.
SELECT
    l.id_livre,
    l.titre,
    l.annee_publication,
    l.prix,
    GROUP_CONCAT(
        CONCAT_WS(' ', a.prenom, a.nom)
        ORDER BY a.nom, a.prenom
        SEPARATOR ', '
    ) AS auteurs
FROM livre l
LEFT JOIN ecrire e ON e.id_livre = l.id_livre
LEFT JOIN auteur a ON a.id_auteur = e.id_auteur
GROUP BY l.id_livre, l.titre, l.annee_publication, l.prix
ORDER BY l.titre;
