-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: database_portcartier
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.11-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `audience_ratings`
--

DROP TABLE IF EXISTS `audience_ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audience_ratings` (
  `rating_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`rating_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audience_ratings`
--

LOCK TABLES `audience_ratings` WRITE;
/*!40000 ALTER TABLE `audience_ratings` DISABLE KEYS */;
INSERT INTO `audience_ratings` VALUES (1,'Enfant'),(2,'Adolescent'),(3,'Adulte'),(4,'Général');
/*!40000 ALTER TABLE `audience_ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Livre'),(2,'Film'),(3,'Jeu'),(4,'Console'),(5,'CD');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `author_producer` varchar(100) NOT NULL,
  `publication_year` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `audience_rating_id` int(11) DEFAULT NULL,
  `genre_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`document_id`),
  KEY `category_id` (`category_id`),
  KEY `audience_rating_id` (`audience_rating_id`),
  KEY `genre_id` (`genre_id`),
  CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`audience_rating_id`) REFERENCES `audience_ratings` (`rating_id`),
  CONSTRAINT `documents_ibfk_3` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`genre_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
INSERT INTO `documents` VALUES (1,'Python pour les nuls','John Paul Mueller',2020,1,4,8,'Les bases de la programmation en Python, langage qui peut être utilisé seul ou couplé : syntaxe, conception des programmes, blocs de données, chaînes et dictionnaires ou encore programmation procédurale et orientée objet.','9782412053140','../img/python.jpg'),(2,'Nevermind','Nirvana',1991,5,4,9,'Nevermind est le deuxième album studio du groupe américain de grunge Nirvana. ',NULL,'../img/nirvananevermind.jpg'),(3,'Maman j\'ai raté l\'avion !','Chris Columbus',1992,2,4,1,'Maman, j\'ai raté l\'avion ! (Home Alone) est un film de Noël comique et familiale. Le jeune Kevin McCallister (8 ans) qui se retrouve seul chez lui, alors que toute sa famille est partie en vacances à Paris en l\'oubliant par mégarde, et que des voleurs tentent de cambrioler la maison.',NULL,'../img/mamanjairatelavion.jpg'),(4,'Les Aventures de Sherlock Holmes','Arthur Conan Doyle',1892,1,4,11,'Les Aventures de Sherlock Holmes est un recueil de nouvelles policières écrit par Sir Arthur Conan Doyle et mettant en scène son célèbre détective privé.','9780192823786','../img/sherlockholmes.jpg'),(5,'Monopoly','Hasbro Gaming',2022,3,4,10,'Jeu de table qui consiste à ruiner ses adversaires par des opérations immobilières. Il symbolise les aspects apparents et spectaculaires du capitalisme, les fortunes se faisant et se défaisant au fil des coups de dés.',NULL,'../img/monopoly.jpg'),(6,'Programmer en JAVA','Claude Delannoy',2020,1,4,8,'L\'apprentissage du langage se fait en quatre étapes : apprentissage de la syntaxe de base, maîtrise de la programmation en objet Java, initiation à la programmation graphique et événementielle avec la bibliothèque Swing, introduction au développement web avec les servlets Java et les JSP.','9782212801651','../img/java.jpg'),(7,'Pirates des Caraïbes : La Malédiction du Black Pearl','Jerry Bruckheimer et Walt Disney Pictures',2003,2,4,7,'C\'est le premier volet de la franchise Pirates des Caraïbes',NULL,'../img/piratesdescaraibes1.jpg'),(8,'Wii Sport','Nintendo',2006,4,4,10,'Le jeu est un ensemble de cinq simulations de sport conçues pour initier les nouveaux joueurs à la télécommande Wii.',NULL,'../img/wiisport.jpg'),(9,'Adibou','Nintendo DS',2009,4,1,8,'Adibou 6-7 ans :joue à lire et à compter',NULL,'../img/adibou.jpg'),(10,'Various Positions','Leonard Cohen',1984,5,4,9,'Album d\'un grand auteur-compositeur-interprète, musicien, poète, romancier et peintre canadien du Québec',NULL,'../img/variouspositions.jpg'),(11,'Oops!... I Did It Again','Britney Spears',2000,5,4,9,'Deuxième Album de la princesse de la pop Britney Spears',NULL,'../img/britneyspears.jpg'),(12,'Le Trône de fer','George R. R. Martin',1996,1,3,7,'Le Trône de fer (A Song of Ice and Fire) est une série de romans de fantasy de George R. R. Martin, dont l\'écriture et la parution sont en cours.','9782756422435','../img/got1.jpg'),(13,'Caillou - Mon grand livre d\'aventures','Marion Johnson',2009,1,1,10,'Les enfants retrouvent dans cette collection les aventures de Caillou qu\'ils ont pu voir à la télévision. Caillou, maintenant âgé de quatre ans, poursuit sa découverte de la vie en s\'ouvrant plus au monde extérieur.','2894507127','../img/caillou.jpg'),(14,'Twister','MB Jeux',1966,3,4,10,'Ce jeu Twister est le jeu de contorsions amusantes avec une petite variante ! ',NULL,'../img/twister.JPG'),(15,'Le Cercle 2','Hideo Nakata',2005,2,3,3,'Six mois après les horribles événements qui leur avaient fait fuir Seattle, Rachel Keller et son jeune fils, Aidan, se sont réfugiés à Astoria, dans l\'Oregon. La journaliste espère oublier ses épreuves dans cette paisible bourgade côtière, mais de nouvelles menaces ne tardent pas à planer sur sa vie.',NULL,'../img/cercle2.jpg'),(16,'Piano Man','Billy Joel',1973,5,4,9,'Piano Man est le second album de Billy Joel, sorti le 9 novembre 1973 chez Columbia Records.',NULL,'../img/pianoman.jpg'),(17,'Barbie','Greta Gerwig',2023,2,4,1,'A Barbie Land, vous êtes un être parfait dans un monde parfait. Sauf si vous êtes en crise existentielle, ou si vous êtes Ken.',NULL,'../img/barbie.jpg'),(18,'Dune 1','Frank Herbert',2021,1,4,4,'Dune . Il n\'y a pas, dans tout l\'Empire, de planète plus inhospitalière que Dune. Partout, des sables à perte de vue. Une seule richesse : l\'épice de longue vie, née du désert, et que tout l\'univers convoite.. Quand Leto Atréides reçoit Dune en fief, il flaire le piège. Il aura besoin des guerriers Fremen qui, réfugiés au fond du désert, se sont adaptés à une vie très dure en préservant leur liberté, leurs coutumes et leur foi. Ils rêvent du prophète qui proclamera la guerre sainte et changera le cours de l\'Histoire. Cependant, les Révérendes Mères du Bene Gesserit poursuivent leur programme millénaire de sélection génétique : elles veulent créer un homme qui réunira tous les dons latents de l\'espèce. Le Messie des Fremen est-il déjà né dans l\'Empire?','9782266320481','../img/dune.jpg'),(19,'Où est Charlie ?','Martin Handford',2021,1,1,10,'Dans cette édition collector, voici intégralement regroupées les sept aventures de Charlie, le célèbre globe-trotteur qui se cache aux quatre coins de la planète : Où est Charlie ?, Le Voyage fantastique, Charlie remonte le temps, À Hollywood, Le Livre magique, La Grande Expo, Le Carnet Secret. Son format poche est idéal pour l\'emmener partout !\r\nTout Charlie dans une édition à mettre dans toutes les poches !','2324028239','../img/ouestcharlie.jpg');
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`employee_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'Doe','John','123 Boulevard Inconnu','Port-Cartier','QC','418-555-9012','admin@mail.com','mdp123',1),(2,'Doe','Jane','321 Rue Imposteurs','Port-Cartier','QC','418-555-3456','employe@mail.com','securepwd789',0),(3,'Kouider','Karim','3, place Laval, bureau 400','Laval','QC','800-251-6621','karim.kouider@collegecdi.ca','420P12ID',1),(4,'Roy','Martin','1259, rue Berri, 3e étage','Montréal','QC','514-849-4757','martin.roy@collegecdi.ca','420P12ID',0);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `genres`
--

DROP TABLE IF EXISTS `genres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `genres` (
  `genre_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`genre_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `genres`
--

LOCK TABLES `genres` WRITE;
/*!40000 ALTER TABLE `genres` DISABLE KEYS */;
INSERT INTO `genres` VALUES (1,'Comédie'),(2,'Drame'),(3,'Horreur'),(4,'Science-fiction'),(5,'Documentaire'),(6,'Romance'),(7,'Fantasy'),(8,'Éducation'),(9,'Musique'),(10,'Divertissement'),(11,'Policier');
/*!40000 ALTER TABLE `genres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `reserved_quantity` int(11) DEFAULT 0,
  `available_quantity` int(11) DEFAULT NULL,
  PRIMARY KEY (`inventory_id`),
  KEY `document_id` (`document_id`),
  CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
INSERT INTO `inventory` VALUES (1,1,2,0,2),(2,2,4,0,3),(3,3,1,0,1),(4,4,3,0,3),(5,5,2,0,2),(6,6,1,1,0),(7,7,4,1,3),(8,8,2,0,2),(9,9,2,0,2),(10,10,1,0,1),(11,11,2,0,2),(12,12,2,0,2),(13,13,1,0,1),(14,14,4,0,4),(15,15,4,0,3),(16,16,3,0,3),(17,17,1,0,1),(18,18,4,0,4),(19,19,2,0,2);
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loans`
--

DROP TABLE IF EXISTS `loans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loans` (
  `loan_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `loan_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  PRIMARY KEY (`loan_id`),
  KEY `member_id` (`member_id`),
  KEY `document_id` (`document_id`),
  CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`),
  CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loans`
--

LOCK TABLES `loans` WRITE;
/*!40000 ALTER TABLE `loans` DISABLE KEYS */;
INSERT INTO `loans` VALUES (1,2,2,'2024-07-08','2024-07-15','2024-07-17',1),(2,7,5,'2024-07-09','2024-07-16','2024-07-15',2),(3,2,2,'2024-07-06','2024-07-13',NULL,1),(4,4,7,'2024-07-02','2024-07-09',NULL,1),(5,2,15,'2024-07-06','2024-07-13',NULL,1);
/*!40000 ALTER TABLE `loans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `members` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`member_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES (1,'Paul','Jean','1098 Rue Principale','Port-Cartier','QC','514-555-1234','popaul@gmail.com','aaaa1111'),(2,'Perron','Julie','456 Rue Secondaire','Port-Cartier','QC','418-555-5678','julieperron29@hotmail.com','abcd1234'),(3,'Lavoie','Béatrice','29 rue des Colibri','Terrebonne','QC','819-444-1234','bblavoie@yahoo.ca','papillon6'),(4,'Hébert','Mathieu','111 boulevard des Chemises','Saint-Calixte','QC','450-888-4545','barbebleu@hotmail.fr','ab12cd34'),(5,'Papineau','Gilles','54 rue Lavoie','Blainville','QC','514-989-9898','gillespapineau@hotmail.com','papipapineau11'),(6,'Allard','Juliette','858 ch. des Anglais','Mascouche','QC','438-111-1245','cocotte26874@gmail.com','cocottebiblio26874'),(7,'Michaud','Maurice','911 rue des membres','Membrevillle','QC','451-451-4545','member@mail.com','121212');
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `reservation_date` date NOT NULL,
  `in_progress` tinyint(1) DEFAULT 1,
  `reserved_quantity` int(11) DEFAULT NULL,
  PRIMARY KEY (`reservation_id`),
  KEY `member_id` (`member_id`),
  KEY `document_id` (`document_id`),
  CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`),
  CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (1,1,1,'2024-07-05',0,1),(2,2,2,'2024-07-05',0,1),(3,3,7,'2024-07-06',1,1),(4,4,6,'2024-07-06',1,1),(5,7,5,'2024-07-10',0,2);
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-07-17 19:28:47
