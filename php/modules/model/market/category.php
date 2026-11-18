<?php


/**
 * php/modules/model/market/category.php
 *
 * Market Category Model - Modell einer Handelplatzkategorie (singular).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * Market Category Model - Modell einer Handelplatzkategorie (singular).
 */
class MarketCategoryModel_ implements MarketCategoryInterface
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
	 * Konfiguration- oder Suchoptionen.
	 * @property array
	 */
	protected array $options = array();

    /**
     * Identifikationsnummer des Handelplatzes in der Datenbank.
     * @property int
     * @default 0 
     */
    protected int $id = 0;
    
	/**
	 * 4-Grossbuchstaben Code einer Handelsplatzkategorie.
	 * @property string
	 * @default '' Leere Zeichekette.
	 */
	protected string $code = '';

	/**
	 * Name (Bezecihung) der Handelsplatzkategorie.
	 * @property array Assoziative Array (Map) wobei die Scluessel die 
	 * 	2-Kleinbuchstabenkenung der Sprache sind in denen die Werte die 
	 * 	entspechenden Name der Handelsplatzkategorie sind.
	 * @default '' Namen als leere Zeichenketten.
	 */
	protected array $name = array('de' => '', 'en' => '');
	
	
    /**
     * Konstruktor
     *
     * @param \PDO $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param array $options (optional) Optionale Konfigurationsparameter.
     * 
     * @param integer $id (optional) Identifikationsnummer einer
     *   Handelplatzkategorie in der Datenbank. Wenn dieser Parameter 
     *   vorhanden ist, dann werden die Daten des entsprechenden 
     *   Handelplatzes aus der Datenbank gelesen und die 
     *   Objekteigenschaften entsprechend initialisiert. 
     */
    public function __construct (\PDO $dbh, array $config, array $options = array())
    {
        $this->dbh = $dbh;
        $this->config = $config;
        $this->options = $options;
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
     * Holt die Optionen
     *
     * @return array Optionen
     */
    public function getOptions (): array
    {
        return $this->options;
    }
    
    
    /**
     * Setzt die Optionen
     *
     * @param array $options Optionen
     * @return \HaHeLa\MarketCategoryModel
     */
    public function setOptions (array $options): \HaHeLa\MarketCategoryModel
    {
        $this->options = $options;
        return $this;
    }
    

    /**
     * Holt die Identifikationsnummer der Handelplatzkategorie in der 
	 * 	Datenbank.
     *
     * @return int Identifikationsnummer der Handelplatzkategorie.
     */
    public function getId (): int
    {
        return $this->id;
    }
    
    
    /**
     * Setzt die Identifikationsnummer der Handelplatzkategrie.
     *
     * @param int $id Identifikationsnummer der Handelplatzkategorie.
     * @return \HaHeLa\MarketCategoryModel
     */
    public function setId (int $id): \HaHeLa\MarketCategoryModel
    {
        $this->id = intval($id);
        return $this;
    }
	
    
	/**
	 * Holt den 4-Grossbuchstabencode der Handelplatzkategorie in der 
	 * 	Datenbank.
	 *
	 * @return string 4-Grossbuchstabencode der Handelplatzkategorie.
	 */
	public function getCode (): string
	{
	    return $this->code;
	}


	/**
	 * Setzt den 4-Grossbuchstabencode der Handelplatzkategrie.
	 *
	 * @param string $code 4-Grossbuchstabencode der Handelplatzkategorie.
	 * @return \HaHeLa\MarketCategoryModel
	 */
	public function setCode (string $code): \HaHeLa\MarketCategoryModel
	{
	    $this->code = strval($code);
	    return $this;
	}

	
	/**
	 * Holt den Namen (die Bezeichnung) der Handelsplatzkategorie in der 
	 * 	entsprechenden Sprache.
	 * 
	 * @param string $language 2-Kleinbuchstabenkennung der Sprache.
	 * @return string 	Namen (Bezeichnung) der Handelsplatzkategorie 
	 * 	in der entsprechenden Sprache.
	 */	
	public function getName (string $language = 'en'): string
	{
		return $this->name[$language];
	}	
	
	
	/**
	 * Setzt den Namen (die Bezeichnung) der Handelsplatzkategorie in der 
	 * 	entsprechenden Sprache.
	 * 
	 * @param string $name Name (Bezeichnung) der Handelsplatzkategorie.
	 * @param string $language 2-Kleinbuchstabenkennung der Sprache.
	 * @return \HaHeLa\MarketCategoryModel
	 */
	public function setName (string $name, string $language = 'en'): \HaHeLa\MarketCategoryModel
	{
		$this->name[$language] = strval($name);
		return $this;
	}
	
	
	/**
	 * Erzeugt einen Marktplatzkategoriedatensatz in der Datenbank.
	 * 
	 * @return int Identifikationsnummer des 
	 * 	Markplatzkategoriedatensatzes in der Datenbank.
	 */
	public function create (): int
	{
	    $query  = "INSERT INTO {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}market_category";
		$query .= ' (code, name_de, name_en)';
		$query .= ' VALUES(:code,:name_de,:name_en)';
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(':code', $this->code, \PDO::PARAM_STR);
		$stmt->bindParam(':name_de', $this->name['de'], \PDO::PARAM_STR);
		$stmt->bindParam(':name_en', $this->name['en'], \PDO::PARAM_STR);
	    $stmt->execute();
	    $this->id = intval($this->dbh->lastInsertId());
	    return $this->id;
	}


	/**
	 * Aktualisiert den Markplatzkategoriedatensatz. 
	 * 
	 * @return bool Ergebnis der Aktualisierung. TRUE - die Aktualisierung
	 * 	war erfolgreich. FALSE - der Datensatz wurde nicht aktualisert.
	 */
	public function update (): bool
	{
		$query = "UPDATE {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}market_category";
		$query .= ' SET code=:code,name_de=:name_de,name_en=:name_en';
		$query .= ' WHERE id=:id';
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(':code', $this->code, \PDO::PARAM_STR);
		$stmt->bindParam(':name_de', $this->name['de'], \PDO::PARAM_STR);
		$stmt->bindParam(':name_en', $this->name['en'], \PDO::PARAM_STR);
		$stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
		return $stmt->execute();
	}


	/**
	 * Liest den Marktplatzkategoriedatensatz, der durch die 
	 * 	Identifikationsnummer in der Eigenschaft $id repraesentiert wird. 
	 *  Ist ein Datensatz mit der gegebenen Identifikationsnummer nicht in 
	 *  der Datenbank vorhanden, dann werden die Eigenschaften des
	 *  Marktplatzkategorieobjekts mit vorgegebenen Werten gefuellt.
	 *
	 * @return \HaHeLa\MarketCategoryModel Gibt das Objekt selbst zurueck 
	 *   (fluent interface).
	 */
	public function read (): \HaHeLa\MarketCategoryModel
	{
	    $query = "SELECT * FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}market_category WHERE id=:id";
	    $stmt = $this->dbh->prepare($query);
	    $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
	    $stmt->execute();
	    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
		if (false === $row) $this->resetProperties();
		else {
			foreach ($row as $key => $value) {
				if (str_contains($key, 'name_')) {
					$prop = substr($key, 0, strpos($key, '_'));
					$lang = substr($key, strpos($key, '_') + 1);
					$this->$prop[$lang] = $value;
				}
				else $this->$key = $value;
			}
		}
	    return $this;
	}
	
	
	/**
	 * Loescht den Markplatzkategoriedatensatz aus der Datenbank und setzt 
	 * 	die ausgewaehlten Objekteigenschaften auf Standardwerte zurueck.
	 * 	Der zu loeschende Datensatz ist durch die Identifikationsnummer 
	 * 	in der Eigenschaft $id repraesentiert.
	 *
	 * @return bool Die Methode liefert den boolschen Wert TRUE, 
	 *   falls die Daten geloescht werden konnten, sonst FALSE.
	 */
	public function delete (): bool
	{
	    $query = "DELETE FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}market_category WHERE id=:id";
	    $stmt = $this->dbh->prepare($query);
	    $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
	    $stmt->execute();
	    $count = $stmt->rowCount();
		$this->resetProperties();
	    return (0 < $count);
	}
	
	
	/**
	 * Setzt ausgewaehlte, proteced Eigenschaften eines Objektes der Instanz 
	 *  der Marktplatzklasse auf vordefinierte Werte zurueck.
	 *
	 * @return \HaHeLa\MarketCategoryModel
	 */
	public function resetProperties (): \HaHeLa\MarketCategoryModel
	{
	    $classVars = get_class_vars('\HaHeLa\MarketCategoryModel');
		foreach ($classVars as $name => $value) { // $value wird nicht genutzt.
			$reflecPropObject = new \ReflectionProperty('\HaHeLa\MarketCategoryModel', $name);
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
				if ('name' === $name) 
					$this->name = array('de' => '', 'en' => '');
	        }
	    }
	    return $this;
	}
	
	
	/**
	 * Ueberprueft ob Markplaetze mit den gegebenen Eigenschaften in der 
	 * 	Datenbank existieren.
	 * 
	 * @property array $options Assoziatives Feld (Map) mit den Suchoptionen 
	 *   als Schluessel/Wert-Paare. Die Suchoptionen sind UND verknuepft.
	 *   Die Bezeichnung der Schluessel muss mit dem entsprechenden 
	 *   Tabellenspaltennamen in der Datenbank uebereinstimmen.
	 * @return FALSE|array Wenn Markplaetze gefunden werden, dann werden 
	 * 	deren Identifikationsnummern zurueckgegeben, sonst FALSE.
	 */
	public function exists (): FALSE|array
	{
	    $query = "SELECT id FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}market_category";
	    $whereClause = ' WHERE';
	    $firstLoop = true;
	    foreach ($this->options as $key => $value) {
	        if ($firstLoop) $firstLoop = FALSE;
	        else $whereClause .= ' AND';
	        if ('id' == $key) $whereClause .= ' id=' . $value;
	        else $whereClause .= ' ' . $key . ' LIKE ' . "'$value'";
	    }
	    $query .= $whereClause;
	    $stmt = $this->dbh->prepare($query);
	    $stmt->execute();
	    $rows = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
	    return $rows;
	}
	
	
	/**
	 * Uebertraegt die wichtigsten Eigenschaften eines 
	 * 	Marktplatzkategoriedatensatzes in ein Objekt einer 
	 * 	Standardklasse und gibt dieses Objekt zurueck.
	 *
	 * @return \stdClass
	 */
	public function toStdObject (): \stdClass
	{
	    $output = new \stdClass();
	    $output->id = intval($this->id);
		$output->options = $this->options;
		$output->code = strval($this->code);
		$output->name = $this->name;
	    return $output;
	}
	
	
	/**
	 * Pruefe die Syntax des Marktkategoriecodes
	 * 	(Market Category Code)
	 * 
	 * @param string $mcc Marktkategoriecode
	 * @return bool Wenn der Marktkategoriecode eine gueltige Syntax hat, 
	 * 	dann liefert die Methode den boolschen Wert TRUE, sonst FALSE.
	 */
	public function validateMarketCategoryCodeSyntax (string $mcc): bool
	{
		$pattern = '/^[A-Z]{4,4}$/i';
		$result = preg_match($pattern, $mcc);
		return (1 === $result);
	}
	
	
	/************************************** private und geschuetzte Methoden */
	
	
}
