# Gemini AI for Magix CMS

[![Release](https://img.shields.io/github/release/magix-cms/geminiai.svg)](https://github.com/magix-cms/geminiai/releases/latest)
[![License](https://img.shields.io/github/license/magix-cms/geminiai.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-blue.svg)](https://php.net/)
[![Magix CMS](https://img.shields.io/badge/Magix%20CMS-4.x-success.svg)](https://www.magix-cms.com/)

**Gemini AI** est un plugin open source qui intègre l'intelligence artificielle générative de **Google Gemini** au cœur de **Magix CMS 4**. Il transforme l'éditeur de contenu natif (TinyMCE) en un véritable assistant de rédaction intelligent, capable de générer, corriger et traduire du texte tout en respectant strictement le balisage HTML de votre site.

## 👥 Auteurs

* **Gerits Aurelien** (gtraxx) - [aurelien@magix-cms.com](mailto:aurelien@magix-cms.com)
* Communauté Magix CMS

## ☕ Soutenir le projet

Si vous souhaitez soutenir le développement de ce plugin, vous pouvez faire un don via PayPal :

[![Faire un don](https://img.shields.io/badge/Donate-PayPal-blue.svg)](https://www.paypal.com/donate/?business=BQBYN3XYGMDML&no_recurring=0&currency_code=EUR)

## 🖥 Demo

<img width="1221" height="660" alt="Interface Gemini AI dans Magix CMS" src="https://github.com/user-attachments/assets/dce130dd-e938-4b81-8a05-32ab527431d4" />

---

## ✨ Fonctionnalités clés

Contrairement à une simple intégration d'API, ce plugin a été conçu pour le webmastering : il force l'IA à produire un balisage HTML sémantique propre, sans polluer vos pages avec des structures de documents complètes (`<html>`, `<body>`) ou des styles non désirés.

* **Rédaction & Correction :** Création de contenu à partir de mots-clés, reformulation ou correction orthographique d'un texte existant.
* **Traduction Intelligente :** Traduction instantanée vers le Français, Anglais, Néerlandais et Allemand en préservant parfaitement la structure des balises HTML (`<strong>`, `<h2>`, `<ul>`, etc.).
* **Contrôle du Ton :** Adaptabilité du style rédactionnel (Professionnel, Marketing, Amical) selon votre audience.
* **Nettoyage Chirurgical :** Suppression automatique des styles "inline", des commentaires et des résidus de Markdown souvent injectés par les IA.
* **Interface Sécurisée :** Un pont AJAX natif protège votre clé API côté serveur (Backend) sans l'exposer dans le code source de l'éditeur.

---

## 🚀 Installation & Configuration

1. Téléchargez et décompressez l'archive du plugin.
2. Placez le dossier `GeminiAI` dans le répertoire `plugins/` de votre installation Magix CMS.
3. Connectez-vous à l'administration de votre site.
4. Rendez-vous dans **Extensions** > **Gestionnaire**.
5. Cliquez sur le bouton d'installation automatique pour **Gemini AI**.
6. Accédez à la configuration du plugin et saisissez votre clé API obtenue sur [Google AI Studio](https://aistudio.google.com/). Un badge dynamique vous confirmera instantanément si la connexion est établie.

Une fois connecté, l'icône de l'assistant IA s'activera automatiquement dans la barre d'outils de votre éditeur TinyMCE.

---

## ⚙️ Prérequis

* **Magix CMS 4.x**
* **PHP 8.2** ou supérieur
* L'extension **cURL** activée sur votre serveur
* Une connexion internet sortante autorisée vers `generativelanguage.googleapis.com`

---

## 🛠 Dépannage (Troubleshooting)

| Problème | Cause possible | Solution |
| :--- | :--- | :--- |
| **Badge "Erreur de liaison"** | Extension cURL manquante ou SSL bloqué | Vérifiez que PHP cURL est actif et que votre pare-feu autorise les requêtes sortantes. |
| **Erreur 403 / Key Invalid** | Clé API incorrecte | Générez une nouvelle clé sur Google AI Studio et vérifiez les espaces. |
| **Erreur 429 / Quota Exceeded** | Limite gratuite atteinte | Attendez quelques minutes ou passez à un plan "Pay-as-you-go" chez Google. |
| **Contenu bloqué (Safety)** | Filtres de sécurité Google | Reformulez votre demande (prompt) pour éviter les sujets bloqués par l'IA. |

---

## 🤝 Contribution

Ce projet est open source. Nous encourageons les développeurs à l'améliorer :
1. Forker le projet.
2. Créer une branche pour une nouvelle fonctionnalité (`git checkout -b feature/AmazingFeature`).
3. Commiter les changements (`git commit -m 'Add AmazingFeature'`).
4. Push sur la branche (`git push origin feature/AmazingFeature`).
5. Ouvrir une **Pull Request**.

---

## 📄 Licence

Ce projet est sous licence **GPLv3**. Voir le fichier [LICENSE](LICENSE) pour plus de détails.
Copyright (C) 2008 - 2026 Gerits Aurelien (Magix CMS)
Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier selon les termes de la Licence Publique Générale GNU telle que publiée par la Free Software Foundation ; soit la version 3 de la Licence, ou (à votre discrétion) toute version ultérieure.