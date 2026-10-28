# Progression des TP — BTS SIO SLAM

Ce document permet de retrouver les **consignes des travaux pratiques** et les **versions successives du code de référence** de l'application PHP.

Le fichier `README.md` présente le projet lui-même. Le présent document est consacré au suivi pédagogique des TP.

## Où trouver les consignes ?

Les documents distribués en cours sont regroupés dans le répertoire **[TP_consigne](./TP_consigne/)**.

Chaque étudiant applique les consignes à **son propre projet**. Le projet de médiathèque de ce dépôt constitue un exemple de référence.

## Correspondance entre TP et versions du code

| Version | Étape | Notions abordées | Code de référence |
|---|---|---|---|
| `v0.1.0` | TP 1 — Initialisation | Première structure du projet et démarrage du code PHP | [Voir le code](https://github.com/RedCoal38/projet-bts/tree/v0.1.0) |
| `v0.1.1` | TP 1 — Premières pages | Pages `index.php` et `listLivres.php`, en-tête et pied de page réutilisables, premières données de catalogue et feuille CSS | [Voir le code](https://github.com/RedCoal38/projet-bts/tree/v0.1.1) |
| `v0.2.0` | TP 2 | Fonctions PHP, recherche par identifiant et fiche détaillée | [Voir le code](https://github.com/RedCoal38/projet-bts/tree/v0.2.0) |
| `v0.3.1` | TP 3 — Partie 1 | Création d'une classe `Livre`, objets et propriétés publiques | [Voir le code](https://github.com/RedCoal38/projet-bts/tree/v0.3.1) |
| `v0.3.2` | TP 3 — Partie 2 | Encapsulation, propriétés privées, getters, setters et constructeur | [Voir le code](https://github.com/RedCoal38/projet-bts/tree/v0.3.2) |
| `v0.3.3` | TP 3 — Partie 3 | Intégration des objets dans le projet existant | [Voir le code](https://github.com/RedCoal38/projet-bts/tree/v0.3.3) |

> Les versions suivantes seront ajoutées à mesure de l’avancement des TP. Les tags `v0.1.0` et `v0.1.1` correspondent à deux états successifs du TP 1 : initialisation, puis réalisation des premières pages. Ces tags ont été vérifiés dans le dépôt GitHub.

## Comment consulter une version ?

**Depuis GitHub :** cliquer sur « Voir le code » dans le tableau pour afficher les fichiers à cette étape. Il est également possible de consulter la page des [tags](https://github.com/RedCoal38/projet-bts/tags) ou celle des [Releases](https://github.com/RedCoal38/projet-bts/releases) lorsqu'elles sont publiées.

**Depuis un dépôt Git local :** après avoir enregistré son travail, on peut consulter une version avec :

```bash
git fetch --tags
git switch --detach v0.3.2
```

Pour revenir à la branche de travail :

```bash
git switch main
```

**Attention :** la commande `git switch --detach` sert à examiner une ancienne version du dépôt de référence. Elle ne doit pas être utilisée à la légère dans le projet personnel d'un étudiant contenant des modifications non enregistrées.

## À retenir

- Les **consignes** expliquent ce qu'il faut réaliser : elles se trouvent dans `TP_consigne/`.
- Les **tags** repèrent le code à la fin d'une étape donnée.
- Les **Releases** peuvent fournir une présentation plus détaillée de chaque version.
- Le code de référence sert d'exemple : il ne remplace pas le travail de conception et de développement de chaque étudiant.
