<?php


declare(strict_types=1);


if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}


// Fully Qualified File Name of the configuration file.
// (vollqualifizierter Dateiname: Pfad + Dateihauptname + Dateinameerweiterung)
$fqfn = '..' . DS . 'dat' . DS . 'configuration-' 
    . strtolower(gethostname())
    .'-'.strtolower(php_uname('s'))
    .'.ini';
    
$config = parse_ini_file($fqfn, true);

// Vertrauliche Sektionen der Konfiguration.
$confidentialSections = array(
    'DATABASE'
);

// Daten der vertraulichen Sektionen aus der Konfiguration entfernen.
foreach ($confidentialSections as $section) {
    unset($config[$section]);
}

// Ausgabe 
header("HTTP/1.1 200 OK");
header("Content-Type: application/json; charset=utf-8");
print json_encode($config);
print PHP_EOL;
