<?php
/**
 * php/includes/session.inc.php
 *
 * MySQL Session Handler.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 *
 * @global \PDO $dbh
 * @global \stdClass $dbhResult
 * @global array $configuration Konfigurationsdaten
 */
namespace HaHeLa;
use \HaHeLa\Modules_Model_Session as Session;


// Session-Name setzen.
session_name($configuration['session']['name']);


// Optionale Session-Konfiguration
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', $configuration['session']['samesite']); 
ini_set('session.cookie_secure', $configuration['session']['secure']); 
ini_set('session.use_strict_mode', '1');


// Handler registrieren – MUSS vor session_start() geschehen
$options = array();
$handler = new Session($dbh, $configuration, $options, 3600); // 60 Minuten
session_set_save_handler($handler, true);


// Session starten.
session_start();
