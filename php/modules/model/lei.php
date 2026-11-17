<?php


/**
 * php/modules/model/lei.php
 *
 * Lei Model - Modell eines Legal-Entity-Identifiers (singular).
 * Der Legal Entity Identifier (LEI) ist ein 20-stelliger, alphanumerischer 
 * Code zur eindeutigen Identifikation von Rechtstraegern (Unternehmen, Fonds, 
 * Vereine) bei Finanztransaktionen. Er basiert auf ISO 17442, ist weltweit 
 * einheitlich und seit 2018 für viele Marktteilnehmer verpflichtend. 
 * 
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * Lei Model - Modell eines Legal-Entity-Identifiers (singular).
 */
class LeiModel extends SqldbModel implements LeiInterface
{
	/**
	 * Datenbank-Handle
	 * @property \PDO
	 */
	private \PDO $dbh;
	
	/**
	 * Konfigurationsdaten des Systems.
	 * @property array
	 */
	private array $config;
	
	/**
	 * Identifikationsnummer des Handelplatzes in der Datenbank.
	 * @property integer
	 * @default 0 
	 */
	protected int $id = 0;
	
	/**
	 * Konfiguration- oder Suchoptionen.
	 * @property array
	 */
	protected array $options = array();
	
	/**
	 * Alle zusaetzliche Eigenschaften als assoziative Feld.
	 * @property array
	 */
	protected array $properties = array();
	
	
	/**
	 * Konstruktor
	 *
	 * @param \PDO $dbh Datenbank-Handle.
	 * @param array $config Konfigurationsdaten.
	 * @param array $options (optional) Optionale Konfigurationsparameter.
	 * 
	 * @param integer $id (optional) Identifikationsnummer eines
	 * 	LEI in der Datenbank. Wenn dieser Parameter vorhanden ist, 
	 * 	dann werden die Daten des entsprechenden LEIs aus der Datenbank 
	 * 	gelesen und die Objekteigenschaften entsprechend initialisiert. 
	 */
	public function __construct(\PDO $dbh, array $config, array $options = array())
	{
		$this->dbh = $dbh;
		$this->config = $config;
		$this->options = $options;
		parent::__construct($this->dbh, $this->config, $this->options);
		// Dynamische Deklaration von Objekteigenschaften.
		$this->declareProperties();
		// Beachte optionale Parameter.
		if (3 < \func_num_args()) {
			if (4 == \func_num_args()) { // Parameteranzahl ist 4
				$this->id = intval(\func_get_arg(3));
				$this->read();
			}
			else {
				// Option auf mehr als 4 Parameter.
			}
		}
	}
    

    /**
     * Holt die Identifikationsnummer des LEIs in der Datenbank.
     *
     * @return integer Identifikationsnummer des LEIs.
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * Setzt die Identifikationsnummer des LEIs.
     *
     * @param integer $id Identifikationsnummer des LEIs.
     * @return \HaHeLa\Modules_Model_Lei
     */
    public function setId(int $id): \HaHeLa\LeiModel
    {
        $this->id = intval($id);
        return $this;
    }
	
	
	/**
	 * Holt die Optionen
	 *
	 * @return array Optionen
	 */
	public function getOptions(): array
	{
		return $this->options;
	}
	
	
	/**
	 * Setzt die Optionen
	 *
	 * @param array $options Optionen
	 * @return \HaHeLa\LeiModel
	 */
	public function setOptions(array $options): \HaHeLa\LeiModel
	{
		$this->options = $options;
		return $this;
	}
	
	
	/**
	 * Holt die zusaetzlichen Objekteigenschaften, d.h. Objekteigenschaffen 
     *  exklusive $id und $options.
	 *
	 * @return array
	 */
	public function getProperties(): array
	{
		return $this->properties;
	}
	
	
	/**
	 * Setzt die zusaetzlichen Objekteigenschaften. $id und $options zaehlen 
	 * 	nicht zu den zusaetzlichen Objekteigenschaften.
	 *
	 * @param array $properties Zusaetzliche Objekteigenschaften.
	 * @return \HaHeLa\Modules_Model_Lei
	 */
	public function setProperties(array $properties): \HaHeLa\LeiModel
	{
		// Sollten Elemente mit dem Schluesseln 'id' oder 'options' im
		// 	Parameterfeld $properties enthalten sein, dann werden diese 
		// 	Elemente entfernt.
		if (array_key_exists('id', $properties))
			unset($properties['id']);
		if (array_key_exists('options', $properties))
			unset($properties['options']);		
		$this->properties = $properties;
		return $this;
	}
	
	
	/**
	 * Holt den Wert einer Eigenschaft.
	 * 
	 * @param string $propName Name einer Objekteigenschaft. Der Name der 
	 * 	Objekteigenschaft entspricht dem entsprechenen Tabellenspaltennamen 
	 * 	der Tabelle 'wa_lei'.
	 * @return string Wert einer Eigenshaft.
	 */
	public function getProperty(string $propName): string
	{
		return $this->properties[$propName];
	}
	
	
	/**
	 * Setzt den Wert einer Eigenschaft.
	 * 
	 * @param string $propName Name einer Objekteigenschaft. Der Name der 
	 * 	Objekteigenschaft entspricht dem entsprechenen Tabellenspaltennamen 
	 * 	der Tabelle 'wa_lei'.
	 * @param string $propValue Wert einer Objekteigenschaft.
	 * @return \HaHeLa\LeiModel
	 */
	public function setProperty(string $propName, string $propValue): \HaHeLa\LeiModel
	{
		$this->properties[$propName] = strval($propValue);
		return $this;
	}


    /**
     * Erzeugt einen LEI-Datensatzes in der Datenbank.
     * 
     * @return integer Identifikationsnummer des neuen LEI-Datensatzes 
     *  in der Datenbank oder Wert 0 als Hinweis, dass kein LEI-Datensatz 
     *  in der Datenbank erzeugt werden konnte.
     */
    public function create(): int
    {
        // Eigenschaften, deren Werte leer sind, werden aus der 
        // 	Eigenschaftsliste entfernt.
		$props = $this->properties;
		foreach ($props as $key => $value) {
			if (empty($value)) {
				unset($props[$key]);
			}
		}
		$query  = "INSERT INTO {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}lei";
		$columns  = ' (';
		$values = ' VALUES(';
		$firstPass = TRUE;
		foreach (array_keys($props) as $key) {
			if ($firstPass) $firstPass = FALSE;
			else {
				$columns .= ',';
				$values .= ',';
			}
			$columns .= $key;
			$values .= ":$key";
		}
		$columns .= ')';
		$values .= ')';
		$query .= $columns . $values;
		$stmt = $this->dbh->prepare($query);
		$stmt->execute($props);
		$this->id = intval($this->dbh->lastInsertId());
		return $this->id;
	}

	
	/**
	 * Liest den LEI-Datensatz, der durch die Identifikationsnummer 
	 *  in der Eigenschaft $id repraesentiert wird. 
	 *  Ist ein Datensatz mit der gegebenen Identifikationsnummer nicht in 
	 *  der Datenbank vorhanden, dann werden die Eigenschaften des
	 *  LEI-Objekts mit vorgegebenen Werten gefuellt.
	 *
	 * @return \HaHeLa\LeiModel Gibt das Objekt selbst zurueck 
     *  (fluent interface).
	 */
	public function read(): \HaHeLa\LeiModel
	{
		$query = "SELECT * FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}lei WHERE id=:id";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch(\PDO::FETCH_ASSOC);
		if (FALSE === $row) $this->resetProperties();
		else {
			$this->resetProperties(TRUE);
			foreach ($row as $key => $value) {
				if ('id' !== $key && 'options' !== $key) {
					$this->setProperty($key, strval($value)); 
				}
			}
		}
		return $this;
	}
	
	
	/**
	 * Erneuert einen LEI-Datensatz in der Datenbank. Der zu erneuerten 
	 * 	Datensatz ist durch die Identifikationsnummer in der Eigenschaft 
	 * 	$id repraesentiert. Die zu erneuernten Daten befinden sich in der 
	 * 	Eigenschaft $properties. 
	 * 
	 * @return bool Ergebnis der Aktualisierung. TRUE - die Aktualisierung
	 * 	war erfolgreich. FALSE - der Datensatz wurde nicht aktualisert.
	 */
	public function update(): bool
	{
		
		$query = "UPDATE {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}lei";
		$sets = ' SET ';
		$firstPass = TRUE;
		$props = $this->properties;
		// Der (Die) folgende(n) Wert(e) wird (werden) nicht aktualisiert 
		// 	und daher vaus den Eigenschaften entfernt.
		unset($props['updated_at']);
		// ... Platz fuer weitere Werte.
		foreach ($props as $key => $value) {
			if (empty($value)) 
				$props[$key] = NULL;
			if ($firstPass) $firstPass = FALSE;
			else {
				$sets .= ',';
			}
			$sets .= "$key=:$key";
		}
		$where = ' WHERE id=:id';
		$query .= $sets . $where;
		$stmt = $this->dbh->prepare($query);
		$props['id'] = $this->id;
		return $stmt->execute($props);
	}


	/**
	 * Loescht einen LEI-Datensatz aus der Datenbank und setzt die 
	 * 	ausgewaehlten Objekteigenschaften auf Standardwerte zurueck.
	 * 	Der zu loeschende Datensatz ist durch die Identifikationsnummer 
	 * 	in der Eigenschaft $id repraesentiert.
	 *
	 * @return bool Die Methode liefert den boolschen Wert true, 
	 *   falls die Daten geloescht werden konnten, sonst false.
	 */
	public function delete(): bool
	{
		$query = "DELETE FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}lei WHERE id=:id";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->rowCount();
		$this->resetProperties();
		return (0 < $count);
	}

	
	/**
	 * Setzt ausgewaehlte, proteced Eigenschaften eines Objektes der Instanz 
	 *  der LEI-Klasse auf vordefinierte Werte zurueck.
	 *
	 * @param bool $additionalOnly Optional Wenn dieser Indikator TRUE 
	 * 	ist, dann dann werden nur die zusaetzlichen Eigenschaften auf 
	 * 	vordefinierte Werte zurueck gesetzt. Wenn dieser Indikator, 
	 * 	wie voreingestellt FALSE ist, dann werden alle ausgewaehlten.
	 * 	protected Eigenschaften auf vordefinierte Werte eingestellt.
	 * @return HaHeLa\LeiModel
	 */
	public function resetProperties(bool $additionalOnly = FALSE): \HaHeLa\LeiModel
	{
		if (TRUE === $additionalOnly) {
			$this->properties = array();
		}
		else {
			$classVars = get_class_vars('\HaHeLa\LeiModel');
			foreach ($classVars as $name => $value) { // $value wird nicht genutzt.
				$reflecPropObject = new \ReflectionProperty('\HaHeLa\LeiModel', $name);
				if ($reflecPropObject->isProtected()) {
					$typeName = $reflecPropObject->getType()->getName();
					switch ($typeName) {
						case 'array': $this->$name = array();
						break;
						case 'int': $this->$name = 0;
						break;
						case 'string': $this->$name = '';
						break;
						default: $this->$name = NULL;
					}
				}
			}
		}
		return $this;
	}
	
	
	/**
	 * Such LEI in der Datenbank. 
	 * 
	 * @param string $lei LEI
	 * @return bool Wenn der LEI in der Datenbank gefunden wird, dann werden 
	 * 	die Eigenschaften des LEI-Objektes entsprechend initialisiert und 
	 * 	die Methode liefert den Wert TRUE. Wenn der LEI nicht in der 
	 * 	Datenbank gefunden wird, dann werden die Eigenschaften des 
	 * 	LEI-Objektes auf voreingestellte Werte zurueckgesetzt und die 
	 * 	Methode liefert den Wert FALSE.
	 */
	public function searchLei(string $lei): bool
	{
		$query = "SELECT * FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}lei";
		$whereClause = ' WHERE lei LIKE :lei';
		$query .= $whereClause;
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(':lei', $lei, \PDO::PARAM_STR);
		$stmt->execute();
		$row = $stmt->fetch(\PDO::FETCH_ASSOC);
		if (false === $row) {
			$this->resetProperties();
			return FALSE;
		}
		foreach ($row as $key => $value) {
			if ('id' === $key) $this->setId($value);
			else $this->setProperty($key, strval($value)); 
		}
		return TRUE;
	}

    	
    /**
     * Ueberprueft ob LEI-Datensaetze mit den gegebenen Eigenschaften in der 
     * 	Datenbank existieren.
     * 
     * @property array $options Assoziatives Feld (Map) mit den Suchoptionen 
     *  als Schluessel/Wert-Paare. Die Suchoptionen sind UND verknuepft.
     *  Die Bezeichnung der Schluessel muss mit dem entsprechenden 
     *  Tabellenspaltennamen in der Datenbank uebereinstimmen.
     * @return array|FALSE Wenn LEI-Datensaetze gefunden werden, dann werden 
     * 	deren Identifikationsnummern zurueckgegeben, sonst FALSE.
     */
	public function exists(): array|false
	{
		$query = "SELECT id FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}lei";
		$whereClause = ' WHERE';
		$firstLoop = true;
		foreach ($this->options as $key => $value) {
			if ($firstLoop) $firstLoop = FALSE;
			else $whereClause .= ' AND';
			if ('id' == $key) $whereClause .= ' id=' . intval($value);
			else {
				if (empty($value)) $whereClause .= ' ' . $key . ' IS NULL';		
				else $whereClause .= ' ' . $key . ' LIKE ' . "'$value'";
			}
		}
		$query .= $whereClause;
		$stmt = $this->dbh->prepare($query);
		$stmt->execute();
		$rows = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
		if (empty($rows))
			$rows = FALSE;
		return $rows;
	}
	
	
	/**
	 * Uebertraegt die wichtigsten Eigenschaften eines LEI-Datensatzes 
	 *     in ein Objekt einer Standardklasse und gibt dieses Objekt zurueck.
	 *
	 * @return \stdClass
	 */
	public function toStdObject(): \stdClass
	{
		$output = new \stdClass();
		$output->id = intval($this->id);
		$output->options = $this->options;
		$output->properties = $this->properties;
		return $output;
	}
	
	
	/**
	 * Prueft die Syntax des LEIs.
	 * 
	 * @param string $lei Vollstaendiger 20-stelliger LEI.
	 * @return bool Wenn der LEI eine gueltige Syntax hat, dann liefert die 
	 * 	Methode den boolschen Wert TRUE, sonst FALSE.
	 */
	public function validateLeiSyntax(string $lei): bool
	{
		$lei = strtoupper(trim($lei));
		$pattern = '/^[A-Z0-9]{18,18}[0-9]{2,2}$/i';
		if (!preg_match($pattern, $lei)) {
		    return FALSE;
		}
		// Buchstaben in Zahlen umwandeln
		$numericString = '';
		foreach (str_split($lei) as $char) {
			if (ctype_alpha($char)) {
				$numericString .= (ord($char) - ord('A') + 10);
			} 
			else {
				$numericString .= $char;
			}
		}
		// MOD 97 muss 1 ergeben (gültiger LEI)
		$remainder = 0;
		foreach (str_split($numericString, 9) as $block) {
			$remainder = (int)(($remainder . $block) % 97);
		}
		return (1 === $remainder);
	}
	
	
    /************************************** private und geschuetzte Methoden */
	
	
	/**
	 * Deklariere dynamische zusaetzliche Objekteigenschaften.
	 * 
	 * @return void
	 */
	private function declareProperties(): void
	{
		$tabName = $this->config['DATABASE']['TABLE_PREFIX'].'lei';
		$tabDescriptions = parent::describeTable($tabName);
		foreach ($tabDescriptions as $tabDescription) {
			if ('id' !== $tabDescription['COLUMN_NAME']) {
				$this->properties[$tabDescription['COLUMN_NAME']] = '';
			}
		}
	}
}
