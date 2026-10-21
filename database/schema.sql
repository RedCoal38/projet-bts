CREATE DATABASE IF NOT EXISTS mediatheque
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE mediatheque;


CREATE TABLE categorie (
    id_categorie INT UNSIGNED AUTO_INCREMENT,
    libelle VARCHAR(100) NOT NULL,

    CONSTRAINT pk_categorie
        PRIMARY KEY (id_categorie),

    CONSTRAINT uq_categorie_libelle
        UNIQUE (libelle)
);


CREATE TABLE auteur (
    id_auteur INT UNSIGNED AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100),

    CONSTRAINT pk_auteur
        PRIMARY KEY (id_auteur)
);


CREATE TABLE livre (
    id_livre INT UNSIGNED AUTO_INCREMENT,
    titre VARCHAR(255) NOT NULL,
    isbn VARCHAR(20),
    annee_publication SMALLINT UNSIGNED,
    id_categorie INT UNSIGNED NOT NULL,

    CONSTRAINT pk_livre
        PRIMARY KEY (id_livre),

    CONSTRAINT uq_livre_isbn
        UNIQUE (isbn),

    CONSTRAINT fk_livre_categorie
        FOREIGN KEY (id_categorie)
        REFERENCES categorie(id_categorie)
);


CREATE TABLE ecrire (
    id_livre INT UNSIGNED NOT NULL,
    id_auteur INT UNSIGNED NOT NULL,

    CONSTRAINT pk_ecrire
        PRIMARY KEY (id_livre, id_auteur),

    CONSTRAINT fk_ecrire_livre
        FOREIGN KEY (id_livre)
        REFERENCES livre(id_livre),

    CONSTRAINT fk_ecrire_auteur
        FOREIGN KEY (id_auteur)
        REFERENCES auteur(id_auteur)
);


CREATE TABLE exemplaire (
    id_exemplaire INT UNSIGNED AUTO_INCREMENT,
    numero_inventaire VARCHAR(50) NOT NULL,
    etat VARCHAR(50) NOT NULL,
    id_livre INT UNSIGNED NOT NULL,

    CONSTRAINT pk_exemplaire
        PRIMARY KEY (id_exemplaire),

    CONSTRAINT uq_exemplaire_numero
        UNIQUE (numero_inventaire),

    CONSTRAINT fk_exemplaire_livre
        FOREIGN KEY (id_livre)
        REFERENCES livre(id_livre)
);


CREATE TABLE adherent (
    id_adherent INT UNSIGNED AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,

    CONSTRAINT pk_adherent
        PRIMARY KEY (id_adherent),

    CONSTRAINT uq_adherent_email
        UNIQUE (email)
);


CREATE TABLE emprunt (
    id_emprunt INT UNSIGNED AUTO_INCREMENT,
    date_emprunt DATE NOT NULL,
    date_retour_prevue DATE NOT NULL,
    date_retour DATE,
    id_adherent INT UNSIGNED NOT NULL,
    id_exemplaire INT UNSIGNED NOT NULL,

    CONSTRAINT pk_emprunt
        PRIMARY KEY (id_emprunt),

    CONSTRAINT fk_emprunt_adherent
        FOREIGN KEY (id_adherent)
        REFERENCES adherent(id_adherent),

    CONSTRAINT fk_emprunt_exemplaire
        FOREIGN KEY (id_exemplaire)
        REFERENCES exemplaire(id_exemplaire)
);
