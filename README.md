# Gemini AI for Magix CMS

![Release](https://img.shields.io/github/release/magix-cms/geminiai.svg)
![License](https://img.shields.io/github/license/magix-cms/geminiai.svg)
![PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-blue.svg)

Ce plugin open source intègre l'intelligence artificielle **Google Gemini** au sein de [Magix CMS v3](https://www.magix-cms.com). Il transforme votre éditeur de contenu en un assistant de rédaction intelligent capable de générer, corriger et traduire du texte en HTML pur.

## Auteurs

* **Gerits Aurelien** (gtraxx) - [aurelien@magix-cms.com](mailto:aurelien@magix-cms.com)
* Communauté Magix CMS

---

## Description

Le plugin **Gemini AI** utilise les modèles génératifs de Google (Gemini 1.5 Flash / 2.0) pour assister les administrateurs dans la gestion de leur contenu. Contrairement à une intégration standard, ce plugin force l'IA à produire un balisage HTML sémantique compatible avec TinyMCE, sans polluer vos pages avec des structures de documents complètes (`<html>`, `<body>`).

### Fonctionnalités clés :
* **Rédaction & Correction** : Création de contenu à partir de mots-clés ou correction de texte existant.
* **Traduction Intelligente** : Traduction vers le Français, Anglais, Néerlandais et Allemand en conservant les balises HTML (`<strong>`, `<ul>`, etc.).
* **Contrôle du Ton** : Adaptabilité du style (Professionnel, Marketing, Amical).
* **Nettoyage Chirurgical** : Suppression automatique des styles "inline" et des résidus de Markdown injectés par l'IA.

---

##  Installation

1. **Téléchargement** : Décompressez l'archive dans le dossier `plugins/geminiai` de votre installation Magix CMS.
2. **Installation via l'UI** :
   * Connectez-vous à l'administration.
   * Allez dans **Extensions > Plugins**.
   * Recherchez **Gemini AI** et cliquez sur **Installer**.
3. **Configuration** :
   * Saisissez votre clé API obtenue sur [Google AI Studio](https://aistudio.google.com/).
   * Activez l'option **Magix AI** dans la configuration générale de Magix CMS pour activer l'icône dans TinyMCE.

---

## Prérequis

* **Magix CMS v3**
* **PHP 7.4** ou supérieur.
* **Extension cURL** activée sur le serveur.
* Une connexion internet sortante autorisée vers `generativelanguage.googleapis.com`.

---

## Dépannage (Troubleshooting)

| Problème | Cause possible | Solution |
| :--- | :--- | :--- |
| **L'icône ne s'affiche pas** | Magix AI est désactivé | Vérifiez la configuration générale de Magix CMS. |
| **Erreur 403 / Key Invalid** | Clé API incorrecte | Générez une nouvelle clé sur Google AI Studio. |
| **Erreur 429 / Quota Exceeded** | Limite gratuite atteinte | Attendez 60 secondes ou passez à un plan payant (Pay-as-you-go). |
| **Contenu bloqué (Safety)** | Filtres de sécurité Google | Reformulez votre demande pour éviter les sujets sensibles. |

---

## Contribution

Ce projet est open source. Nous encourageons les développeurs à :
1. Forker le projet.
2. Créer une branche pour une nouvelle fonctionnalité (`git checkout -b feature/AmazingFeature`).
3. Commiter les changements (`git commit -m 'Add AmazingFeature'`).
4. Push sur la branche (`git push origin feature/AmazingFeature`).
5. Ouvrir une **Pull Request**.

---

## Licence

Distribué sous la licence MIT et GPL3. Voir `LICENSE` pour plus d'informations.