# Vestige d’Or – Maison d’enchères en ligne

Je présente ici **Vestige d’Or**, une maison d’enchères en ligne développée avec Symfony.
L’objectif du projet est de proposer une expérience d’enchères élégante, professionnelle et claire, en mettant l’accent sur :

- une **interface sombre** et sobre, inspirée des maisons d’enchères haut de gamme,
- un parcours utilisateur fluide (inscription, enchères, favoris, factures),
- une base technique propre et extensible.

---

## 1. Objectifs du projet

Avec ce projet, je voulais :

- Concevoir une **plateforme d’enchères** moderne, adaptée aux montres, objets anciens, œuvres d’art, etc.
- Mettre en place un **vrai espace utilisateur** :
  - profil,
  - enchères en cours / remportées,
  - favoris,
  - factures.
- Respecter les contraintes **légales (Belgique)** :
  - Mentions légales,
  - Conditions générales de vente,
  - Politique de confidentialité (RGPD).
- Soigner le **design** : thème sombre, composants épurés, typographie soignée.

---

## 2. Stack technique

- **Back-end** : PHP 8 / **Symfony 6**
- **Base de données** : MySQL (Doctrine ORM)
- **Templates** : Twig
- **Front** :
  - Bootstrap 5,
  - CSS personnalisée (`public/css/app.css`), thème sombre.
- **Outils** :
  - Fixtures pour les données de développement,
  - Interface d’admin personnalisée (sans EasyAdmin).

---

## 3. Fonctionnalités principales

### Côté visiteur / utilisateur

- **Accueil**
  - Hero image sombre,
  - Bloc “Comment ça marche ?” en 3 étapes,
  - Mise en avant des ventes.

- **Catalogue des lots**
  - Liste des lots avec cartes épurées (`.lot-card`),
  - Vignettes, titre, catégorie, prix actuel.

- **Détail d’un lot**
  - Grande image + description,
  - Prix de départ, prix actuel, incrément minimum,
  - Indication de l’état de l’enchère (ouverte, à venir, terminée),
  - Historique des enchères.

- **Compte utilisateur**
  - Inscription / connexion,
  - Page **Profil** avec :
    - informations personnelles,
    - adresse,
    - actions (mes enchères, factures, favoris…),
  - Formulaire d’édition du profil.

- **Mes enchères**
  - Liste des enchères de l’utilisateur,
  - Indication des lots remportés,
  - Lien vers **Mes factures** pour les lots gagnés.

- **Mes factures**
  - Historique des commandes / factures,
  - Statut des paiements.

- **Mes favoris**
  - Liste des lots ajoutés en favoris,
  - Affichage de la **photo du lot**,
  - Indication claire si le lot n’est plus disponible ou si l’enchère est terminée.

- **Pages légales**
  - Mentions légales personnalisées pour **Vestige d’Or SRL (Belgique)**,
  - CGV,
  - Politique de confidentialité.

### Côté administration

- **Tableau de bord admin**
  - Vue d’ensemble :
    - commandes en attente,
    - enchères en cours / à venir,
    - lots vendus.
  - Dernières commandes,
  - Utilisateurs récents,
  - Enchères qui se terminent bientôt.

- **Gestion des lots**
  - Création / édition de lots :
    - titre, description, catégorie,
    - prix de départ, incrément minimum,
    - fin des enchères,
    - image,
    - événement d’enchère lié.
  - Champs prévus pour :
    - **prix d’achat immédiat** (optionnel),
    - **estimation min/max** (optionnels).

- **Gestion des commandes et utilisateurs**
  - Liste des commandes,
  - Détail d’une commande,
  - Liste des utilisateurs (via admin dédié).

---

## 4. Installation et lancement

### Prérequis

- PHP 8.x
- Composer
- MySQL
- Node/npm (si besoin pour assets)
- Symfony CLI (optionnel mais pratique)

### Étapes

1. Cloner le projet :

```bash
git clone <url-du-depot>
cd ProjetFinal-Karima
```

2. Installer les dépendances PHP :

```bash
composer install
```

3. Configurer l’environnement :

- Copier `.env` en `.env.local`
- Adapter au besoin :

```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/projet_karima"
```

4. Créer la base de données + migrations :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. (Optionnel) Charger des données de test :

```bash
php bin/console doctrine:fixtures:load
```

6. Lancer le serveur Symfony :

```bash
symfony serve
```

Le site sera disponible sur `http://127.0.0.1:8000`.

---

## 5. Parcours de démonstration

Pour présenter le projet, je peux suivre ce scénario :

1. **Accueil** : montrer le hero sombre + “Comment ça marche ?”.
2. **Catalogue de lots** : naviguer dans les catégories, afficher quelques lots.
3. **Détail d’un lot** : montrer les fonctionnalités d’enchère + historique.
4. **Inscription / Connexion** : créer un compte de test.
5. **Profil** : afficher les informations utilisateur + boutons d’accès.
6. **Mes enchères / Mes factures / Favoris** :
   - montrer un lot favori,
   - montrer un lot remporté et la facture correspondante.
7. **Admin** :
   - accéder au tableau de bord,
   - créer / éditer un lot,
   - montrer les indicateurs (commandes, enchères, utilisateurs).

---

## 6. Évolutions possibles

Pour la suite, je prévois (ou j’ai préparé le terrain pour) :

- Activer l’**achat immédiat** sur certains lots, en parallèle des enchères.
- Exploiter l’**estimation min/max** pour gérer des prix de réserve et des lots “invendus / remis en vente”.
- Renforcer encore la partie **paiement / facturation** (intégration d’un PSP, gestion PDF, etc.).
- Internationalisation (FR/NL/EN) si besoin.

