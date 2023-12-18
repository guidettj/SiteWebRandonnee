-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : dim. 21 mai 2023 à 16:50
-- Version du serveur : 10.4.27-MariaDB
-- Version de PHP : 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `tpnote`
--

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--
CREATE TABLE `utilisateurs` (
  `username` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`username`, `email`, `password`) VALUES
('Georges', 'georges@hotmail.fr', 'testgeorges'),
('JoraneGUIDETTI123', 'jorane@free.fr', 'testjorane'),
('Lala', 'lala@free.fr', 'testlala'),
('DylanA', 'dylan@free.fr', 'testdylan'),
('JeanD', 'jeand@free.fr', 'testjeand'),
('MartinM', 'martinm@free.fr', 'testmartin'),
('SteveT', 'steve@free.fr', 'teststeve'),
('AliceG', 'aliceg@free.fr', 'testalice'),
('NoemieP', 'noemie@free.fr', 'testnoemie');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD UNIQUE KEY `username` (`username`) USING HASH;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
