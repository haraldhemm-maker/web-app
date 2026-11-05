-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Erstellungszeit: 05. Nov 2026 um 08:38
-- Server-Version: 10.11.14-MariaDB-0ubuntu0.24.04.1
-- PHP-Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `hahela_web_app`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wa_security_type`
--

CREATE TABLE `wa_security_type` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Identifikationsnummer',
  `code` enum('indefinite','bond','equity','certificate','etf','fund','stock','warrant','benchmark','index','etc','etn','future','option','money_market','reit','crypto') NOT NULL DEFAULT 'equity' COMMENT 'Wertpapierschlüssel',
  `name_en` varchar(63) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Englische Bezeichnung',
  `name_de_singular` varchar(63) NOT NULL COMMENT 'Wertpapierbezeichnung in Deutsch (Einzahl)',
  `name_de_plural` varchar(63) NOT NULL COMMENT 'Wertpapierbezeichnung in Deutsch (Mehrzahl)'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Wertpapiertypen';

--
-- Daten für Tabelle `wa_security_type`
--

INSERT INTO `wa_security_type` (`id`, `code`, `name_en`, `name_de_singular`, `name_de_plural`) VALUES
(1, 'indefinite', 'unknown', 'unbestimmt', 'unbestimmt'),
(2, 'stock', 'stock', 'Aktie', 'Aktien'),
(3, 'equity', 'equity', 'Aktie', 'Aktien'),
(4, 'bond', 'bond', 'Anleihe', 'Anleihen'),
(5, 'etf', 'exchange traded fund', 'ETF - börsengehandelter Indexfond', 'ETFs - börsengehandelte Indexfonds'),
(6, 'fund', 'fund', 'Fond', 'Fonds'),
(7, 'certificate', 'certificate', 'Zertifikat', 'Zertifikate'),
(8, 'warrant', 'warrant', 'Optionsschein', 'Optionsscheine'),
(9, 'benchmark', 'benchmark', 'Vergleichsmaßstab', 'Vergleichsmaßstäbe'),
(10, 'index', '', 'Index', 'Indizes'),
(11, 'etc', 'exchange traded commodity', 'ETC - börsengehandelter Rohstoffindex', 'ETCs - börsengehandelte Rohstoffindizes'),
(12, 'etn', 'exchange traded note', 'ETN - börsengehandelte Inhaberschuldverschreibung', 'ETNs - börsengehandelte Inhaberschuldverschreibungen'),
(13, 'future', 'future', 'Future – Terminkontrakt', 'Futures – Terminkontrakte'),
(14, 'option', 'option', 'Option – börsengehandelte Option', 'Options – börsengehandelte Optionen'),
(15, 'money_market', 'money market instrument', 'Money market instrument - Geldmarktinstrument', 'Money market instruments - Geldmarktinstrumente'),
(16, 'reit', 'real estate investment trust', 'Estate Investment Trust - Immobilien-Anlagegesellschaft', 'Estate Investment Trusts - Immobilien-Anlagegesellschaften'),
(17, 'crypto', 'Crypto', 'Kryptowährung', 'Kryptowährungen');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `wa_security_type`
--
ALTER TABLE `wa_security_type`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `wa_security_type`
--
ALTER TABLE `wa_security_type`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identifikationsnummer', AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
