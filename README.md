# Médiathèque

## Présentation

Application web permettant de gérer le catalogue d'une médiathèque, ses adhérents et les emprunts d'exemplaires.

## Fonctionnalités principales

L'application permettra de :

- consulter le catalogue des livres ;
- gérer les adhérents ;
- enregistrer un emprunt et son retour ;
- authentifier un adhérent.

## Utilisateurs

Les adhérents pourront se connecter et consulter les informations utiles à leur utilisation de la médiathèque. La gestion des données pourra être complétée au cours du projet.

## Opération métier principale

L'opération métier principale est l'emprunt d'un exemplaire par un adhérent, avec une date d'emprunt, une date de retour prévue et éventuellement une date de retour réelle.

## Modèle de données

### Données principales

- **categorie** : classe les livres ;
- **livre** : décrit une œuvre du catalogue ;
- **auteur** : décrit un auteur ;
- **exemplaire** : représente un exemplaire physique d'un livre ;
- **adherent** : représente un utilisateur de la médiathèque ;
- **emprunt** : enregistre l'emprunt d'un exemplaire par un adhérent.

### Relations principales

- Une catégorie peut contenir plusieurs livres ; un livre appartient à une catégorie.
- Un livre peut posséder plusieurs exemplaires ; un exemplaire correspond à un livre.
- Un adhérent peut effectuer plusieurs emprunts ; un emprunt appartient à un adhérent.
- Un exemplaire peut apparaître dans plusieurs emprunts au cours du temps ; un emprunt concerne un exemplaire.

### Relation N-N

Un livre peut avoir plusieurs auteurs et un auteur peut avoir écrit plusieurs livres. La table d'association **ecrire** relie donc **livre** et **auteur**.

### Authentification

L'entité **adherent** contient notamment une adresse e-mail unique et un mot de passe. Le mot de passe sera stocké sous forme sécurisée lorsque l'authentification sera développée.

## Évolutions

Le modèle et les fonctionnalités pourront évoluer au fur et à mesure du développement de l'application.
