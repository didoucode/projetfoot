-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 19 mars 2025 à 22:22
-- Version du serveur : 8.0.31
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `site_foot`
--

-- --------------------------------------------------------

--
-- Structure de la table `abonnements_joueurs`
--

DROP TABLE IF EXISTS `abonnements_joueurs`;
CREATE TABLE IF NOT EXISTS `abonnements_joueurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `joueur_id` int DEFAULT NULL,
  `date_abonnement` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `joueur_id` (`joueur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `classement`
--

DROP TABLE IF EXISTS `classement`;
CREATE TABLE IF NOT EXISTS `classement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipe_id` int NOT NULL,
  `points` int DEFAULT '0',
  `matchs_joues` int DEFAULT '0',
  `victoires` int DEFAULT '0',
  `nuls` int DEFAULT '0',
  `defaites` int DEFAULT '0',
  `buts_marques` int DEFAULT '0',
  `buts_encaisses` int DEFAULT '0',
  `difference_buts` int DEFAULT '0',
  `recentes` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `equipe_id` (`equipe_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `classement`
--

INSERT INTO `classement` (`id`, `equipe_id`, `points`, `matchs_joues`, `victoires`, `nuls`, `defaites`, `buts_marques`, `buts_encaisses`, `difference_buts`, `recentes`) VALUES
(1, 2, 60, 25, 18, 6, 1, 50, 15, 35, 'V,V,V,V,V'),
(2, 1, 58, 25, 18, 4, 3, 45, 18, 27, 'L,N,L,V,V'),
(3, 3, 50, 25, 15, 5, 5, 40, 20, 20, 'V,V,V,N,L'),
(4, 4, 45, 25, 13, 6, 6, 35, 22, 13, 'L,V,N,N,V'),
(5, 5, 40, 25, 12, 4, 9, 30, 25, 5, 'V,N,L,N,L'),
(6, 6, 38, 25, 11, 5, 9, 28, 26, 2, 'L,V,N,N,V'),
(7, 7, 35, 25, 10, 5, 10, 25, 27, -2, 'L,N,L,V,V'),
(8, 8, 30, 25, 8, 6, 11, 22, 30, -8, 'L,N,L,V,V'),
(9, 9, 25, 25, 6, 7, 12, 18, 35, -17, 'N,N,N,N,L');

-- --------------------------------------------------------

--
-- Structure de la table `equipe`
--

DROP TABLE IF EXISTS `equipe`;
CREATE TABLE IF NOT EXISTS `equipe` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `ville` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `entraineur` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_creation` date DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `equipe`
--

INSERT INTO `equipe` (`id`, `nom`, `ville`, `entraineur`, `date_creation`, `logo`) VALUES
(1, 'Wydad AC', NULL, NULL, NULL, 'wac.jpg'),
(2, 'Raja CA', NULL, NULL, NULL, 'raja.jpg'),
(3, 'AS FAR', NULL, NULL, NULL, 'faar.jpg'),
(4, 'RS Berkane', NULL, NULL, NULL, 'rsb.jpg'),
(5, 'FUS Rabat', NULL, NULL, NULL, 'fus.jpg'),
(6, 'Maghreb Fès', NULL, NULL, NULL, 'mas.jpg'),
(7, 'Moghreb Tétouan', NULL, NULL, NULL, 'mat.jpg'),
(8, 'Olympic Safi', NULL, NULL, NULL, 'ocsf.jpg'),
(9, 'Difaâ El Jadida', NULL, NULL, NULL, 'dhj.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `informations`
--

DROP TABLE IF EXISTS `informations`;
CREATE TABLE IF NOT EXISTS `informations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipe_id` int DEFAULT NULL,
  `information` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`),
  KEY `equipe_id` (`equipe_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `informations`
--

INSERT INTO `informations` (`id`, `equipe_id`, `information`) VALUES
(1, 1, 'Wydad AC est l’un des clubs les plus titrés du Maroc. Il a remporté plusieurs fois la Ligue des champions de la CAF.'),
(2, 2, 'Raja CA est connu pour sa grande base de supporters et ses performances exceptionnelles dans les compétitions africaines.\nLe match précédent de Raja Club Athletic était contre Moghreb Atlético Tetuán dans Botola Pro, le match s\'est terminé par un match nul (1 - 1).'),
(3, 3, 'AS FAR est basé à Rabat et est l’un des clubs les plus anciens du Maroc, fondé en 1956.'),
(4, 4, 'RS Berkane, situé à Berkane, a remporté plusieurs titres nationaux et a fait forte impression en compétitions continentales.'),
(5, 5, 'FUS Rabat est un club de football réputé pour ses jeunes talents et ses succès sur la scène nationale.'),
(6, 6, 'Maghreb Fès est un club historique basé à Fès, avec un grand nombre de fans passionnés.'),
(7, 7, 'Moghreb Tétouan a remporté la Ligue professionnelle en 2012, un exploit marquant pour ce club du nord du Maroc.'),
(8, 8, 'Olympic Safi, basé à Safi, est un club qui a marqué l’histoire du football marocain avec plusieurs trophées nationaux.'),
(9, 9, 'Difaâ El Jadida est un club bien respecté du championnat marocain, avec une riche histoire et une base de supporters dévoués.');

-- --------------------------------------------------------

--
-- Structure de la table `joueurs`
--

DROP TABLE IF EXISTS `joueurs`;
CREATE TABLE IF NOT EXISTS `joueurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `age` int DEFAULT NULL,
  `equipes` int DEFAULT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `origine` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nationalite` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `clubs` text COLLATE utf8mb4_general_ci,
  `goals` int DEFAULT NULL,
  `moyenne_note` decimal(3,1) NOT NULL DEFAULT '1.0',
  PRIMARY KEY (`id`),
  KEY `equipe_id` (`equipes`)
) ;

--
-- Déchargement des données de la table `joueurs`
--

INSERT INTO `joueurs` (`id`, `nom`, `role`, `age`, `equipes`, `prenom`, `origine`, `nationalite`, `date_naissance`, `clubs`, `goals`, `moyenne_note`) VALUES
(11, 'El Kaabi', 'FW', 30, 1, 'Ayoub', 'Maroc', 'Maroc', '1993-06-25', 'Wydad AC', 50, '9.0'),
(12, 'El Karti', 'CM', 29, 1, 'Walid', 'Maroc', 'Maroc', '1994-07-23', 'Wydad AC', 20, '9.0'),
(13, 'Zniti', 'Gardien', 33, NULL, 'Anas', 'Maroc', 'Maroc', '1991-10-28', 'Raja CA', 0, '6.2'),
(14, 'Rahimi', 'FW', 27, 2, 'Soufiane', 'Maroc', 'Maroc', '1997-06-02', 'Raja CA', 35, '9.4'),
(15, 'Msuva', 'CM', 31, 3, 'Simon', 'Tanzanie', 'Tanzanie', '1993-10-02', 'FUS Rabat', 18, '7.3'),
(16, 'Benoun', 'DC', 30, 2, 'Badr', 'Maroc', 'Maroc', '1993-09-30', 'Raja CA', 10, '7.8'),
(17, 'Nekkach', 'CM', 38, 1, 'Ibrahim', 'Maroc', 'Maroc', '1985-06-08', 'Wydad AC', 8, '6.1'),
(18, 'Haddad', 'FW', 32, 3, 'Ismail', 'Maroc', 'Maroc', '1992-06-15', 'FUS Rabat', 27, '4.3'),
(19, 'El Bahri', 'CM', 35, 4, 'Mohammed', 'Maroc', 'Maroc', '1989-01-12', 'RS Berkane', 15, '6.3'),
(20, 'Jebor', 'FW', 32, 1, 'William', 'Liberia', 'Liberia', '1991-11-10', 'Wydad AC', 45, '8.6');

-- --------------------------------------------------------

--
-- Structure de la table `matchs`
--

DROP TABLE IF EXISTS `matchs`;
CREATE TABLE IF NOT EXISTS `matchs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipe1_id` int DEFAULT NULL,
  `equipe2_id` int DEFAULT NULL,
  `tournoi_id` int DEFAULT NULL,
  `score_equipe1` int DEFAULT NULL,
  `score_equipe2` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `date_planification` date DEFAULT NULL,
  `heure_planification` time DEFAULT NULL,
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL,
  `journee` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `equipe1_id` (`equipe1_id`),
  KEY `equipe2_id` (`equipe2_id`),
  KEY `tournoi_id` (`tournoi_id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `matchs`
--

INSERT INTO `matchs` (`id`, `equipe1_id`, `equipe2_id`, `tournoi_id`, `score_equipe1`, `score_equipe2`, `date`, `date_planification`, `heure_planification`, `heure_debut`, `heure_fin`, `journee`) VALUES
(49, 2, 1, 1, 2, 1, '2025-03-10', NULL, NULL, NULL, NULL, 1),
(50, 3, 4, 1, 1, 2, '2025-03-10', NULL, NULL, NULL, NULL, 1),
(51, 5, 6, 1, 0, 1, '2025-03-10', NULL, NULL, NULL, NULL, 1),
(52, 7, 8, 1, 1, 1, '2025-03-10', NULL, NULL, NULL, NULL, 1),
(53, 9, 1, 1, 2, 3, '2025-03-10', NULL, NULL, NULL, NULL, 1),
(54, 2, 3, 1, 3, 0, '2025-03-12', NULL, NULL, NULL, NULL, 2),
(55, 4, 5, 1, 2, 2, '2025-03-12', NULL, NULL, NULL, NULL, 2),
(56, 6, 7, 1, 1, 0, '2025-03-12', NULL, NULL, NULL, NULL, 2),
(57, 8, 9, 1, 0, 1, '2025-03-12', NULL, NULL, NULL, NULL, 2),
(58, 1, 5, 1, 1, 3, '2025-03-12', NULL, NULL, NULL, NULL, 2),
(59, 2, 4, 1, 1, 0, '2025-03-14', NULL, NULL, NULL, NULL, 3),
(60, 3, 5, 1, 2, 1, '2025-03-14', NULL, NULL, NULL, NULL, 3),
(61, 6, 9, 1, 0, 2, '2025-03-14', NULL, NULL, NULL, NULL, 3),
(62, 7, 1, 1, 2, 2, '2025-03-14', NULL, NULL, NULL, NULL, 3),
(63, 8, 3, 1, 1, 1, '2025-03-14', NULL, NULL, NULL, NULL, 3),
(64, 2, 5, 1, 3, 1, '2025-03-16', NULL, NULL, NULL, NULL, 4),
(65, 4, 7, 1, 0, 1, '2025-03-16', NULL, NULL, NULL, NULL, 4),
(66, 6, 8, 1, 1, 3, '2025-03-16', NULL, NULL, NULL, NULL, 4),
(67, 9, 3, 1, 2, 1, '2025-03-16', NULL, NULL, NULL, NULL, 4),
(68, 1, 8, 1, 2, 2, '2025-03-16', NULL, NULL, NULL, NULL, 4),
(69, 2, 6, 1, 4, 2, '2025-03-18', NULL, NULL, NULL, NULL, 5),
(70, 3, 8, 1, 2, 1, '2025-03-18', NULL, NULL, NULL, NULL, 5),
(71, 5, 9, 1, 1, 1, '2025-03-18', NULL, NULL, NULL, NULL, 5),
(72, 7, 2, 1, 0, 2, '2025-03-18', NULL, NULL, NULL, NULL, 5),
(73, 1, 4, 1, 1, 3, '2025-03-18', NULL, NULL, NULL, NULL, 5);

-- --------------------------------------------------------

--
-- Structure de la table `nextmatch`
--

DROP TABLE IF EXISTS `nextmatch`;
CREATE TABLE IF NOT EXISTS `nextmatch` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipe1_id` int DEFAULT NULL,
  `equipe2_id` int DEFAULT NULL,
  `date_match` datetime DEFAULT NULL,
  `journee` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `equipe1_id` (`equipe1_id`),
  KEY `equipe2_id` (`equipe2_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `nextmatch`
--

INSERT INTO `nextmatch` (`id`, `equipe1_id`, `equipe2_id`, `date_match`, `journee`) VALUES
(10, 1, 3, '2025-04-01 20:00:00', 1),
(11, 2, 4, '2025-04-02 20:00:00', 1),
(12, 5, 6, '2025-04-03 20:00:00', 1),
(13, 7, 9, '2025-04-04 20:00:00', 1),
(14, 3, 5, '2025-04-05 20:00:00', 2),
(15, 6, 2, '2025-04-06 20:00:00', 2),
(16, 9, 1, '2025-04-07 20:00:00', 2),
(17, 4, 7, '2025-04-08 20:00:00', 2);

-- --------------------------------------------------------

--
-- Structure de la table `staff`
--

DROP TABLE IF EXISTS `staff`;
CREATE TABLE IF NOT EXISTS `staff` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prenom` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `poste` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nationalite` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `telephone` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_embauche` date DEFAULT NULL,
  `email` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `equipes` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_equipes` (`equipes`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `staff`
--

INSERT INTO `staff` (`id`, `nom`, `prenom`, `poste`, `nationalite`, `date_naissance`, `telephone`, `date_embauche`, `email`, `created_at`, `equipes`) VALUES
(2, 'Smith', 'John', 'Défenseur', 'Anglaise', '1992-09-22', '0712345678', '2022-05-20', 'john.smith@email.com', '2025-02-18 12:35:21', NULL),
(3, 'Lopez', 'Carlos', 'Milieu', 'Espagnole', '1998-03-30', '0812345678', '2021-08-15', 'carlos.lopez@email.com', '2025-02-18 12:35:21', NULL),
(4, 'sss', 'dd', 'MEDci', 'MAROCAINE', '2000-06-16', '06794', '2025-01-31', 'TEST@gmail.com', '2025-02-18 12:36:15', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `tournois`
--

DROP TABLE IF EXISTS `tournois`;
CREATE TABLE IF NOT EXISTS `tournois` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tournois`
--

INSERT INTO `tournois` (`id`, `nom`, `type`, `date_debut`, `date_fin`) VALUES
(1, 'Botola Pro', NULL, NULL, NULL),
(2, 'Coupe du Trône', NULL, NULL, NULL),
(3, 'Coupe de la CAF', NULL, NULL, NULL),
(4, 'Ligue des Champions CAF', NULL, NULL, NULL),
(5, 'Super Coupe du Maroc', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `trophees`
--

DROP TABLE IF EXISTS `trophees`;
CREATE TABLE IF NOT EXISTS `trophees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipe_id` int NOT NULL,
  `cl` int DEFAULT '0',
  `botola` int DEFAULT '0',
  `coup_trone` int DEFAULT '0',
  `caf` int DEFAULT '0',
  `coup_arabe` int DEFAULT '0',
  `super_coup` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `equipe_id` (`equipe_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `trophees`
--

INSERT INTO `trophees` (`id`, `equipe_id`, `cl`, `botola`, `coup_trone`, `caf`, `coup_arabe`, `super_coup`) VALUES
(1, 1, 3, 17, 9, 1, 1, 1),
(2, 2, 3, 13, 9, 3, 2, 2),
(3, 3, 1, 13, 12, 0, 0, 1),
(4, 4, 0, 1, 2, 2, 0, 2),
(5, 5, 0, 1, 1, 1, 0, 0),
(6, 6, 0, 4, 4, 1, 1, 0),
(7, 7, 0, 2, 0, 0, 0, 0),
(8, 8, 0, 0, 0, 0, 0, 0),
(9, 9, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `verified` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `role` enum('user','admin','admin_tournoi') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `verified`, `created_at`, `role`) VALUES
(3, 'test', 'test@gmail.com', '123', 1, '2025-02-12 20:20:25', 'admin'),
(5, 'user1', 'user1@gmail.com', '$2y$10$xFWpYPu2H0F48gCSHfVye.szWgcj5yeEIrxZOS08VegpmD33QX1j6', 1, '2025-02-13 14:44:38', 'user'),
(6, 'user2', 'user2@gmail.com', '1234', 1, '2025-02-13 14:47:18', 'user'),
(7, 'user3', 'user3@gmail.com', '12A', 1, '2025-02-15 12:09:38', 'user'),
(8, 'user6', 'user5@gmail.com', '123A', 1, '2025-02-15 12:11:16', 'user'),
(9, 'salma', 'salmadidou25@gmail.com', '123SALMA', 1, '2025-02-17 19:36:51', 'user'),
(10, 'yassine', 'chouqi@gmail.com', '123', 1, '2025-03-17 17:34:08', 'user');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `abonnements_joueurs`
--
ALTER TABLE `abonnements_joueurs`
  ADD CONSTRAINT `abonnements_joueurs_ibfk_1` FOREIGN KEY (`joueur_id`) REFERENCES `joueurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `joueurs`
--
ALTER TABLE `joueurs`
  ADD CONSTRAINT `joueurs_ibfk_1` FOREIGN KEY (`equipes`) REFERENCES `equipe` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `matchs`
--
ALTER TABLE `matchs`
  ADD CONSTRAINT `matchs_ibfk_1` FOREIGN KEY (`equipe1_id`) REFERENCES `equipe` (`id`),
  ADD CONSTRAINT `matchs_ibfk_2` FOREIGN KEY (`equipe2_id`) REFERENCES `equipe` (`id`),
  ADD CONSTRAINT `matchs_ibfk_3` FOREIGN KEY (`tournoi_id`) REFERENCES `tournois` (`id`);

--
-- Contraintes pour la table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `fk_equipes` FOREIGN KEY (`equipes`) REFERENCES `equipe` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
