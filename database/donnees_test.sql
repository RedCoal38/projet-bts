-- Jeu de données de démonstration pour la médiathèque.
-- À exécuter APRÈS database/schema.sql (avec la colonne livre.prix ajoutée).
-- Script réexécutable : les enregistrements sont identifiés par leurs ISBN,
-- et les auteurs/catégories existants sont réutilisés.
USE mediatheque;
START TRANSACTION;

INSERT INTO categorie (libelle) VALUES
('Roman'), ('Science-fiction'), ('Informatique')
ON DUPLICATE KEY UPDATE libelle = VALUES(libelle);

-- Les auteurs n'ont pas de contrainte UNIQUE dans schema.sql.
-- On les insère uniquement s'ils n'existent pas déjà.
INSERT INTO auteur (nom, prenom)
SELECT 'Orwell', 'George' WHERE NOT EXISTS
(SELECT 1 FROM auteur WHERE nom = 'Orwell' AND prenom = 'George');
INSERT INTO auteur (nom, prenom)
SELECT 'Herbert', 'Frank' WHERE NOT EXISTS
(SELECT 1 FROM auteur WHERE nom = 'Herbert' AND prenom = 'Frank');
INSERT INTO auteur (nom, prenom)
SELECT 'Pratchett', 'Terry' WHERE NOT EXISTS
(SELECT 1 FROM auteur WHERE nom = 'Pratchett' AND prenom = 'Terry');
INSERT INTO auteur (nom, prenom)
SELECT 'Gaiman', 'Neil' WHERE NOT EXISTS
(SELECT 1 FROM auteur WHERE nom = 'Gaiman' AND prenom = 'Neil');

INSERT INTO livre (titre, isbn, annee_publication, prix, id_categorie)
VALUES ('1984', '9782070368228', 1949, 9.50,
        (SELECT id_categorie FROM categorie WHERE libelle = 'Roman'))
ON DUPLICATE KEY UPDATE titre=VALUES(titre), annee_publication=VALUES(annee_publication), prix=VALUES(prix);
INSERT INTO livre (titre, isbn, annee_publication, prix, id_categorie)
VALUES ('Dune', '9782266320481', 1965, 11.90,
        (SELECT id_categorie FROM categorie WHERE libelle = 'Science-fiction'))
ON DUPLICATE KEY UPDATE titre=VALUES(titre), annee_publication=VALUES(annee_publication), prix=VALUES(prix);
INSERT INTO livre (titre, isbn, annee_publication, prix, id_categorie)
VALUES ('De bons présages', '9782290354063', 1990, 10.90,
        (SELECT id_categorie FROM categorie WHERE libelle = 'Roman'))
ON DUPLICATE KEY UPDATE titre=VALUES(titre), annee_publication=VALUES(annee_publication), prix=VALUES(prix);

-- La table ecrire possède une clé primaire composée : INSERT IGNORE évite les doublons.
INSERT IGNORE INTO ecrire (id_livre, id_auteur)
SELECT l.id_livre, a.id_auteur FROM livre l JOIN auteur a
WHERE l.isbn = '9782070368228' AND a.nom = 'Orwell' AND a.prenom = 'George';
INSERT IGNORE INTO ecrire (id_livre, id_auteur)
SELECT l.id_livre, a.id_auteur FROM livre l JOIN auteur a
WHERE l.isbn = '9782266320481' AND a.nom = 'Herbert' AND a.prenom = 'Frank';
INSERT IGNORE INTO ecrire (id_livre, id_auteur)
SELECT l.id_livre, a.id_auteur FROM livre l JOIN auteur a
WHERE l.isbn = '9782290354063' AND a.nom = 'Pratchett' AND a.prenom = 'Terry';
INSERT IGNORE INTO ecrire (id_livre, id_auteur)
SELECT l.id_livre, a.id_auteur FROM livre l JOIN auteur a
WHERE l.isbn = '9782290354063' AND a.nom = 'Gaiman' AND a.prenom = 'Neil';

COMMIT;

-- Contrôle : le dernier livre doit avoir deux auteurs.
SELECT l.id_livre, l.titre, l.annee_publication, l.prix,
       GROUP_CONCAT(CONCAT_WS(' ', a.prenom, a.nom) ORDER BY a.nom SEPARATOR ', ') AS auteurs
FROM livre l
LEFT JOIN ecrire e ON e.id_livre = l.id_livre
LEFT JOIN auteur a ON a.id_auteur = e.id_auteur
WHERE l.isbn IN ('9782070368228', '9782266320481', '9782290354063')
GROUP BY l.id_livre, l.titre, l.annee_publication, l.prix
ORDER BY l.titre;
