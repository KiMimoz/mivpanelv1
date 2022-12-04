-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Gazdă: localhost:3306
-- Timp de generare: iul. 27, 2022 la 07:25 PM
-- Versiune server: 10.5.15-MariaDB-1:10.5.15+maria~focal
-- Versiune PHP: 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Bază de date: `syko_sitevanzare`
--

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `auth` varchar(32) NOT NULL,
  `SteamID` varchar(32) NOT NULL DEFAULT '0',
  `online` tinyint(4) NOT NULL DEFAULT 0,
  `warn` int(11) NOT NULL DEFAULT 0,
  `last_time` date NOT NULL DEFAULT '0000-00-00',
  `minutes` int(10) NOT NULL DEFAULT 0,
  `vip` int(11) NOT NULL DEFAULT 0,
  `password` varchar(32) NOT NULL,
  `access` varchar(32) NOT NULL,
  `flags` varchar(32) NOT NULL,
  `email` varchar(50) NOT NULL DEFAULT 'email@yahoo.com',
  `Admin` int(2) NOT NULL DEFAULT 0,
  `Boss` int(2) NOT NULL DEFAULT 0,
  `IP` varchar(128) NOT NULL,
  `LastIP` varchar(128) NOT NULL,
  `panelStyle` int(11) NOT NULL DEFAULT 0,
  `Country` varchar(128) NOT NULL DEFAULT '0',
  `Facebook` varchar(128) NOT NULL DEFAULT '0',
  `Instagram` varchar(128) NOT NULL DEFAULT '0',
  `Steam` varchar(128) NOT NULL DEFAULT '0',
  `Avatar` varchar(256) NOT NULL DEFAULT 'default.png',
  `Banned` int(2) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='AMX Mod X Admins';

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `advanced_bans`
--

CREATE TABLE `advanced_bans` (
  `victim_name` varchar(32) DEFAULT NULL,
  `victim_steamid` varchar(35) DEFAULT NULL,
  `banlength` int(10) DEFAULT NULL,
  `unbantime` varchar(32) DEFAULT NULL,
  `reason` varchar(128) DEFAULT NULL,
  `admin_name` varchar(64) DEFAULT NULL,
  `admin_steamid` varchar(35) DEFAULT NULL,
  `date` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `countries`
--

CREATE TABLE `countries` (
  `ID` int(11) NOT NULL,
  `Country` varchar(128) NOT NULL DEFAULT '0',
  `Flag` varchar(128) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Eliminarea datelor din tabel `countries`
--

INSERT INTO `countries` (`ID`, `Country`, `Flag`) VALUES
(1, 'Afghanistan', 'af'),
(2, 'Albania', 'ab'),
(3, 'Algeria', 'dz'),
(4, 'Andorra', 'ad'),
(5, 'Angola', 'ao'),
(6, 'Antigua and Barbuda', 'ag'),
(7, 'Argentina', 'ar'),
(8, 'Armenia', 'am'),
(9, 'Australia', 'au'),
(10, 'Austria', 'at'),
(11, 'Azerbaijan', 'az'),
(12, 'Bahamas', 'bs'),
(13, 'Bahrain', 'bh'),
(14, 'Bangladesh', 'bd'),
(15, 'Barbados', 'bb'),
(16, 'Belarus', 'by'),
(17, 'Belgium', 'be'),
(18, 'Belize', 'bz'),
(19, 'Benin', 'bj'),
(20, 'Butan', 'bt'),
(21, 'Bolivia', 'bo'),
(22, 'Bosnia and Herzegovina', 'ba'),
(23, 'Botswana', 'bw'),
(24, 'Brazil', 'br'),
(25, 'Brunei', 'bn'),
(26, 'Bulgaria', 'bg'),
(27, 'Burkina Faso', 'bf'),
(28, 'Burundi', 'bi'),
(29, 'Cambodgia', 'kh'),
(30, 'Cameroon', 'cm'),
(31, 'Canada', 'ca'),
(32, 'Central Africa', 'cf'),
(33, 'Chad', 'td'),
(34, 'Chile', 'cl'),
(35, 'China', 'cn'),
(36, 'Colombia', 'co'),
(37, 'Congo', 'cd'),
(38, 'Costa Rica', 'cr'),
(39, 'Croatia', 'hr'),
(40, 'Cuba', 'cu'),
(41, 'Cyprus', 'cy'),
(42, 'Czech Republic', 'cz'),
(43, 'Denmark', 'dk'),
(44, 'Dominican Republic', 'do'),
(45, 'Ecuador', 'ec'),
(46, 'Egypt', 'eg'),
(47, 'Estonia', 'ee'),
(48, 'Ethiopia', 'et'),
(49, 'Fiji', 'fj'),
(50, 'Finland', 'fi'),
(51, 'France', 'fr'),
(52, 'Georgia', 'ge'),
(53, 'Germany', 'de'),
(54, 'Ghana', 'gh'),
(55, 'Greece', 'gr'),
(56, 'Haiti', 'ht'),
(57, 'Honduras', 'hn'),
(58, 'Hungary', 'hu'),
(59, 'Iceland', 'is'),
(60, 'India', 'in'),
(61, 'Indonesia', 'id'),
(62, 'Iran', 'ir'),
(63, 'Iraq', 'iq'),
(64, 'Ireland', 'ie'),
(65, 'Israel', 'il'),
(66, 'Italy', 'it'),
(67, 'Japan', 'jp'),
(68, 'Kazakhstan', 'kz'),
(69, 'Latvia', 'lv'),
(70, 'Lebanon', 'lb'),
(71, 'Liechtenstein', 'li'),
(72, 'Lithuania', 'lt'),
(73, 'Luxembourg', 'lu'),
(74, 'Malaysia', 'my'),
(75, 'Maldives', 'mv'),
(76, 'Mali', 'ml'),
(77, 'Malta', 'mt'),
(78, 'Mexico', 'mx'),
(79, 'Moldova', 'md'),
(80, 'Monaco', 'mc'),
(81, 'Montenegro', 'me'),
(82, 'Morocco', 'ma'),
(83, 'Netherlands', 'nl'),
(84, 'Nigeria', 'ng'),
(85, 'North Koreea', 'kp'),
(86, 'North Macedonia', 'mk'),
(87, 'Norway', 'no'),
(88, 'Pakistan', 'pk'),
(89, 'Paraguay', 'py'),
(90, 'Peru', 'pe'),
(91, 'Philippines', 'ph'),
(92, 'Poland', 'pl'),
(93, 'Portugal', 'pt'),
(94, 'Qatar', 'qa'),
(95, 'Romania', 'ro'),
(96, 'Russia', 'ru'),
(97, 'San Marino', 'sm'),
(98, 'Saudi Arabia', 'sa'),
(99, 'Senegal', 'sn'),
(100, 'Serbia', 'rs'),
(101, 'Singapore', 'sg'),
(102, 'Slovakia', 'sk'),
(103, 'Slovenia', 'si'),
(104, 'South Africa', 'za'),
(105, 'South Koreea', 'kr'),
(106, 'Spain', 'es'),
(107, 'Sri Lanka', 'lk'),
(108, 'Sudan', 'sd'),
(109, 'Sweden', 'se'),
(110, 'Switzerland', 'ch'),
(111, 'Thailand', 'th'),
(112, 'Tunisia', 'tn'),
(113, 'Turkey', 'tr'),
(114, 'Turkmenistan', 'tm'),
(115, 'Ukraine', 'ua'),
(116, 'UAE', 'ae'),
(117, 'United Kindom', 'gb'),
(118, 'United States of America', 'us'),
(119, 'Uruguay', 'uy'),
(120, 'Uzbekistan', 'uz'),
(121, 'Venezuela', 've'),
(122, 'Vietnam', 'vn'),
(123, 'Yemen', 'ye');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `owner_settings`
--

CREATE TABLE `owner_settings` (
  `id` int(11) NOT NULL,
  `online` varchar(255) CHARACTER SET latin1 NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Eliminarea datelor din tabel `owner_settings`
--

INSERT INTO `owner_settings` (`id`, `online`, `last_updated`) VALUES
(1, '32', '2022-06-24 11:36:22');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_applications`
--

CREATE TABLE `panel_applications` (
  `id` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `UserName` varchar(32) NOT NULL,
  `Answers` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Questions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Status` int(11) NOT NULL DEFAULT 0,
  `Date` timestamp NOT NULL DEFAULT current_timestamp(),
  `ActionBy` varchar(24) NOT NULL DEFAULT 'None',
  `Motiv` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_functions`
--

CREATE TABLE `panel_functions` (
  `funcID` int(11) NOT NULL,
  `funcPlayerID` int(13) NOT NULL,
  `funcColor` varchar(32) NOT NULL,
  `funcIcon` varchar(32) NOT NULL,
  `funcName` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_groups`
--

CREATE TABLE `panel_groups` (
  `groupID` int(11) NOT NULL,
  `groupAdmin` int(13) NOT NULL,
  `groupColor` varchar(32) NOT NULL,
  `groupName` varchar(32) NOT NULL,
  `groupFlags` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Eliminarea datelor din tabel `panel_groups`
--

INSERT INTO `panel_groups` (`groupID`, `groupAdmin`, `groupColor`, `groupName`, `groupFlags`) VALUES
(16, 6, 'red', 'Owner', 'abcdefghijklmnopqrstu'),
(18, 1, '#36FFFF', 'Helper', 'bx'),
(19, 2, 'orange', 'Administrator', 'bcefgijmnpqu'),
(21, 3, 'green', 'Moderator', 'bcdefgijkmnopqu'),
(22, 5, 'purple', 'God', 'abcdefgijklmnopqrstu'),
(23, 4, '#f23f44', 'Semi-God', 'bcdefgijkmnopqsu'),
(24, 0, 'gray', 'User', 'z'),
(25, 22, '#3600FF', 'Founder', 'abcdefghijklmnopqrstu');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_logs`
--

CREATE TABLE `panel_logs` (
  `logID` int(11) NOT NULL,
  `logText` varchar(256) NOT NULL,
  `logBy` int(11) NOT NULL,
  `logIP` varchar(256) NOT NULL,
  `logDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_news`
--

CREATE TABLE `panel_news` (
  `id` int(11) NOT NULL,
  `text` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin` int(11) NOT NULL,
  `title` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_notifications`
--

CREATE TABLE `panel_notifications` (
  `ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `UserName` varchar(32) NOT NULL,
  `Notification` text NOT NULL,
  `FromID` int(11) NOT NULL,
  `FromName` varchar(32) NOT NULL,
  `Seen` int(11) NOT NULL DEFAULT 0,
  `Link` text NOT NULL,
  `Date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_questions`
--

CREATE TABLE `panel_questions` (
  `id` int(11) NOT NULL,
  `question` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_recovery`
--

CREATE TABLE `panel_recovery` (
  `RecoverKey` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_reply_admin_topics`
--

CREATE TABLE `panel_reply_admin_topics` (
  `replyID` int(11) NOT NULL,
  `replyAdminID` int(11) NOT NULL,
  `replyPlayerID` int(11) NOT NULL,
  `replyText` text NOT NULL,
  `replyDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_reply_suggestions`
--

CREATE TABLE `panel_reply_suggestions` (
  `replyID` int(11) NOT NULL,
  `replySuggestionID` int(11) NOT NULL,
  `replyPlayerID` int(11) NOT NULL,
  `replyText` text NOT NULL,
  `replyDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_reply_unbans`
--

CREATE TABLE `panel_reply_unbans` (
  `replyID` int(11) NOT NULL,
  `replyUnbanID` int(11) NOT NULL,
  `replyPlayerID` int(11) NOT NULL,
  `replyText` text NOT NULL,
  `replyDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_settings`
--

CREATE TABLE `panel_settings` (
  `ID` int(11) NOT NULL,
  `IPLoginVerify` int(11) NOT NULL,
  `Maintenance` int(11) NOT NULL,
  `ServersOfTheWeek` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `AdminApp` int(11) NOT NULL DEFAULT 0,
  `SuggestionApp` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Eliminarea datelor din tabel `panel_settings`
--

INSERT INTO `panel_settings` (`ID`, `IPLoginVerify`, `Maintenance`, `ServersOfTheWeek`, `AdminApp`, `SuggestionApp`) VALUES
(1, 0, 0, '<ul>\n	<li><span style=\"color:#ffffff;\"><strong>In primul rand, pentru a juca trebuie neaparat sa ai sunet, pentru ca tot jocul se bazeaza pe comenzile </strong></span><span style=\"color:#66ffff;\"><strong>Jeanului</strong></span><span style=\"color:#ffffff;\"><strong>.</strong></span></li>\n	<li><span style=\"color:#ff0000;\"><strong>Jean</strong></span><span style=\"color:#ffffff;\"><strong> este diferit in fiecare runda si apartine echipei CT. Modul de joc se bazeaza pe jocul \"simon spune\" sau \"simon says\" in care principalul scop este sa asculti orice comanda spusa de catre </strong></span><span style=\"color:#ff0000;\"><strong>Jean</strong></span><span style=\"color:#ffffff;\"><strong>, dar doar daca comanda incepe cu \"</strong></span><span style=\"color:#ff0000;\"><strong>Jean spune...</strong></span><span style=\"color:#ffffff;\"><strong>\".</strong></span></li>\n	<li><span style=\"color:#ffffff;\"><strong>Daca comanda nu incepe cu jean spune, voi trebuie sa o ignorati, si sa respectati ultima comanda data.</strong></span></li>\n	<li><span style=\"color:#ffffff;\"><strong>Scopul principal ca si Prizonier este sa ramai ultimul in viata, sau sa evadezi(sa omori toti gardienii). Jeanul va da comenzi prin care, rand pe rand, cei mai \"inceti\" prizonieri vor fi omorati. Cateva exemple de astfel de comenzi:</strong></span></li>\n	<li><span style=\"color:#ffffff;\"><strong>O data de ati ramas ultimul prizonier, va apare un meniu de Duel, care poate fi accesat si prin comanda in chat /lr , de unde puteti alege fie duel cu fiecare gardian pe rand, sau </strong></span><span style=\"color:#66ff00;\"><strong>freeday</strong></span><span style=\"color:#ffffff;\"><strong>.</strong></span></li>\n</ul>\n', 1, 1);

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_shop`
--

CREATE TABLE `panel_shop` (
  `itemID` int(11) NOT NULL,
  `itemName` text NOT NULL,
  `itemPrice` text NOT NULL,
  `itemText` text NOT NULL,
  `itemMethod` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_suggestions`
--

CREATE TABLE `panel_suggestions` (
  `id` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `UserName` varchar(32) NOT NULL,
  `Answers` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Questions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Status` int(11) NOT NULL DEFAULT 0,
  `Date` timestamp NOT NULL DEFAULT current_timestamp(),
  `ActionBy` varchar(24) NOT NULL DEFAULT 'None',
  `Motiv` text NOT NULL,
  `Hide` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_suggestion_questions`
--

CREATE TABLE `panel_suggestion_questions` (
  `id` int(11) NOT NULL,
  `question` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_suspend`
--

CREATE TABLE `panel_suspend` (
  `spID` int(11) NOT NULL,
  `spAdmin` int(11) NOT NULL,
  `spPlayer` int(11) NOT NULL,
  `spReason` varchar(125) NOT NULL,
  `spDays` int(50) NOT NULL,
  `spDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_topics`
--

CREATE TABLE `panel_topics` (
  `id` int(11) NOT NULL,
  `Topic` text NOT NULL,
  `Date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Eliminarea datelor din tabel `panel_topics`
--

INSERT INTO `panel_topics` (`id`, `Topic`, `Date`) VALUES
(1, '<p style=\"text-align:center;\"><strong>Serverul a suferit un mic downtime astazi intre orele 12:00 si 14:00.</strong></p>\n', '2022-02-22 21:42:24'),
(2, '<h3><span style=\"font-size:20px;\">Informatii deschidere ticket ajutor</span></h3>\n\n<p><span style=\"font-size:18px;\"><small>Inainte de a deschide un tichet, citeste raspunsurile intrebarilor puse frecvent. De obicei se raspunde la tichete in maxim 24 de ore. Incercati sa spuneti clar ce problema aveti in tichet si sa dati detalii despre problema avuta.</small></span></p>\n', '2019-02-14 17:53:58'),
(3, 'Informatii deschidere cerere de unban\n', '2022-04-28 13:19:21'),
(4, '<div style=\"text-align:center\"><span style=\"color:#2980b9\"><strong>&nbsp;Itemele vor fi acordate imediat dupa verificarea platii.</strong></span></div>\r\n\r\n<div style=\"text-align:center\"><span style=\"color:#2980b9\"><strong>In cazul refund-ului pe Paypal contul in cauza va fi banat permanent pe server/forum. Odata ce ai donat pe server ti-ai asumat aceasta responsabilitate.</strong></span></div>\r\n\r\n<div style=\"text-align:center\"><span style=\"color:#2980b9\"><strong>Pentru informatii suplimentare despre un anumit item din shop, apasati click pe numele acestuia.</strong></span></div>\r\n', '2022-04-28 12:49:58'),
(5, '<p><span style=\"color:#2980b9\"><strong>Contact Owner: Syko</strong></span></p>\r\n\r\n<ul>\r\n	<li><span style=\"color:#2980b9\"><strong>Steam: Syko # JB.B-ZONE.RO (syko_o)</strong></span></li>\r\n	<li><span style=\"color:#2980b9\"><strong>Discord: Syko#0472</strong></span></li>\r\n</ul>\r\n\r\n<p><span style=\"color:#2980b9\"><strong>Donatiile se pot face prin:&nbsp;</strong></span></p>\r\n\r\n<ul>\r\n	<li><span style=\"color:#2980b9\"><strong>Paypal</strong></span></li>\r\n	<li><span style=\"color:#2980b9\"><strong>Paysafe (PIN)</strong></span></li>\r\n	<li><span style=\"color:#2980b9\"><strong>Skrill.</strong></span></li>\r\n	<li><span style=\"color:#2980b9\"><strong>Revolut.</strong></span></li>\r\n</ul>\r\n', '2021-05-08 13:14:16'),
(6, '<p style=\"text-align:center;\"><span style=\"font-size:18px;\"><strong><span style=\"color:#ff0000;\">Server Rules</span></strong></span></p>\n\n<p style=\"text-align:center;\"> </p>\n\n<ol>\n	<li><strong>Nu aveti voie sa injurati.</strong></li>\n	<li><strong>Nu aveti voie sa vorbiti urat.</strong></li>\n	<li><strong>Nu aveti voie sa folositi coduri.</strong></li>\n</ol>\n\n<p style=\"text-align:center;\"> </p>\n', '2022-04-28 12:39:47'),
(7, '<p>dsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsadsa</p>\n', '2022-04-28 12:44:03'),
(8, '<p>Informatii sugestii</p>\n', '2022-04-29 14:50:27'),
(9, '<p><strong>Orice aplicatie facuta in bataie de joc este automat <span style=\"color:#ff0000;\">respinsa</span>!</strong></p>\n', '2022-04-29 14:50:27');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_unbans`
--

CREATE TABLE `panel_unbans` (
  `id` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `UserName` varchar(32) NOT NULL,
  `Answers` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Questions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Status` int(11) NOT NULL DEFAULT 0,
  `Date` timestamp NOT NULL DEFAULT current_timestamp(),
  `ActionBy` varchar(24) NOT NULL DEFAULT 'None',
  `Motiv` text NOT NULL,
  `Hide` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_unban_questions`
--

CREATE TABLE `panel_unban_questions` (
  `id` int(11) NOT NULL,
  `question` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `panel_updates`
--

CREATE TABLE `panel_updates` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `text` text NOT NULL,
  `textshort` varchar(300) NOT NULL,
  `admin` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexuri pentru tabele eliminate
--

--
-- Indexuri pentru tabele `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `advanced_bans`
--
ALTER TABLE `advanced_bans`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`ID`);

--
-- Indexuri pentru tabele `owner_settings`
--
ALTER TABLE `owner_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `setting` (`online`,`last_updated`);

--
-- Indexuri pentru tabele `panel_applications`
--
ALTER TABLE `panel_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `panel_functions`
--
ALTER TABLE `panel_functions`
  ADD PRIMARY KEY (`funcID`);

--
-- Indexuri pentru tabele `panel_groups`
--
ALTER TABLE `panel_groups`
  ADD PRIMARY KEY (`groupID`);

--
-- Indexuri pentru tabele `panel_logs`
--
ALTER TABLE `panel_logs`
  ADD PRIMARY KEY (`logID`);

--
-- Indexuri pentru tabele `panel_news`
--
ALTER TABLE `panel_news`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `panel_notifications`
--
ALTER TABLE `panel_notifications`
  ADD PRIMARY KEY (`ID`);

--
-- Indexuri pentru tabele `panel_questions`
--
ALTER TABLE `panel_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `panel_recovery`
--
ALTER TABLE `panel_recovery`
  ADD PRIMARY KEY (`RecoverKey`);

--
-- Indexuri pentru tabele `panel_reply_admin_topics`
--
ALTER TABLE `panel_reply_admin_topics`
  ADD PRIMARY KEY (`replyID`);

--
-- Indexuri pentru tabele `panel_reply_suggestions`
--
ALTER TABLE `panel_reply_suggestions`
  ADD PRIMARY KEY (`replyID`);

--
-- Indexuri pentru tabele `panel_reply_unbans`
--
ALTER TABLE `panel_reply_unbans`
  ADD PRIMARY KEY (`replyID`);

--
-- Indexuri pentru tabele `panel_settings`
--
ALTER TABLE `panel_settings`
  ADD PRIMARY KEY (`ID`);

--
-- Indexuri pentru tabele `panel_shop`
--
ALTER TABLE `panel_shop`
  ADD PRIMARY KEY (`itemID`);

--
-- Indexuri pentru tabele `panel_suggestions`
--
ALTER TABLE `panel_suggestions`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `panel_suggestion_questions`
--
ALTER TABLE `panel_suggestion_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `panel_suspend`
--
ALTER TABLE `panel_suspend`
  ADD PRIMARY KEY (`spID`);

--
-- Indexuri pentru tabele `panel_topics`
--
ALTER TABLE `panel_topics`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `panel_unbans`
--
ALTER TABLE `panel_unbans`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `panel_unban_questions`
--
ALTER TABLE `panel_unban_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `panel_updates`
--
ALTER TABLE `panel_updates`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pentru tabele eliminate
--

--
-- AUTO_INCREMENT pentru tabele `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `advanced_bans`
--
ALTER TABLE `advanced_bans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `owner_settings`
--
ALTER TABLE `owner_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pentru tabele `panel_applications`
--
ALTER TABLE `panel_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `panel_functions`
--
ALTER TABLE `panel_functions`
  MODIFY `funcID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `panel_groups`
--
ALTER TABLE `panel_groups`
  MODIFY `groupID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pentru tabele `panel_logs`
--
ALTER TABLE `panel_logs`
  MODIFY `logID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `panel_news`
--
ALTER TABLE `panel_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `panel_notifications`
--
ALTER TABLE `panel_notifications`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `panel_questions`
--
ALTER TABLE `panel_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `panel_reply_admin_topics`
--
ALTER TABLE `panel_reply_admin_topics`
  MODIFY `replyID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `panel_reply_suggestions`
--
ALTER TABLE `panel_reply_suggestions`
  MODIFY `replyID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `panel_reply_unbans`
--
ALTER TABLE `panel_reply_unbans`
  MODIFY `replyID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `panel_settings`
--
ALTER TABLE `panel_settings`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pentru tabele `panel_shop`
--
ALTER TABLE `panel_shop`
  MODIFY `itemID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `panel_suggestions`
--
ALTER TABLE `panel_suggestions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `panel_suggestion_questions`
--
ALTER TABLE `panel_suggestion_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `panel_suspend`
--
ALTER TABLE `panel_suspend`
  MODIFY `spID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pentru tabele `panel_unbans`
--
ALTER TABLE `panel_unbans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `panel_unban_questions`
--
ALTER TABLE `panel_unban_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `panel_updates`
--
ALTER TABLE `panel_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
