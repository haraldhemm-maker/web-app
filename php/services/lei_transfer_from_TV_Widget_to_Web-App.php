<?php 
/**
 * Konstanten
 */
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
define('CONFIGURATION_PATH', '..'.DS.'..'.DS.'dat'); // OHNE terminierendem Backslash bzw. Slash
define('CONFIGURATION_BASEFILE_NAME_TEMPLATE', 'configuration-rappelkiste-*.ini');        


function insertArray(\PDO $pdo, string $table, array $data): void
{
    $columns      = array_keys($data);
    $placeholders = array_map(fn($col) => ':' . $col, $columns);

    $sql = sprintf(
        'INSERT INTO %s (%s) VALUES (%s)',
        $table,
        implode(', ', $columns),
        implode(', ', $placeholders)
    );

    $stmt = $pdo->prepare($sql);
    $stmt->execute($data); // Schlüssel entsprechen den Platzhaltern
}


// Konfiguration laden.
$osName = php_uname('s');
$fileName = str_replace('*', strtolower($osName), CONFIGURATION_BASEFILE_NAME_TEMPLATE);
$fileName = CONFIGURATION_PATH.DS.$fileName;
$config = parse_ini_file($fileName, true);

// Verbindung mit Datenbankserver herstellen und Datenbank oeffnen.
$dbhResult = new \stdClass();
$dataSourceName = "mysql:host={$config['DATABASE']['SERVERNAME']};dbname={$config['DATABASE']['DATABASENAME']}";
try {
  $dbh = new \PDO($dataSourceName, $config['DATABASE']['USERNAME'], $config['DATABASE']['PASSWORD']);
  // set the PDO error mode to exception
  $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
  $dbhResult->result = true;
  $dbhResult->code = 0;
  $dbhResult->message = 'Eine Verbindung zum Datenbankserver und zur Datenbank konnte hergestellt werden. ';
}
catch(\PDOException $e) {
  $dbhResult->result = false;
  $dbhResult->code = $e->getCode();
  $dbhResult->message = 'Eine Verbindung zum Datenbankserver bzw. zur Datenbank konnte NICHT hergestellt werden: ' . $e->getMessage();
}
if (false === $dbhResult->result) {
  die($dbhResult->message . $dbhResult->code);
}


echo "\nBitte warten ... ";


$srcQuery = 'SELECT * FROM hahela_tv_widget.tv_lei';
$stmt = $dbh->prepare($srcQuery);
$stmt->execute();
while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
    unset($row['id']);
    insertArray($dbh, 'hahela_web_app.wa_lei', $row);
}


echo "Done\n";
exit;
