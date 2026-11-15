<?php


/**
 * php/services/bloomberg_exchange_code_to_mic_mapping.php
 * - Laedt Bloomberg Exchange Codes und MICS aus einer CSV-Datei in die 
 *      Datenbanktabelle "hahela_web_app.wa_bloomberg_exchcode_to_mic".
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license HaHeLa CC BY-NC-ND
 * @version 1.0.0
 * @since 1.0.0
 * 
 * Quelle: 
 *  Bloomberg-Exchange-Code-to-MIC-Mapping.xlsx 
 *  https://insights.ultumus.com/bloomberg-exchange-code-to-mic-mapping
 *
 * Run in browser:
 * http://localhost/~harald/app/web-app/php/services/bloomberg_exchange_codes_to_mics.php
 * Run in console:
 * cd ~harald/public_html/app/web-app/php/services
 * php bloomberg_exchange_codes_to_mics.php
 */


declare(strict_types=1);


if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}


// Name der Datenbanktabelle (ohne Prefix) und Name der CSV-Datei.
define('DB_TABLE_NAME', 'bloomberg_exchcode_to_mic ');
define('CSV_FILE_NAME', 'Bloomberg-Exchange-Code-to-MIC-Mapping.csv');

// Konfiguration laden.
$configFileName = 'configuration-'.strtolower(gethostname()).'-'.strtolower(php_uname('s')).'.ini';
$configuration = parse_ini_file('..'.DS.'..'.DS.'dat'.DS.$configFileName, true);


// Datenbank initialisieren,
include_once '..'.DS.'inc'.DS.'database.inc.php';

// Laden der SQL-DB-Klasse inklusive Interface.
include_once '..'.DS.'modules'.DS.'interface'.DS.'sqldb.php';
include_once '..'.DS.'modules'.DS.'model'.DS.'sqldb.php';


$csvFilename = '.'.DS.'dat'.DS.CSV_FILE_NAME;


//
// ***************************** Daten aufbereiten und zur Verfuegung stellen.
//


/**
 * Holt alle Spaltennamen einer Datenbanktabelle.
 * 
 * @global object $dbh Datenbank-Handle.
 * @global array $configuration Assoziatives Feld mit Konfigurationsdaten.
 * @param string $tabName Name der Datenbanktabelle deren Spaltennamen 
 *  geholt werden.
 * @return array Indexiertes Feld mit allen Spaltennamen einer 
 *  Datenbanktabelle.
 */
function getDbColNames(string $tabName): array
{
    global $dbh, $configuration;
    $tabColNames = array();
    $sqldb = new  HaHeLa\SqldbModel($dbh, $configuration, $options = array());
    $tabDescriptions = $sqldb->describeTable($tabName);
    foreach ($tabDescriptions as $tabDescription) {
        if ('id' !== $tabDescription['COLUMN_NAME']) {
            $tabColNames[] = $tabDescription['COLUMN_NAME'];
        }
    }
    return $tabColNames;
}


/**
 * Holt alle Spaltennamen einer CSV-Datei.
 * 
 * @param string $fileName Dateiname der CSV-Datei (inkl. Pfadangabe).
 * @return array Indexiertes Feld mit allen Spaltennamen einer 
 *  CSV-Datei.
 */
function getCsvColNames(string $fileName): array
{
    $csvColNames = array();
    if (FALSE !== ($fh = fopen($fileName, 'r'))) {
        if (FALSE !== ($rowData = fgetcsv($fh, 1000, ';'))) {
            //
            // Kopfzeile
            //
            $csvColNames = $rowData;
        }
        fclose($fh);
    }
    return $csvColNames;
}


try {
    //
    // Spaltennamen in der Datenbanktabelle mit den Spaltennamen in der 
    //  CSV-Datei vergleiche. Wenn es Unterschiede in den Spaltennamen gibt, 
    //  dann wird eine Exception ausgeloest.
    // 
    $dbColNames = getDbColNames('wa_'.DB_TABLE_NAME);
    $csvColNames = getCsvColNames($csvFilename);
    $diffs = array_diff($csvColNames, $dbColNames);
    if (0 < count($diffs)) {
        $msg  = 'Die folgenden Spalten in der CSV-Datei sind NICHT';
        $msg .= ' in der Datenbanktabelle \"wa_bloomberg_exchcode_to_mic\"'; 
        $msg .= ' enthalten.';
        foreach($diffs as $diff) {
            $msg .= "\n$diff";
        }
        throw new \Exception($msg . "\n");
    }
    
    //
    // Datenbanktabelle wa_bloomberg_exchcode_to_mic leeren.
    //
    $query  = "TRUNCATE TABLE {$configuration['DATABASE']['DATABASENAME']}.{$configuration['DATABASE']['TABLE_PREFIX']}".DB_TABLE_NAME;
    $stmt = $dbh->prepare($query);
    $return = $stmt->execute();

    //
    // CSV-Daten in die Datenbank zeilenweise importieren.
    //
    if (($fh = fopen($csvFilename, 'r')) !== false) {
        $firstPass = true;
        while (($row = fgetcsv($fh, 1000, ";")) !== false) {
            if ($firstPass) {
                $firstPass = false;
            }
            else {
                for ($i = 0; $i < count($row); $i++) {
                    $row[$i] = trim($row[$i]);
                }
                $query  = 'INSERT INTO wa_'.DB_TABLE_NAME.' (';
                $query .= implode(',', $dbColNames);
                $query .= ') VALUES (?,?,?,?,?,?,?,?)';
                $stmt = $dbh->prepare($query);
                $result = $stmt->execute($row);
            }
        }
        fclose($fh);
    }
        
}
catch(\Exception $e) {
    echo 'Error: ' . $e->getMessage();
}


echo "\n", 'Done' . "\n";

