Site Web de la Bibliothèque Municipale de Port-Cartier (Projet Intégration 2 420-P12-ID 2024-07-17)

Introduction

Ce projet est une mise à jour du site web de la bibliothèque municipale de Port-Cartier. Il vise à offrir une version moderne du site en utilisant des technologies web de base telles qu'HTML, CSS, et JavaScript, avec une option de gestion back-end en PHP et MySQL ou en Node.js avec MongoDB. Le site permet la gestion des membres, des employés, des documents, des prêts et des réservations, tout en offrant des fonctionnalités de recherche et de gestion adaptées aux besoins des utilisateurs.

Fonctionnalités

- Gestion des Membres : Création, modification et gestion des membres de la bibliothèque.
- Gestion des Employés : Création, modification et gestion des employés.
- Gestion des Documents : Ajout, modification et gestion des documents (livres, films, jeux, etc.).
- Gestion des Prêts : Enregistrement et suivi des prêts de documents, avec gestion des retours et des retards.
- Gestion des Réservations : Réservation de documents disponibles ou prêtés, avec suivi des réservations actives.
- Recherche : Recherche de documents par mots-clés, catégorie, type ou genre.
- Rapports pour Employés : Affichage de listes telles que les membres, les retards, les documents réservés, et les documents prêtés.
- Interface Réactive : Le site est conçu pour être accessible et fonctionnel sur divers appareils, y compris les ordinateurs et les appareils mobiles.

Installation

1. Clone le Référentiel
   git clone https://github.com/ton-nom-utilisateur/Bibliotheque.git

2. Installe XAMPP
   Télécharge et installe XAMPP depuis apachefriends.org. Assure-toi que les services Apache et MySQL sont en cours d'exécution.

3. Configure la Base de Données
   - Copie le dossier du projet dans le répertoire htdocs de XAMPP (D:\xampp\htdocs\PortCartier).
   - Ouvre phpMyAdmin via http://localhost/phpmyadmin.
   - Crée une nouvelle base de données (nommée portcartier par exemple).
   - Importer le fichier de sauvegarde de la base de données fourni (database_backup.sql).

4. Configuration du Site
   - Modifie les paramètres de connexion à la base de données dans le fichier de configuration PHP (config.php).
   - Assure-toi que les chemins d'accès aux images et autres fichiers statiques sont corrects.

Utilisation

1. Accède au Site
   Ouvre ton navigateur et accède à http://localhost/PortCartier.

2. Connexion
   - Utilise les identifiants fournis dans le fichier identifiants.txt pour te connecter en tant que membre, employé ou administrateur.

3. Fonctionnalités
   - Pour les Membres : Réserve des documents, consulte tes prêts actifs, et effectue des recherches dans la bibliothèque.
   - Pour les Employés : Effectue des prêts, retours, annulations, et consulte les rapports.
   - Pour les Administrateurs : Gère les membres et les employés, en plus de toutes les fonctionnalités des employés.
