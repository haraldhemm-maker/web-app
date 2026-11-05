-- ============================================================
-- wa_security_type – CFI-konforme Fassung (ISO 10962:2019)
-- Datenbank: hahela_web_app
--
-- Aufbau des CFI-Codes (6 Zeichen):
--   1. Zeichen = Kategorie
--   2. Zeichen = Gruppe
--   3.-6. Zeichen = Attribute
-- In dieser Tabelle werden Kategorie und Gruppe (2 Zeichen)
-- als cfi_code geführt; die 4 Attributzeichen können später
-- je Instrument hinzugefügt werden.
--
-- CFI-Kategorien (ISO 10962:2019, 1. Zeichen):
--   E Equities (Anteile)
--   C Collective Investment Vehicles (Fonds)
--   D Debt Instruments (Schuldverschreibungen)
--   R Entitlements (Bezugsrechte, Optionsscheine)
--   O Listed Options (börsengehandelte Optionen)
--   F Futures
--   H Non-listed and complex listed options
--   I Spot
--   J Forwards
--   K Strategies
--   L Financing
--   S Swaps
--   T Referential Instruments (Indizes, Referenzwerte)
--   M Others (misc.)
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Tabelle `wa_security_type`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `wa_security_type`;

CREATE TABLE `wa_security_type` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Identifikationsnummer',
  `code` varchar(31) NOT NULL COMMENT 'Anwendungsschlüssel (kleingeschrieben)',
  `cfi_code` char(2) DEFAULT NULL COMMENT 'CFI-Code (ISO 10962): Kategorie (1. Zeichen) + Gruppe (2. Zeichen); NULL = keine CFI-Abbildung',
  `name_en` varchar(63) NOT NULL COMMENT 'Englische Bezeichnung',
  `name_de_singular` varchar(63) NOT NULL COMMENT 'Wertpapierbezeichnung in Deutsch (Einzahl)',
  `name_de_plural` varchar(63) NOT NULL COMMENT 'Wertpapierbezeichnung in Deutsch (Mehrzahl)',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Sortierreihenfolge',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`),
  KEY `idx_cfi_code` (`cfi_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Wertpapier-/Instrumententypen (CFI-konform, ISO 10962)';

-- --------------------------------------------------------
-- Daten für Tabelle `wa_security_type`
-- --------------------------------------------------------

INSERT INTO `wa_security_type` (`id`, `code`, `cfi_code`, `name_en`, `name_de_singular`, `name_de_plural`, `sort`) VALUES
-- 1. Equities (CFI-Kategorie E)
(1,  'indefinite', NULL,   'unknown',                'unbestimmt',                    'unbestimmt',                     1),
(2,  'stock',      'ES',   'common/ordinary share',  'Aktie',                         'Aktien',                         2),
(3,  'equity',     'E',    'equity',                 'Anteilsinstrument',             'Anteilsinstrumente',             3),
(4,  'preferred',  'EP',   'preference share',       'Vorzugsaktie',                 'Vorzugsaktien',                  4),
(5,  'reit',       'CB',   'real estate investment trust', 'REIT - Immobilien-Anlagegesellschaft', 'REITs - Immobilien-Anlagegesellschaften', 5),

-- 2. Collective Investment Vehicles (CFI-Kategorie C)
(6,  'fund',         'CI', 'investment fund',        'Investmentfonds',              'Investmentfonds',               10),
(7,  'etf',          'CE', 'exchange traded fund',   'ETF - börsengehandelter Indexfonds', 'ETFs - börsengehandelte Indexfonds', 11),
(8,  'hedge_fund',   'CH', 'hedge fund',             'Hedgefonds',                   'Hedgefonds',                    12),
(9,  'fof',          'CF', 'fund of funds',           'Dachfonds',                    'Dachfonds',                     13),

-- 3. Debt Instruments (CFI-Kategorie D)
(10, 'bond',        'DB',   'bond',                   'Anleihe',                      'Anleihen',                      20),
(11, 'convertible', 'DC',   'convertible bond',       'Wandelschuldverschreibung',    'Wandelschuldverschreibungen',  21),
(12, 'mtn',         'DT',   'medium-term note',       'Medium-Term-Note (MTN)',      'Medium-Term-Notes (MTNs)',     22),
(13, 'etn',         'DT',   'exchange traded note',  'ETN - börsengehandelte Inhaberschuldverschreibung', 'ETNs - börsengehandelte Inhaberschuldverschreibungen', 23),
(14, 'etc',         'DT',   'exchange traded commodity', 'ETC - börsengehandelter Rohstoff-Zertifikat', 'ETCs - börsengehandelte Rohstoff-Zertifikate', 24),
(15, 'money_market','DY',   'money market instrument','Geldmarktinstrument',         'Geldmarktinstrumente',         25),
(16, 'mbs',         'DG',   'mortgage-backed security','Mortgage-Backed Security (MBS)', 'Mortgage-Backed Securities (MBS)', 26),
(17, 'abs',         'DA',   'asset-backed security',  'Asset-Backed Security (ABS)', 'Asset-Backed Securities (ABS)', 27),

-- 4. Entitlements (CFI-Kategorie R)
(18, 'warrant',     'RW',   'warrant',                'Optionsschein',                'Optionsscheine',                40),
(19, 'right',       'RS',   'subscription right',     'Bezugsrecht',                  'Bezugsrechte',                  41),

-- 5. Optionen und Futures (CFI-Kategorien O, F, H, J)
(20, 'option',      'O',    'listed option',          'Option - börsengehandelt',     'Optionen - börsengehandelt',   50),
(21, 'future',      'F',    'future',                 'Future - Terminkontrakt',     'Futures - Terminkontrakte',    51),
(22, 'forward',     'J',    'forward',                'Forward - Termingeschäft',     'Forwards - Termingeschäfte',   52),

-- 6. Zertifikate / strukturierte Produkte (CFI: E-Y bzw. D-S/D-E)
(23, 'certificate', 'EY',   'structured instrument (participation)', 'Zertifikat', 'Zertifikate',             60),

-- 7. Referential Instruments (CFI-Kategorie T) – keine Wertpapiere!
(24, 'index',       'TI',   'index',                  'Index',                        'Indizes',                       70),
(25, 'benchmark',   'T',    'benchmark',              'Vergleichsmaßstab',            'Vergleichsmaßstäbe',            71),

-- 8. Sonstige (CFI-Kategorie M) – keine Wertpapiere im engeren Sinn
(26, 'crypto',      'M',    'crypto asset',           'Kryptowährung',                'Kryptowährungen',               90),
(27, 'other',       'M',    'other',                  'Sonstiges',                    'Sonstiges',                      99);

-- ============================================================
-- Hinweise zur Migration von Ihrer bisherigen Tabelle:
--
--  code (alt)   -> code (neu)   Begründung
--  ----------     ------------  -------------------------------
--  stock         -> stock       CFI-Gruppe ES (Stammaktie) bleibt
--  equity         -             Doppelung zu stock entfernt;
--                                'equity' ist jetzt die übergeordnete
--                                CFI-Kategorie E
--  etf            -> etf        CFI-Gruppe CE
--  fund           -> fund       CFI-Gruppe CI (Investmentfonds);
--                                deutsch "Fond(s)" durch "Investmentfonds"
--                                ersetzt ("Fonds" ist Singular UND Plural)
--  bond           -> bond       CFI-Gruppe DB
--  certificate    -> certificate CFI-Gruppe EY (strukturiertes Produkt
--                                mit Partizipation); Bonus-/Discount-/
--                                Faktor-Zertifikate weiter differenzierbar
--  warrant        -> warrant    CFI-Gruppe RW
--  index          -> index      CFI-Kategorie T, Gruppe I
--                                (Referential Instrument, kein Wertpapier)
--  benchmark      -> benchmark  CFI-Kategorie T; als Typ belassen, aber
--                                kein Wertpapier
--  etc            -> etc       CFI: als Medium-Term-Note (DT) emittiert
--  etn            -> etn       CFI: Medium-Term-Note (DT)
--  future         -> future    CFI-Kategorie F
--  option         -> option    CFI-Kategorie O
--  money_market   -> money_market CFI-Gruppe DY
--                                (Spalte code von "money_market" auf
--                                max. 31 Zeichen erweitert)
--  reit           -> reit      CFI-Gruppe CB (unter CIV)
--  crypto         -> crypto    Nicht Bestandteil von ISO 10962:2019;
--                                auf Kategorie M (Others) abgebildet
--
-- Neue Typen: preferred, hedge_fund, fof, convertible, mtn, mbs,
--            abs, right, forward, other
--
-- Typkonvention: `code` ist bewusst als varchar statt enum definiert,
-- damit neue Typen ohne Schemaänderung ergänzt werden können.
-- Die 4 CFI-Attributzeichen (Position 3-6) sind typunabhängig und
-- gehören in eine Instrumententabelle (z. B. wa_security), nicht hier.
-- ============================================================

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
