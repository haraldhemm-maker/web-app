<?php
/**
 * php/inc/database.inc.php
 *
 * Open/close connection to database server and database.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 *
 * @global array $configuration Konfigurationsdaten
 * @global reference $dbh
 * @global stdClass $dbhResult
 * @throws PDOException
 */
$dbh = null;
$dbhResult = null;
openDatabase();


/**
 * Offnet Datenbankverbindung.
 *
 * @global array $configuration Konfigurationsdaten.
 * @global reference $dbh PDO-Objekt.
 * @global stdClass $dbhResult Information zur Datenbankverbindung.
 * @return void
 */
function openDatabase ()
{
    global $configuration, $dbh, $dbhResult;
    $dataSourceName = "mysql:host={$configuration['DATABASE']['SERVERNAME']};dbname={$configuration['DATABASE']['DATABASENAME']}";
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_PERSISTENT => true, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8' COLLATE 'utf8_general_ci'"];
    $dbhResult = new stdClass();
    try {
        $dbh = new PDO($dataSourceName, $configuration['DATABASE']['USERNAME'], $configuration['DATABASE']['PASSWORD'], $options);
        // set the PDO error mode to exception
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbhResult->result = true;
        $dbhResult->code = 0;
        $dbhResult->message = 'Eine Verbindung zum Datenbankserver und zur Datenbank konnte hergestellt werden. ';
    }
    catch(PDOException $e) {
        $dbh = null;
        $dbhResult->result = false;
        $dbhResult->code = $e->getCode();
        $dbhResult->message = 'Eine Verbindung zum Datenbankserver bzw. zur Datenbank konnte NICHT hergestellt werden: ' . $e->getMessage();
    }
}


/**
 * Schliesst Datenbankverbindung.
 *
 * @global reference $dbh PDO-Objekt.
 * @global stdClass $dbhResult Information zur Datenbankverbindung.
 * @return void
 */
function closeDatabase ()
{
    global $dbh, $dbhResult;
    $dbh = null;
    $dbhResult = null;
}
