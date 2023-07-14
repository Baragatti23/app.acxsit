-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-06-2023 a las 19:03:55
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `acxs_it`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activites`
--

CREATE TABLE `activites` (
  `reference_activite` varchar(15) NOT NULL,
  `reference_utilisateur` varchar(15) NOT NULL,
  `action_activite` varchar(55) NOT NULL,
  `table_activite` varchar(255) NOT NULL,
  `element_activite` varchar(15) NOT NULL,
  `created_at_activite` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at_activite` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `activites`
--

INSERT INTO `activites` (`reference_activite`, `reference_utilisateur`, `action_activite`, `table_activite`, `element_activite`, `created_at_activite`, `updated_at_activite`) VALUES
('ACT110935VBD', 'UTS109083DOM', 'UPDATE', 'PROFORMAS', 'F023-007', '2023-06-18 14:41:20', '2023-06-18 14:41:20'),
('ACT142489YQU', 'UTS109083DOM', 'CREATE', 'STAGERES', 'STA969443LBS', '2023-06-19 17:32:36', '2023-06-19 17:32:36'),
('ACT167757IVZ', 'UTS109083DOM', 'CREATE', 'STAGERES', 'STA665548MIX', '2023-06-19 17:26:54', '2023-06-19 17:26:54'),
('ACT367369OQH', 'UTS109083DOM', 'CREATE', 'STAGERES', 'STA599267VXR', '2023-06-19 17:28:32', '2023-06-19 17:28:32'),
('ACT413736FYF', 'UTS109083DOM', 'UPDATE', 'BORDEREAUS', 'BL-F023-003', '2023-06-19 16:52:55', '2023-06-19 16:52:55'),
('ACT455853QIG', 'UTS109083DOM', 'CREATE', 'STAGERES', 'STA024597VJY', '2023-06-19 17:29:50', '2023-06-19 17:29:50'),
('ACT572238ESE', 'UTS109083DOM', 'CREATE', 'FORMATIONS', 'FOR543216DVU', '2023-06-19 16:03:30', '2023-06-19 16:03:30'),
('ACT672386OCC', 'UTS109083DOM', 'CREATE', 'FORMATIONS', 'FOR292300CSX', '2023-06-19 16:09:25', '2023-06-19 16:09:25'),
('ACT679696YQA', 'UTS109083DOM', 'CREATE', 'STAGERES', 'STA249712MIJ', '2023-06-19 17:34:00', '2023-06-19 17:34:00'),
('ACT714195EZV', 'UTS109083DOM', 'CREATE', 'LICENCES', 'LIC206272JEI', '2023-06-19 17:54:10', '2023-06-19 17:54:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bordereaus`
--

CREATE TABLE `bordereaus` (
  `reference_bordereau` varchar(15) NOT NULL,
  `nom_livreur_bordereau` varchar(100) NOT NULL,
  `nom_recepteur_bordereau` varchar(100) DEFAULT NULL,
  `contact_livreur_bordereau` varchar(255) NOT NULL,
  `contact_recepteur_bordereau` varchar(255) DEFAULT NULL,
  `reference_estado` varchar(15) NOT NULL,
  `created_at_bordereau` datetime NOT NULL DEFAULT '2023-05-09 11:34:49',
  `updated_at_bordereau` datetime NOT NULL DEFAULT '2023-05-09 11:34:49',
  `reference_proforma` varchar(15) DEFAULT NULL,
  `reference_utilisateur` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `bordereaus`
--

INSERT INTO `bordereaus` (`reference_bordereau`, `nom_livreur_bordereau`, `nom_recepteur_bordereau`, `contact_livreur_bordereau`, `contact_recepteur_bordereau`, `reference_estado`, `created_at_bordereau`, `updated_at_bordereau`, `reference_proforma`, `reference_utilisateur`) VALUES
('BL-F023-003', 'Nassirath Lourdes', 'Sagbo Matias', '+229 78656757', '+229 90876756', 'EST0003', '2023-06-18 07:38:27', '2023-06-19 14:52:55', 'F023-006', 'UTS109083DOM'),
('BOR546781EEI', 'HOUNOU AxelSSS', 'Josiane Bolopo', '+229 89768976', '+229 56786545', 'EST0002', '2023-06-07 17:53:18', '2023-06-15 13:33:34', 'F023-006', 'UTS109083DOM');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calcules`
--

CREATE TABLE `calcules` (
  `reference_proforma` varchar(15) NOT NULL,
  `reference_equipement` varchar(15) NOT NULL,
  `gpl_equipement_calcule` double NOT NULL,
  `discount_calcule` double DEFAULT NULL,
  `gpl_calcule` double DEFAULT NULL,
  `transport_calcule` double DEFAULT NULL,
  `douane_calcule` double DEFAULT NULL,
  `marge_calcule` double DEFAULT NULL,
  `unites_calcule` int(11) NOT NULL,
  `tva_calcule` double DEFAULT NULL,
  `prix_achat_calcule` double DEFAULT NULL,
  `prix_vente_calcule` double DEFAULT NULL,
  `currency_calcule` varchar(10) NOT NULL,
  `created_at_calcule` datetime NOT NULL,
  `updated_at_calcule` datetime NOT NULL,
  `reference_utilisateur` varchar(15) NOT NULL,
  `reference_type` varchar(15) NOT NULL,
  `montant_total_ht_calcule` double DEFAULT NULL,
  `montant_total_ttc_calcule` double DEFAULT NULL,
  `montant_unitaire_ttc_calcule` double DEFAULT NULL,
  `montant_unitaire_ht_calcule` double DEFAULT NULL,
  `tva_percent_calcule` double NOT NULL,
  `douane_percent_calcule` double NOT NULL,
  `currency_value_calcule` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `calcules`
--

INSERT INTO `calcules` (`reference_proforma`, `reference_equipement`, `gpl_equipement_calcule`, `discount_calcule`, `gpl_calcule`, `transport_calcule`, `douane_calcule`, `marge_calcule`, `unites_calcule`, `tva_calcule`, `prix_achat_calcule`, `prix_vente_calcule`, `currency_calcule`, `created_at_calcule`, `updated_at_calcule`, `reference_utilisateur`, `reference_type`, `montant_total_ht_calcule`, `montant_total_ttc_calcule`, `montant_unitaire_ttc_calcule`, `montant_unitaire_ht_calcule`, `tva_percent_calcule`, `douane_percent_calcule`, `currency_value_calcule`) VALUES
('F023-006', 'EQU0001', 280500, 5, 266475, 44000, 434665, 5, 1, 218509.5, 745140, 266475, 'USD', '2023-06-07 14:42:44', '2023-06-07 14:47:24', 'UTS109083DOM', 'TYP0001', 266475, 484984.5, 484984.5, 266475, 18, 40, 550),
('F023-006', 'EQU183822GBX', 1071950, 18, 878999, 121000, 1399998.6, 7, 7, 5722283.49, 2399997.6, 996913.5, 'USD', '2023-06-07 14:55:13', '2023-06-07 14:55:13', 'UTS109083DOM', 'TYP0001', 6978394.5, 12700677.99, 1814382.57, 996913.5, 18, 40, 550),
('F023-006', 'EQU289381QJZ', 434500, 19, 351945, 69850, 590513, 2.5, 29, 10074099.75, 1012308, 423637.5, 'USD', '2023-06-07 14:49:15', '2023-06-07 14:49:15', 'UTS109083DOM', 'TYP0001', 12285487.5, 22359587.25, 771020.25, 423637.5, 18, 40, 550);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `reference_categorie` varchar(15) NOT NULL,
  `libelle_categorie` varchar(255) NOT NULL,
  `created_at_categorie` datetime NOT NULL,
  `updated_at_categorie` datetime NOT NULL,
  `reference_utilisateur` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`reference_categorie`, `libelle_categorie`, `created_at_categorie`, `updated_at_categorie`, `reference_utilisateur`) VALUES
('CAT0001', 'Router', '2023-05-30 12:15:08', '2023-05-30 12:15:08', 'UTS109083DOM'),
('CAT0002', 'Swtich', '2023-05-30 12:15:08', '2023-05-30 12:15:08', 'UTS109083DOM'),
('CAT0004', 'Controller', '2023-05-30 12:15:08', '2023-05-30 12:15:08', 'UTS109083DOM'),
('CAT0005', 'Repetiteur', '2023-05-30 12:15:08', '2023-05-30 12:15:08', 'UTS109083DOM'),
('CAT0006', 'UTP Cable', '2023-05-30 12:15:08', '2023-05-30 12:15:08', 'UTS109083DOM'),
('CAT0007', 'Optical Fiber Cable', '2023-05-30 12:15:08', '2023-05-30 12:15:08', 'UTS109083DOM'),
('CAT0008', 'Firewall', '2023-05-30 12:15:08', '2023-06-18 08:15:46', 'UTS109083DOM');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clients`
--

CREATE TABLE `clients` (
  `reference_client` varchar(15) NOT NULL,
  `email_client` varchar(255) NOT NULL,
  `telephone_client` varchar(50) NOT NULL,
  `name_client` varchar(60) NOT NULL,
  `address_client` varchar(255) DEFAULT NULL,
  `created_at_client` datetime NOT NULL DEFAULT '2023-05-09 11:34:48',
  `updated_at_client` datetime NOT NULL DEFAULT '2023-05-09 11:34:48',
  `reference_utilisateur` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clients`
--

INSERT INTO `clients` (`reference_client`, `email_client`, `telephone_client`, `name_client`, `address_client`, `created_at_client`, `updated_at_client`, `reference_utilisateur`) VALUES
('CLI366781NWL', 'people@cceibank.com', '+229 90873687', 'CCEI BANK', 'Ganhi', '2023-06-18 11:14:31', '2023-06-18 11:14:31', 'UTS109083DOM'),
('CLI943314WDS', 'contact@nsiabanque.bj', '+229 78675434', 'NSIA Banque Bénin', 'Ganhi', '2023-05-29 21:16:20', '2023-06-18 08:20:59', 'UTI0001');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `connexions`
--

CREATE TABLE `connexions` (
  `reference_connexion` varchar(15) NOT NULL,
  `ip_connexion` varchar(80) DEFAULT NULL,
  `etat_connexion` varchar(15) DEFAULT NULL,
  `browser_connexion` varchar(60) DEFAULT NULL,
  `os_connexion` varchar(60) DEFAULT NULL,
  `user_agent_connexion` text DEFAULT NULL,
  `date_closed_connexion` datetime DEFAULT NULL,
  `created_at_connexion` datetime NOT NULL DEFAULT '2023-05-09 11:34:52',
  `updated_at_connexion` datetime NOT NULL DEFAULT '2023-05-09 11:34:52',
  `reference_utilisateur` varchar(15) NOT NULL,
  `token_connexion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `connexions`
--

INSERT INTO `connexions` (`reference_connexion`, `ip_connexion`, `etat_connexion`, `browser_connexion`, `os_connexion`, `user_agent_connexion`, `date_closed_connexion`, `created_at_connexion`, `updated_at_connexion`, `reference_utilisateur`, `token_connexion`) VALUES
('CON118447DNW', NULL, 'OPEN', 'Chrome', 'Windows', '', NULL, '2023-06-19 09:14:35', '2023-06-19 09:14:35', 'UTS109083DOM', '112|cjaQ5AvnRMuzejKUr7dpuNoMR9sVy6yhtCWTMzSS'),
('CON532759MMG', NULL, 'OPEN', 'Chrome', 'Windows', '', NULL, '2023-06-19 09:13:51', '2023-06-19 09:13:51', 'UTS109083DOM', '111|StrHYJBbrDPL8imy5GcUSxd0pMCxPPFfTunKvssb'),
('CON542852HYD', NULL, 'OPEN', 'Chrome', 'Windows', '', NULL, '2023-06-18 13:14:46', '2023-06-18 13:14:46', 'UTS109083DOM', '110|hpZ61HkhwKPGe6rN3japI6sfaUhA6gbSGUXZuC1k');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipements`
--

CREATE TABLE `equipements` (
  `reference_equipement` varchar(15) NOT NULL,
  `designation_equipement` varchar(80) NOT NULL,
  `reference_categorie` varchar(60) DEFAULT NULL,
  `prix_vente_equipement` double DEFAULT 0,
  `prix_achat_equipement` double DEFAULT 0,
  `stock_equipement` int(11) DEFAULT 0,
  `reference_licence` varchar(15) DEFAULT NULL,
  `gpl_equipement` double DEFAULT 0,
  `caracteristiques_equipement` text DEFAULT NULL,
  `created_at_equipement` datetime NOT NULL DEFAULT '2023-05-09 11:34:50',
  `updated_at_equipement` datetime NOT NULL DEFAULT '2023-05-09 11:34:50',
  `reference_fournisseur` varchar(15) DEFAULT NULL,
  `reference_utilisateur` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `equipements`
--

INSERT INTO `equipements` (`reference_equipement`, `designation_equipement`, `reference_categorie`, `prix_vente_equipement`, `prix_achat_equipement`, `stock_equipement`, `reference_licence`, `gpl_equipement`, `caracteristiques_equipement`, `created_at_equipement`, `updated_at_equipement`, `reference_fournisseur`, `reference_utilisateur`) VALUES
('EQU0001', 'Cisco Catalyst 9400 Series Switches', 'CAT0002', NULL, NULL, NULL, NULL, 0, NULL, '2023-05-09 11:34:50', '2023-05-09 11:34:50', 'FOU961776FQ', 'UTS00009'),
('EQU183822GBX', 'Cisco 5000 Series Enterprise Network Compute System', 'CAT0001', NULL, NULL, NULL, NULL, NULL, NULL, '2023-05-30 09:34:55', '2023-05-30 09:34:55', 'FOU961776FQ', 'UTI0001');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados`
--

CREATE TABLE `estados` (
  `reference_estado` varchar(15) NOT NULL,
  `libelle_estado` varchar(15) NOT NULL,
  `description_estado` varchar(255) DEFAULT NULL,
  `rgb_color_estado` varchar(30) NOT NULL,
  `created_at_estado` datetime DEFAULT current_timestamp(),
  `updated_at_estado` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `estados`
--

INSERT INTO `estados` (`reference_estado`, `libelle_estado`, `description_estado`, `rgb_color_estado`, `created_at_estado`, `updated_at_estado`) VALUES
('EST0001', 'EN ATTENTE', 'Bordereau crée non livré', '102, 88, 221', '2023-05-22 17:20:30', '2023-05-22 17:20:30'),
('EST0002', 'LIVREE', 'Bordereau déjà livré', '31, 230, 147', '2023-05-22 17:20:30', '2023-05-22 17:20:30'),
('EST0003', 'ANNULE', 'Bordereau annulé', '250, 62, 121', '2023-05-22 17:20:30', '2023-05-22 17:20:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formations`
--

CREATE TABLE `formations` (
  `reference_formation` varchar(15) NOT NULL,
  `libelle_formation` varchar(255) NOT NULL,
  `duree_formation` int(11) NOT NULL,
  `cout_formation` double NOT NULL,
  `created_at_formation` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at_formation` datetime NOT NULL DEFAULT current_timestamp(),
  `domaine_formation` varchar(255) NOT NULL,
  `reference_utilisateur` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `formations`
--

INSERT INTO `formations` (`reference_formation`, `libelle_formation`, `duree_formation`, `cout_formation`, `created_at_formation`, `updated_at_formation`, `domaine_formation`, `reference_utilisateur`) VALUES
('FOR144626WIY', 'CCNA routing & switching', 2, 145000, '2023-06-08 16:06:00', '2023-06-08 16:07:24', 'Administration réseaux', 'UTS109083DOM'),
('FOR292300CSX', 'IPV6', 1, 150000, '2023-06-19 14:09:25', '2023-06-19 14:09:25', 'Réseaux', 'UTS109083DOM'),
('FOR543216DVU', 'Cisco ISE', 2, 1500000, '2023-06-19 14:03:30', '2023-06-19 14:03:30', 'Securité réseaux', 'UTS109083DOM'),
('FOR648422LCV', 'Cisco CCNA I', 3, 1000, '2023-05-29 22:17:45', '2023-06-08 16:05:16', 'Aministration Réseaux', 'UTI0001');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fournisseurs`
--

CREATE TABLE `fournisseurs` (
  `reference_fournisseur` varchar(15) NOT NULL,
  `nom_fournisseur` varchar(80) NOT NULL,
  `telephone_fournisseur` varchar(50) NOT NULL,
  `email_fournisseur` varchar(255) NOT NULL,
  `adresse_fournisseur` varchar(255) NOT NULL,
  `created_at_fournisseur` datetime NOT NULL DEFAULT '2023-05-09 11:34:49',
  `updated_at_fournisseur` datetime NOT NULL DEFAULT '2023-05-09 11:34:49',
  `reference_utilisateur` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `fournisseurs`
--

INSERT INTO `fournisseurs` (`reference_fournisseur`, `nom_fournisseur`, `telephone_fournisseur`, `email_fournisseur`, `adresse_fournisseur`, `created_at_fournisseur`, `updated_at_fournisseur`, `reference_utilisateur`) VALUES
('FOU961776FQ', 'Cisco', '+1 986 6765 6899', 'clients@cisco.us', '78965 CA-111', '2023-06-14 10:12:25', '2023-06-14 10:12:25', 'UTS109083DOM');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `levels`
--

CREATE TABLE `levels` (
  `reference_level` varchar(15) NOT NULL,
  `libelle_level` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `levels`
--

INSERT INTO `levels` (`reference_level`, `libelle_level`, `created_at`, `updated_at`) VALUES
('LEV0001', 'NOUVELLE', '2023-05-10 15:46:50', '2023-05-10 15:46:50'),
('LEV0002', 'EN COURS', '2023-05-10 15:46:50', '2023-05-10 15:46:50'),
('LEV0003', 'EN EXPIRANT', '2023-05-10 15:46:50', '2023-05-10 15:46:50'),
('LEV0004', 'EXPIREE', '2023-05-10 15:46:50', '2023-05-10 15:46:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `licences`
--

CREATE TABLE `licences` (
  `reference_licence` varchar(15) NOT NULL,
  `libelle_licence` varchar(255) NOT NULL,
  `duree_licence` int(11) NOT NULL,
  `reference_fournisseur` varchar(15) NOT NULL,
  `created_at_licence` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at_licence` datetime NOT NULL DEFAULT current_timestamp(),
  `reference_utilisateur` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `licences`
--

INSERT INTO `licences` (`reference_licence`, `libelle_licence`, `duree_licence`, `reference_fournisseur`, `created_at_licence`, `updated_at_licence`, `reference_utilisateur`) VALUES
('LIC206272JEI', 'Windows 11', 6, 'FOU961776FQ', '2023-06-19 15:54:10', '2023-06-19 15:54:10', 'UTS109083DOM'),
('LIC376348HRV', 'PL-3200 Cisco', 12, 'FOU961776FQ', '2023-06-14 11:08:32', '2023-06-14 11:26:05', 'UTS109083DOM');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `livrers`
--

CREATE TABLE `livrers` (
  `reference_bordereau` varchar(15) NOT NULL,
  `reference_equipement` varchar(15) NOT NULL,
  `unites_livrer` int(11) NOT NULL,
  `numero_serie_livrer` varchar(30) NOT NULL,
  `created_at_livrer` datetime NOT NULL DEFAULT '2023-05-09 11:34:53',
  `updated_at_livrer` datetime NOT NULL DEFAULT '2023-05-09 11:34:53',
  `reference_utilisateur` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `livrers`
--

INSERT INTO `livrers` (`reference_bordereau`, `reference_equipement`, `unites_livrer`, `numero_serie_livrer`, `created_at_livrer`, `updated_at_livrer`, `reference_utilisateur`) VALUES
('BOR546781EEI', 'EQU183822GBX', 2, '1234-5678-9012-3456', '2023-05-09 11:34:53', '2023-05-09 11:34:53', 'UTS109083DOM');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2023_04_20_01054_create_profils_table', 1),
(6, '2023_04_20_022308_create_status_table', 1),
(7, '2023_04_20_082659_create_utilisateurs_table', 1),
(8, '2023_04_20_131849_create_clients_table', 1),
(9, '2023_04_24_132259_create_proformas_table', 1),
(10, '2023_04_24_133602_create_bordereaus_table', 1),
(11, '2023_04_24_134421_create_fournisseurs_table', 1),
(12, '2023_04_24_134514_create_equipements_table', 1),
(13, '2023_04_24_134629_create_licences_table', 1),
(14, '2023_04_24_140939_create_connexions_table', 1),
(15, '2023_05_02_162618_create_calcules_table', 1),
(16, '2023_05_02_192620_create_livrers_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'auth_token', '81ed89aca41f7cc34cdbb1b49d35717c8d3d4b8a12d740197577e6fc9c209a13', '[\"*\"]', NULL, NULL, '2023-05-25 03:40:07', '2023-05-25 03:40:07'),
(2, 'App\\Models\\User', 2, 'auth_token', '3d9cd062f94b3b0f43d48ddd22b5a7547b60ec941a5a3bb59b9787b1e8553cb8', '[\"*\"]', NULL, NULL, '2023-05-25 03:41:33', '2023-05-25 03:41:33'),
(3, 'App\\Models\\User', 3, 'auth_token', '46c4e2567cdfe37a9c0060d7ee5754c718f17b789e6a66de25bdabfd54093ca2', '[\"*\"]', NULL, NULL, '2023-05-25 04:12:12', '2023-05-25 04:12:12'),
(4, 'App\\Models\\User', 3, 'auth_token', '93744759e2834f5190d17a4f63dd9144cac2f4321cfceb930bc59b485404b334', '[\"*\"]', NULL, NULL, '2023-05-25 04:12:21', '2023-05-25 04:12:21'),
(5, 'App\\Models\\User', 3, 'auth_token', 'af10edfcfc080a6bbe2d57f0e24d9f9054604ae9ae44c5c89ddd0377a83e66b2', '[\"*\"]', NULL, NULL, '2023-05-25 04:30:28', '2023-05-25 04:30:28'),
(48, 'App\\Models\\User', 9, 'auth_token', '406399faceee55c334c11e3fefab0bf339b99e685148ae7bdef3cad207063dd2', '[\"*\"]', NULL, NULL, '2023-05-25 10:02:16', '2023-05-25 10:02:16'),
(49, 'App\\Models\\User', 9, 'auth_token', 'f8efe12b3930fbf4f7e82c496c7a0471516c19035a52d2b37baf9182c9b0d66a', '[\"*\"]', NULL, NULL, '2023-05-25 10:02:31', '2023-05-25 10:02:31'),
(50, 'App\\Models\\User', 9, 'auth_token', 'cdab0f5a7e00fc8f1d04ebd544c089f57449e1f0e4ba4d94732f58946c003059', '[\"*\"]', NULL, NULL, '2023-05-25 10:06:21', '2023-05-25 10:06:21'),
(51, 'App\\Models\\User', 9, 'auth_token', 'ff840423ad55edc4d915e71501978987ab2318239ad4365fddbd5bcce4e770e5', '[\"*\"]', NULL, NULL, '2023-05-25 10:17:00', '2023-05-25 10:17:00'),
(52, 'App\\Models\\User', 9, 'auth_token', 'b006cd793f54e2d19e1ea017d2943bd59fb470a17e2523a54e9fb347ec7f2c9e', '[\"*\"]', NULL, NULL, '2023-05-25 10:19:14', '2023-05-25 10:19:14'),
(53, 'App\\Models\\User', 9, 'auth_token', '549e5c7fdf11a66c827363b154ba22510a50a7ea0cb67d884850702fa611f953', '[\"*\"]', NULL, NULL, '2023-05-25 10:22:53', '2023-05-25 10:22:53'),
(54, 'App\\Models\\User', 9, 'auth_token', '54afbe01823d5ef37b6d7419f034603c4fccba9261e01e850e0c78d63ad6fb62', '[\"*\"]', NULL, NULL, '2023-05-25 11:22:26', '2023-05-25 11:22:26'),
(55, 'App\\Models\\User', 9, 'auth_token', 'e73b79e8127b3ab85f372a5a4246f91ebaa56abf543747197fb28f7495da7704', '[\"*\"]', NULL, NULL, '2023-05-25 11:23:06', '2023-05-25 11:23:06'),
(56, 'App\\Models\\User', 9, 'auth_token', '5b672d5523882e4adf76b61ebf13ec2e76874aeca4b480d7b136b727937e9bf3', '[\"*\"]', NULL, NULL, '2023-05-25 11:24:13', '2023-05-25 11:24:13'),
(57, 'App\\Models\\User', 9, 'auth_token', 'c468bf725ee1a0e6296aa11e84eb5c3c2b8a4752fc2ff089bf8df6542ca3c0e8', '[\"*\"]', NULL, NULL, '2023-05-25 11:30:26', '2023-05-25 11:30:26'),
(58, 'App\\Models\\User', 9, 'auth_token', '900eed5c65e5e3e427cb78ca5741df830fd265c31ff26c9d73caca16530b5ca2', '[\"*\"]', NULL, NULL, '2023-05-25 11:30:54', '2023-05-25 11:30:54'),
(59, 'App\\Models\\User', 9, 'auth_token', 'ea1bbf073556592064b6687ff218c35d4c3c21388d897efe0a974433b07b2513', '[\"*\"]', NULL, NULL, '2023-05-25 16:46:44', '2023-05-25 16:46:44'),
(60, 'App\\Models\\User', 9, 'auth_token', 'a8db41f78a33b7371b011f52502c1c648ec8bfaec8fc7e988b525728358156b2', '[\"*\"]', NULL, NULL, '2023-05-25 16:53:29', '2023-05-25 16:53:29'),
(61, 'App\\Models\\User', 9, 'auth_token', 'd103f65da76980b5b71b6e25651e9c40de57300a56b6b4dbfe3658960fb46b4e', '[\"*\"]', NULL, NULL, '2023-05-25 16:54:55', '2023-05-25 16:54:55'),
(62, 'App\\Models\\User', 9, 'auth_token', 'cb57be08b66adb74c71c961064080a4eefe47387d8ffacad6b03992dccb15afe', '[\"*\"]', NULL, NULL, '2023-05-25 17:28:08', '2023-05-25 17:28:08'),
(63, 'App\\Models\\User', 9, 'auth_token', '853c5762277fc0a1845f094edd009fa19607e1040ae2150b01f96d6a51d76f09', '[\"*\"]', NULL, NULL, '2023-05-25 17:37:46', '2023-05-25 17:37:46'),
(64, 'App\\Models\\User', 9, 'auth_token', 'd7a94585919f332f6fbbe2835cbe1274b90bf68a5d5fd572bc60679ff10c5d0f', '[\"*\"]', NULL, NULL, '2023-05-26 04:16:34', '2023-05-26 04:16:34'),
(65, 'App\\Models\\User', 9, 'auth_token', 'b55de2256bb16d0c510be768fcd8e2b4ad9e5fc615de9a95b8febcc8b49cd277', '[\"*\"]', NULL, NULL, '2023-05-26 04:25:21', '2023-05-26 04:25:21'),
(66, 'App\\Models\\User', 9, 'auth_token', '8b6a5e02a5c5bdaf02ba83274c25e6dc4fb42ca60667fea2bf0248819ff8321f', '[\"*\"]', NULL, NULL, '2023-05-26 04:27:04', '2023-05-26 04:27:04'),
(67, 'App\\Models\\User', 9, 'auth_token', 'ec212570a4ff2d0e00c46326fe1ba8bc02438218a7bfa23253d7e69097b28283', '[\"*\"]', NULL, NULL, '2023-05-26 04:28:16', '2023-05-26 04:28:16'),
(68, 'App\\Models\\User', 9, 'auth_token', 'c2c755a554f7b8611fb2bf37269e0742b948a6c3e9900ec55faa2b6f65dcb79e', '[\"*\"]', NULL, NULL, '2023-05-26 04:31:46', '2023-05-26 04:31:46'),
(69, 'App\\Models\\User', 9, 'auth_token', '76766259b54801e8a7f0dabfd0a899860311b8bfb62ca4626fc239606166aa0f', '[\"*\"]', NULL, NULL, '2023-05-26 04:47:11', '2023-05-26 04:47:11'),
(70, 'App\\Models\\User', 9, 'auth_token', '276ca3a0996a9ad141b36e46bf13aca9b56f10d54fbe782d3b476a0ad8774f73', '[\"*\"]', NULL, NULL, '2023-05-26 04:48:59', '2023-05-26 04:48:59'),
(71, 'App\\Models\\User', 9, 'auth_token', '2b9ec6c83607b8980b6db291b76dfc5f0c58e0519ebb07eef991ce7553aa4760', '[\"*\"]', NULL, NULL, '2023-05-26 04:50:02', '2023-05-26 04:50:02'),
(72, 'App\\Models\\User', 9, 'auth_token', '49f8b6a6588fa97f9ee110e39752f9def017c40754fed26571228871ca9b4a5b', '[\"*\"]', NULL, NULL, '2023-05-26 05:55:39', '2023-05-26 05:55:39'),
(73, 'App\\Models\\User', 9, 'auth_token', 'f43244fd3a81b1e14d0945e75ec783276b32d2c2826fb8d11429cc6e7c0822fd', '[\"*\"]', NULL, NULL, '2023-05-26 06:44:16', '2023-05-26 06:44:16'),
(74, 'App\\Models\\User', 9, 'auth_token', '23a75380c2d6259174d9ca0b3f69ef9fd4dd04488ba398dbe28378d72830e8d1', '[\"*\"]', NULL, NULL, '2023-05-26 07:00:56', '2023-05-26 07:00:56'),
(75, 'App\\Models\\User', 9, 'auth_token', '98995714931255796468e3b63ba96ecfb4899d34c743d1cf91144015f74872c7', '[\"*\"]', NULL, NULL, '2023-05-26 07:01:49', '2023-05-26 07:01:49'),
(76, 'App\\Models\\User', 9, 'auth_token', '80c10ce5dae2a9f776206db3aea6a17a4de1aba95f4cfa42168771fdbc133e93', '[\"*\"]', NULL, NULL, '2023-05-26 07:03:02', '2023-05-26 07:03:02'),
(77, 'App\\Models\\User', 9, 'auth_token', 'c8ceb2cba8b7070f6ac181a80426ab62f61a6769b90925da6673cf8cb18057bd', '[\"*\"]', NULL, NULL, '2023-05-26 07:04:48', '2023-05-26 07:04:48'),
(78, 'App\\Models\\User', 9, 'auth_token', '368d82ab63d5f99c4ca3ed63d11af62111820b28fb7c640e8c9a01f183f947dc', '[\"*\"]', NULL, NULL, '2023-05-26 07:07:28', '2023-05-26 07:07:28'),
(79, 'App\\Models\\User', 9, 'auth_token', '899b8d2fb63e3c061ff6076ef31847f7662c6816310b0e681f257d81d2e88d09', '[\"*\"]', NULL, NULL, '2023-05-26 08:22:49', '2023-05-26 08:22:49'),
(80, 'App\\Models\\User', 9, 'auth_token', 'a8ba06a5aca07c91ff44c0f45fa599f367994ed7cdcd5a9ea4e24cfff2fa3808', '[\"*\"]', NULL, NULL, '2023-05-26 08:48:18', '2023-05-26 08:48:18'),
(81, 'App\\Models\\User', 9, 'auth_token', 'aee9331e919d6fa1ec2b511103a550b2ae544aecf8b6d6cd93fee0914afd2ab1', '[\"*\"]', NULL, NULL, '2023-05-26 08:52:02', '2023-05-26 08:52:02'),
(82, 'App\\Models\\User', 9, 'auth_token', 'c66ff77736f7c5f29df8a452318430df73d733d35e7f34c054298922df7ff891', '[\"*\"]', NULL, NULL, '2023-05-26 13:54:18', '2023-05-26 13:54:18'),
(83, 'App\\Models\\User', 9, 'auth_token', '4a24ce82dc4877e46379d15ad1163ef40395036d803cca03d57d39c688030fef', '[\"*\"]', NULL, NULL, '2023-05-26 14:04:44', '2023-05-26 14:04:44'),
(84, 'App\\Models\\User', 9, 'auth_token', '18e78227974077b73dca8f3cbfe05a290293b0830a84403af9f9c3bd32016bc7', '[\"*\"]', NULL, NULL, '2023-05-26 14:06:20', '2023-05-26 14:06:20'),
(85, 'App\\Models\\User', 9, 'auth_token', '3bc83e0b2902cd068634cb8e23f32118020d23a273085ed67b3518f5752c96fc', '[\"*\"]', NULL, NULL, '2023-05-26 14:30:53', '2023-05-26 14:30:53'),
(86, 'App\\Models\\User', 9, 'auth_token', 'c6e0f1a2813756d4165ab0ae4cb2f280228a74869a36d557bae9917c031eaddf', '[\"*\"]', NULL, NULL, '2023-05-26 14:47:10', '2023-05-26 14:47:10'),
(87, 'App\\Models\\User', 20, 'auth_token', '0429537c4aab28bfb4a483bc9cb91ca6e237e1d6929cb4b60c7a5b3168d83fbc', '[\"*\"]', NULL, NULL, '2023-05-29 15:27:09', '2023-05-29 15:27:09'),
(88, 'App\\Models\\User', 21, 'auth_token', '2194d8303e0bc4b32fa007f655ccc2e36adb4ca9c03ef56b702362ed2dad4ff6', '[\"*\"]', NULL, NULL, '2023-05-29 15:41:41', '2023-05-29 15:41:41'),
(89, 'App\\Models\\User', 22, 'auth_token', 'f92b25095465128e46f6cdffb239958f20fdd181db3a438ba59a21eda960c0d1', '[\"*\"]', NULL, NULL, '2023-05-29 15:56:31', '2023-05-29 15:56:31'),
(90, 'App\\Models\\User', 23, 'auth_token', '0cd1213ec4efe4656c9e0e036e34caf673f69fc9498795023e6d9083b43403ec', '[\"*\"]', NULL, NULL, '2023-05-29 16:23:15', '2023-05-29 16:23:15'),
(91, 'App\\Models\\User', 24, 'auth_token', 'a7d1c634d6a4d77ca41b39da2152c2074e508412436699b7038f504371552e56', '[\"*\"]', NULL, NULL, '2023-05-29 16:39:40', '2023-05-29 16:39:40'),
(92, 'App\\Models\\User', 25, 'auth_token', 'daf8a3b78c6e8231b8ff77ee717498b4fe12ec8292f4fb029a0f8eca318231cf', '[\"*\"]', NULL, NULL, '2023-05-29 17:43:14', '2023-05-29 17:43:14'),
(93, 'App\\Models\\User', 26, 'auth_token', '04a24d966515cf66069eec237a3333e53be9c3923a99660d775b5972f1b8418f', '[\"*\"]', NULL, NULL, '2023-05-29 17:44:41', '2023-05-29 17:44:41'),
(94, 'App\\Models\\User', 27, 'auth_token', '4e8f6d81ee21715984f1f1074d07422eb36e9687e043ff5e2f42593877ed9e77', '[\"*\"]', NULL, NULL, '2023-05-30 05:30:48', '2023-05-30 05:30:48'),
(95, 'App\\Models\\User', 28, 'auth_token', '7f8d8546d453f873691ded3dad0e38881ab21fb5217493bbe4bc25e32f2e0965', '[\"*\"]', NULL, NULL, '2023-05-30 05:33:06', '2023-05-30 05:33:06'),
(96, 'App\\Models\\User', 29, 'auth_token', '401a4af7064b08e04b1861fc92e1f1d374c2b946b714e748ff48f292da92cad0', '[\"*\"]', NULL, NULL, '2023-05-30 12:35:09', '2023-05-30 12:35:09'),
(97, 'App\\Models\\User', 30, 'auth_token', '08085640c95381fc94e016f7882e1a853bf0e2896c8cf1530d00043a5d15e36e', '[\"*\"]', NULL, NULL, '2023-06-01 11:39:36', '2023-06-01 11:39:36'),
(98, 'App\\Models\\User', 31, 'auth_token', 'f8ab6037d306130f96162e4bfeba3608ffaf71bcff83b2fc920d1fcfedecd931', '[\"*\"]', NULL, NULL, '2023-06-03 18:16:05', '2023-06-03 18:16:05'),
(99, 'App\\Models\\User', 31, 'auth_token', '10569bfdb17903c81da5f061574f7746adad33258df6022a6a69e470fc0a2fac', '[\"*\"]', NULL, NULL, '2023-06-06 13:08:41', '2023-06-06 13:08:41'),
(100, 'App\\Models\\User', 31, 'auth_token', '4380f89f7b0f0e1b99432d98ba531aa5705d789ca561e53bd5f534106a5d5df1', '[\"*\"]', NULL, NULL, '2023-06-06 13:10:09', '2023-06-06 13:10:09'),
(101, 'App\\Models\\User', 31, 'auth_token', 'ddac0593eae5712d254c41bc41463e2e533021bdb5a09ecaf5c68bfb0712652b', '[\"*\"]', NULL, NULL, '2023-06-06 13:10:21', '2023-06-06 13:10:21'),
(102, 'App\\Models\\User', 25, 'auth_token', 'b3c0f324bbefbbb7680588bfc59fce06e23f762447707d8375db538ef279592c', '[\"*\"]', NULL, NULL, '2023-06-18 11:06:38', '2023-06-18 11:06:38'),
(103, 'App\\Models\\User', 25, 'auth_token', 'f75523bf547b96f3af43174003c062d83d790a3269942e0c78d97ae9bc4cecfe', '[\"*\"]', NULL, NULL, '2023-06-18 11:06:51', '2023-06-18 11:06:51'),
(104, 'App\\Models\\User', 25, 'auth_token', 'cd7801d51e0934198d493c268d873015917f7c5b17f7e7dafc25b9ba46284a28', '[\"*\"]', NULL, NULL, '2023-06-18 11:08:23', '2023-06-18 11:08:23'),
(105, 'App\\Models\\User', 25, 'auth_token', '26955a161d63197f661a355da387cfe7ff7bb7732c4ce4fb541c9efa0c7ee0c0', '[\"*\"]', NULL, NULL, '2023-06-18 11:10:02', '2023-06-18 11:10:02'),
(106, 'App\\Models\\User', 25, 'auth_token', '1037365d16f8a14abf93c64fb1973e99a3d6b81ee23642ead90cc48dbc7ec3ad', '[\"*\"]', NULL, NULL, '2023-06-18 11:10:10', '2023-06-18 11:10:10'),
(107, 'App\\Models\\User', 25, 'auth_token', '91efbf570dfb6246eee4fee72aecb59d76f97aa3413e12fb8c90019e177a17c6', '[\"*\"]', NULL, NULL, '2023-06-18 11:11:31', '2023-06-18 11:11:31'),
(108, 'App\\Models\\User', 25, 'auth_token', '0f0f6bbb48dfce5308726a277c05a22a9e139745a2af51886a41d45479c1c01a', '[\"*\"]', NULL, NULL, '2023-06-18 11:13:20', '2023-06-18 11:13:20'),
(109, 'App\\Models\\User', 25, 'auth_token', 'ba1f604dcb205fa8bdaee4dfa26d38e0d380b31e1522941bd6514eeed4a1597c', '[\"*\"]', NULL, NULL, '2023-06-18 11:13:34', '2023-06-18 11:13:34'),
(110, 'App\\Models\\User', 25, 'auth_token', 'f5dc7bc013a9a014268209918b55c237c7015369ae9fba3bd14cdf35a65ad071', '[\"*\"]', NULL, NULL, '2023-06-18 11:14:46', '2023-06-18 11:14:46'),
(111, 'App\\Models\\User', 25, 'auth_token', '820422a424ec18b4758ff5c80a0e0f31bf14ec4fb65bf5f735e5b7b4a48209d9', '[\"*\"]', NULL, NULL, '2023-06-19 07:13:51', '2023-06-19 07:13:51'),
(112, 'App\\Models\\User', 25, 'auth_token', '94dd909d0349dfcbd4bf1e4f9810b7777284dd07ede97498f35151064f25e36e', '[\"*\"]', NULL, NULL, '2023-06-19 07:14:35', '2023-06-19 07:14:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profils`
--

CREATE TABLE `profils` (
  `reference_profil` varchar(15) NOT NULL,
  `libelle_profil` varchar(60) NOT NULL,
  `created_at_profil` datetime NOT NULL DEFAULT '2023-05-09 11:34:46',
  `updated_at_profil` datetime NOT NULL DEFAULT '2023-05-09 11:34:46'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `profils`
--

INSERT INTO `profils` (`reference_profil`, `libelle_profil`, `created_at_profil`, `updated_at_profil`) VALUES
('PRO0001', 'USER', '2023-05-02 20:04:23', '2023-05-02 20:04:23'),
('PRO0002', 'ADMIN', '2023-05-02 20:04:23', '2023-05-02 20:04:23'),
('PRO0003', 'ROOT', '2023-05-02 20:04:23', '2023-05-02 20:04:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proformas`
--

CREATE TABLE `proformas` (
  `reference_proforma` varchar(15) NOT NULL,
  `sujet_proforma` varchar(80) NOT NULL,
  `livraison_proforma` int(11) NOT NULL,
  `garantie_proforma` int(11) NOT NULL,
  `validate_proforma` int(11) DEFAULT NULL,
  `modalite_proforma` varchar(255) DEFAULT NULL,
  `created_at_proforma` datetime NOT NULL DEFAULT '2023-05-09 11:34:48',
  `updated_at_proforma` datetime NOT NULL DEFAULT '2023-05-09 11:34:48',
  `reference_stade` varchar(15) DEFAULT NULL,
  `reference_client` varchar(15) NOT NULL,
  `reference_utilisateur` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proformas`
--

INSERT INTO `proformas` (`reference_proforma`, `sujet_proforma`, `livraison_proforma`, `garantie_proforma`, `validate_proforma`, `modalite_proforma`, `created_at_proforma`, `updated_at_proforma`, `reference_stade`, `reference_client`, `reference_utilisateur`) VALUES
('E023-001', 'REFONTE CUC', 0, 5, 15, 'chèque', '2023-05-29 21:17:11', '2023-06-02 09:36:28', 'STA0002', 'CLI943314WDS', 'UTS109083DOM'),
('F023-006', 'Installatio du Cyber Café', 90, 5, 30, '100% à la livraison', '2023-06-06 14:23:01', '2023-06-15 13:41:41', 'STA0002', 'CLI943314WDS', 'UTS109083DOM'),
('F023-007', 'Fourniture de matériels', 15, 11, 15, '100% à la livraison', '2023-06-17 10:44:35', '2023-06-18 12:41:20', 'STA0004', 'CLI943314WDS', 'UTS109083DOM');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stades`
--

CREATE TABLE `stades` (
  `reference_stade` varchar(15) NOT NULL,
  `libelle_stade` varchar(255) NOT NULL,
  `description_stade` varchar(255) NOT NULL,
  `rgb_color_stade` varchar(50) NOT NULL,
  `created_at_stade` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at_stade` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `stades`
--

INSERT INTO `stades` (`reference_stade`, `libelle_stade`, `description_stade`, `rgb_color_stade`, `created_at_stade`, `updated_at_stade`) VALUES
('STA0001', 'EN ATTENDE', 'Proforma nouvellement créee', '42, 147, 239', '2023-05-19 04:12:37', '2023-05-19 04:12:37'),
('STA0002', 'VALIDEE', 'Proforma déjà validée par le client', '31, 230, 147', '2023-05-19 04:12:37', '2023-05-19 04:12:37'),
('STA0003', 'ANNULEE', 'Proforma annulée ou rejeté par le client', '251, 82, 99', '2023-05-19 04:12:37', '2023-05-19 04:12:37'),
('STA0004', 'ENVOYEE', 'Proforma déjà envoyée au client', '255, 69, 0', '2023-05-19 04:12:37', '2023-05-19 04:12:37'),
('STA0005', 'NON COMPLETEE', 'Proforma validée mais tous les produits ne sont pas encore livrés au client', '255, 69, 0', '2023-05-19 04:12:37', '2023-05-19 04:12:37'),
('STA0006', 'COMPLETEE', 'Tous les equipemets du proforma ont été livrés', '255, 69, 0', '2023-05-19 04:12:37', '2023-05-19 04:12:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stageres`
--

CREATE TABLE `stageres` (
  `reference_stagere` varchar(15) NOT NULL,
  `nom_stagere` varchar(50) NOT NULL,
  `prenoms_stagere` varchar(80) NOT NULL,
  `sexe_stagere` varchar(50) NOT NULL,
  `telephone_stagere` varchar(50) NOT NULL,
  `email_stagere` varchar(60) NOT NULL,
  `ecole_stagere` varchar(100) NOT NULL,
  `type_stagere` varchar(50) NOT NULL,
  `date_debut_stagere` date NOT NULL,
  `date_fin_stagere` date NOT NULL,
  `reference_utilisateur` varchar(15) NOT NULL,
  `created_at_stagere` datetime NOT NULL,
  `updated_at_stagere` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `stageres`
--

INSERT INTO `stageres` (`reference_stagere`, `nom_stagere`, `prenoms_stagere`, `sexe_stagere`, `telephone_stagere`, `email_stagere`, `ecole_stagere`, `type_stagere`, `date_debut_stagere`, `date_fin_stagere`, `reference_utilisateur`, `created_at_stagere`, `updated_at_stagere`) VALUES
('STA249712MIJ', 'Obama', 'Luciano Pedro', 'Masculin', '+229 62549642', 'fifadjiguedes@gmail.com', 'ESGIS', 'Academique', '2023-04-03', '2023-07-03', 'UTS109083DOM', '2023-06-19 15:34:00', '2023-06-19 15:34:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `status`
--

CREATE TABLE `status` (
  `reference_statu` varchar(15) NOT NULL,
  `libelle_statu` varchar(50) NOT NULL,
  `created_at_statu` datetime NOT NULL DEFAULT '2023-05-09 11:34:47',
  `updated_at_statu` datetime NOT NULL DEFAULT '2023-05-09 11:34:47'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `status`
--

INSERT INTO `status` (`reference_statu`, `libelle_statu`, `created_at_statu`, `updated_at_statu`) VALUES
('STA0001', 'ENABLED', '2023-05-02 20:04:23', '2023-05-02 20:04:23'),
('STA0002', 'DISABLED', '2023-05-02 20:04:23', '2023-05-02 20:04:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tolicences`
--

CREATE TABLE `tolicences` (
  `reference_equipement` varchar(15) NOT NULL,
  `reference_licence` varchar(15) NOT NULL,
  `created_at_tolicence` datetime NOT NULL,
  `updated_at_tolicence` datetime NOT NULL,
  `reference_utilisateur` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `tolicences`
--

INSERT INTO `tolicences` (`reference_equipement`, `reference_licence`, `created_at_tolicence`, `updated_at_tolicence`, `reference_utilisateur`) VALUES
('EQU0001', 'LIC376348HRV', '2023-06-19 13:44:13', '2023-06-19 13:44:13', 'UTS109083DOM'),
('EQU183822GBX', 'LIC376348HRV', '2023-06-19 15:53:34', '2023-06-19 15:53:34', 'UTS109083DOM');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `type`
--

CREATE TABLE `type` (
  `reference_type` varchar(15) NOT NULL,
  `libelle_type` varchar(255) NOT NULL,
  `created_at_type` datetime NOT NULL,
  `updated_at_type` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `type`
--

INSERT INTO `type` (`reference_type`, `libelle_type`, `created_at_type`, `updated_at_type`) VALUES
('TYP0001', 'EQUIPEMENT', '2023-06-01 12:09:50', '2023-06-01 12:09:50'),
('TYP0002', 'LICENCE', '2023-06-01 12:09:50', '2023-06-01 12:09:50'),
('TYP0003', 'FORMATION', '2023-06-01 12:09:50', '2023-06-01 12:09:50'),
('TYP0004', 'MAIN D\'OUEVRE', '2023-06-01 12:09:50', '2023-06-01 12:09:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(25, 'baragatti23@gmail.com', NULL, '$2y$10$vMF8qj2OUziRN5ubHKmJceI3JGUBFUPj6qAEBBVNHucvAbHUMx8SW', NULL, '2023-05-29 17:43:14', '2023-05-29 17:43:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `reference_utilisateur` varchar(15) NOT NULL,
  `name_utilisateur` varchar(30) NOT NULL,
  `lastname_utilisateur` varchar(50) NOT NULL,
  `email_utilisateur` varchar(255) NOT NULL,
  `created_at_utilisateur` datetime NOT NULL DEFAULT '2023-05-09 11:34:47',
  `updated_at_utilisateur` datetime NOT NULL DEFAULT '2023-05-09 11:34:47',
  `reference_statu` varchar(15) NOT NULL,
  `reference_profil` varchar(15) NOT NULL,
  `created_by_utilisateur` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `utilisateurs`
--

INSERT INTO `utilisateurs` (`reference_utilisateur`, `name_utilisateur`, `lastname_utilisateur`, `email_utilisateur`, `created_at_utilisateur`, `updated_at_utilisateur`, `reference_statu`, `reference_profil`, `created_by_utilisateur`) VALUES
('UTS109083DOM', 'Baragatti', 'Michael', 'baragatti23@gmail.com', '2023-05-29 19:43:14', '2023-05-29 19:43:14', 'STA0001', 'PRO0001', 'UTS00009');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vendreequipements`
--

CREATE TABLE `vendreequipements` (
  `reference_proforma` varchar(15) NOT NULL,
  `reference_equipement` varchar(15) NOT NULL,
  `gpl_equipement_vendreequipement` double NOT NULL,
  `discount_vendreequipement` double DEFAULT NULL,
  `gpl_vendreequipement` double DEFAULT NULL,
  `transport_vendreequipement` double DEFAULT NULL,
  `douane_vendreequipement` double DEFAULT NULL,
  `marge_vendreequipement` double DEFAULT NULL,
  `unites_vendreequipement` int(11) NOT NULL,
  `tva_vendreequipement` double DEFAULT NULL,
  `prix_achat_vendreequipement` double DEFAULT NULL,
  `prix_vente_vendreequipement` double DEFAULT NULL,
  `currency_vendreequipement` varchar(10) NOT NULL,
  `created_at_vendreequipement` datetime NOT NULL,
  `updated_at_vendreequipement` datetime NOT NULL,
  `reference_utilisateur` varchar(15) NOT NULL,
  `reference_type` varchar(15) NOT NULL,
  `montant_total_ht_vendreequipement` double DEFAULT NULL,
  `montant_total_ttc_vendreequipement` double DEFAULT NULL,
  `montant_unitaire_ttc_vendreequipement` double DEFAULT NULL,
  `montant_unitaire_ht_vendreequipement` double DEFAULT NULL,
  `tva_percent_vendreequipement` double NOT NULL,
  `douane_percent_vendreequipement` double NOT NULL,
  `currency_value_vendreequipement` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `vendreequipements`
--

INSERT INTO `vendreequipements` (`reference_proforma`, `reference_equipement`, `gpl_equipement_vendreequipement`, `discount_vendreequipement`, `gpl_vendreequipement`, `transport_vendreequipement`, `douane_vendreequipement`, `marge_vendreequipement`, `unites_vendreequipement`, `tva_vendreequipement`, `prix_achat_vendreequipement`, `prix_vente_vendreequipement`, `currency_vendreequipement`, `created_at_vendreequipement`, `updated_at_vendreequipement`, `reference_utilisateur`, `reference_type`, `montant_total_ht_vendreequipement`, `montant_total_ttc_vendreequipement`, `montant_unitaire_ttc_vendreequipement`, `montant_unitaire_ht_vendreequipement`, `tva_percent_vendreequipement`, `douane_percent_vendreequipement`, `currency_value_vendreequipement`) VALUES
('F023-006', 'EQU183822GBX', 1071950, 18, 878999, 121000, 1399998.6, 7, 7, 5722283.49, 2399997.6, 996913.5, 'USD', '2023-06-07 14:55:13', '2023-06-07 14:55:13', 'UTS109083DOM', 'TYP0001', 6978394.5, 12700677.99, 1814382.57, 996913.5, 18, 40, 550),
('F023-006', 'EQU289381QJZ', 434500, 19, 351945, 69850, 590513, 2.5, 29, 10074099.75, 1012308, 423637.5, 'USD', '2023-06-07 14:49:15', '2023-06-07 14:49:15', 'UTS109083DOM', 'TYP0001', 12285487.5, 22359587.25, 771020.25, 423637.5, 18, 40, 550);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vendrelicences`
--

CREATE TABLE `vendrelicences` (
  `reference_vendrelicence` varchar(15) NOT NULL,
  `reference_proforma` varchar(15) NOT NULL,
  `created_at_vendrelicence` datetime NOT NULL,
  `updated_at_vendrelicence` datetime NOT NULL,
  `renouvelations_vendrelicence` int(11) NOT NULL,
  `reference_utilisateur` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `activites`
--
ALTER TABLE `activites`
  ADD PRIMARY KEY (`reference_activite`);

--
-- Indices de la tabla `bordereaus`
--
ALTER TABLE `bordereaus`
  ADD PRIMARY KEY (`reference_bordereau`),
  ADD KEY `bordereaus_reference_proforma_foreign` (`reference_proforma`);

--
-- Indices de la tabla `calcules`
--
ALTER TABLE `calcules`
  ADD PRIMARY KEY (`reference_proforma`,`reference_equipement`);

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`reference_categorie`);

--
-- Indices de la tabla `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`reference_client`);

--
-- Indices de la tabla `connexions`
--
ALTER TABLE `connexions`
  ADD PRIMARY KEY (`reference_connexion`);

--
-- Indices de la tabla `equipements`
--
ALTER TABLE `equipements`
  ADD PRIMARY KEY (`reference_equipement`),
  ADD KEY `equipements_reference_fournisseur_foreign` (`reference_fournisseur`);

--
-- Indices de la tabla `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`reference_estado`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `formations`
--
ALTER TABLE `formations`
  ADD PRIMARY KEY (`reference_formation`);

--
-- Indices de la tabla `fournisseurs`
--
ALTER TABLE `fournisseurs`
  ADD PRIMARY KEY (`reference_fournisseur`);

--
-- Indices de la tabla `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`reference_level`);

--
-- Indices de la tabla `licences`
--
ALTER TABLE `licences`
  ADD PRIMARY KEY (`reference_licence`);

--
-- Indices de la tabla `livrers`
--
ALTER TABLE `livrers`
  ADD PRIMARY KEY (`reference_bordereau`,`reference_equipement`),
  ADD KEY `livrers_reference_equipement_foreign` (`reference_equipement`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indices de la tabla `profils`
--
ALTER TABLE `profils`
  ADD PRIMARY KEY (`reference_profil`);

--
-- Indices de la tabla `proformas`
--
ALTER TABLE `proformas`
  ADD PRIMARY KEY (`reference_proforma`),
  ADD KEY `proformas_reference_client_foreign` (`reference_client`);

--
-- Indices de la tabla `stades`
--
ALTER TABLE `stades`
  ADD PRIMARY KEY (`reference_stade`);

--
-- Indices de la tabla `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`reference_statu`);

--
-- Indices de la tabla `tolicences`
--
ALTER TABLE `tolicences`
  ADD PRIMARY KEY (`reference_equipement`,`reference_licence`);

--
-- Indices de la tabla `type`
--
ALTER TABLE `type`
  ADD PRIMARY KEY (`reference_type`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indices de la tabla `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`reference_utilisateur`),
  ADD KEY `utilisateurs_reference_statu_foreign` (`reference_statu`),
  ADD KEY `utilisateurs_reference_profil_foreign` (`reference_profil`);

--
-- Indices de la tabla `vendreequipements`
--
ALTER TABLE `vendreequipements`
  ADD PRIMARY KEY (`reference_proforma`,`reference_equipement`);

--
-- Indices de la tabla `vendrelicences`
--
ALTER TABLE `vendrelicences`
  ADD PRIMARY KEY (`reference_vendrelicence`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bordereaus`
--
ALTER TABLE `bordereaus`
  ADD CONSTRAINT `bordereaus_reference_proforma_foreign` FOREIGN KEY (`reference_proforma`) REFERENCES `proformas` (`reference_proforma`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `equipements`
--
ALTER TABLE `equipements`
  ADD CONSTRAINT `equipements_reference_fournisseur_foreign` FOREIGN KEY (`reference_fournisseur`) REFERENCES `fournisseurs` (`reference_fournisseur`);

--
-- Filtros para la tabla `livrers`
--
ALTER TABLE `livrers`
  ADD CONSTRAINT `livrers_reference_bordereau_foreign` FOREIGN KEY (`reference_bordereau`) REFERENCES `bordereaus` (`reference_bordereau`),
  ADD CONSTRAINT `livrers_reference_equipement_foreign` FOREIGN KEY (`reference_equipement`) REFERENCES `equipements` (`reference_equipement`);

--
-- Filtros para la tabla `proformas`
--
ALTER TABLE `proformas`
  ADD CONSTRAINT `proformas_reference_client_foreign` FOREIGN KEY (`reference_client`) REFERENCES `clients` (`reference_client`);

--
-- Filtros para la tabla `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `utilisateurs_reference_profil_foreign` FOREIGN KEY (`reference_profil`) REFERENCES `profils` (`reference_profil`),
  ADD CONSTRAINT `utilisateurs_reference_statu_foreign` FOREIGN KEY (`reference_statu`) REFERENCES `status` (`reference_statu`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
