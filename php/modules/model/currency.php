<?php


/**
 * php/modules/model/currency.php
 *
 * Currency Model - Modell einer Waehrung (singular).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * Currency Model - Modell einer Waehrung (singular).
 */
class CurrencyModel implements CurrencyInterface
{
    use AccessorTrait;    


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
     * Identifikationsnummer eines Waehrungsdatensatzes in der Datenbank.
     * @property integer
     * @default 0 
     */
    protected int $id = 0;
    
    /**
     * Abkuerzung der Waehrung in Grossbuchstaben.
     * @property string (max 7 Zeichen)
     * @default '' Leere Zeichenkette
     */
    protected string $currency = '';
    
    
    /**
     * Waehrungsname in Englisch.
     * @property string
     * @default '' Leere Zeichenkette
     */
    protected string $name_en = '';    
    
    /**
     * Waehrungssymbol oder eine weitere Abkuerzung zur Waehrung.
     * @property string
     * @default '' Leere Zeichenkette
     */
    protected string $symbol = '';
    
    /**
     * Konfiguration- oder Suchoptionen.
     * @property array
     */
    protected array $options;
    
    
    /**
     * Konstruktor
     *
     * @param \PDO $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param array $options Optionale Konfigurationsparameter.
     * 
     * @param integer $id (optional) Identifikationsnummer eines
     *   Waehrungsdatensatzes in der Datenbank. Wenn dieser Parameter 
     *   vorhanden ist, dann werden die Daten der entsprechenden Waehrung 
     *   aus der Datenbank gelesen und die Objekteigenschaften 
     *   entsprechend initialisiert. 
     */
    public function __construct($dbh, $config, $options = array())
    {
        $this->dbh = $dbh;
        $this->config = $config;
        $this->options = $options;
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
     * Erzeugt einen Waehrungsdatensatzes in der Datenbank.
     * 
     * @return integer Identifikationsnummer des Waehrungsdatensatzes
     *   in der Datenbank.
     */
    public function create()
    {
        $query = "INSERT INTO {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}currency (currency,name_en,symbol) VALUES(:currency,:name_en,:symbol)";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':currency', $this->currency, \PDO::PARAM_STR);
        $stmt->bindParam(':name_en', $this->name_en, \PDO::PARAM_STR);
        $stmt->bindParam(':symbol', $this->symbol, \PDO::PARAM_STR);
        $stmt->execute();
        $this->id = intval($this->dbh->lastInsertId());
        return $this->id;
    }
    
    
    /**
     * Aktualisiert den Waehrungsdatensatz. 
     * 
     * @return boolean Ergebnis der Aktualisierung. true - die Aktualisierung
     *   war erfolgreich. false - der Datensatz wurde nicht aktualisert.
     */
    public function update()
    {
        $query = "UPDATE {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}currency SET currency=:currency,name_en=:name_en,symbol=:symbol WHERE id=:id";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':currency', $this->currency, \PDO::PARAM_STR);
        $stmt->bindParam(':name_en', $this->name_en, \PDO::PARAM_STR);
        $stmt->bindParam(':symbol', $this->symbol, \PDO::PARAM_STR);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    
    /**
     * Liest den Waehrungsdatensatz, der durch die Identifikationsnummer 
     *  in der Eigenschaft $id repraesentiert wird. 
     *  Ist ein Datensatz mit der gegebenen Identifikationsnummer nicht in 
     *  der Datenbank vorhanden, dann werden die Eigenschaften des
     *  Waehrungsobjekts mit vorgegebenen Werten gefuellt.
     *
     * @return HaHeLa\CurrencyModel Gibt das Objekt selbst zurueck 
     *   (fluent interface).
     */
    public function read()
    {
        $query = "SELECT * FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}currency WHERE id=:id";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
		if (false === $row) $this->resetProperties();
		else {
			$this->currency = strval($row['currency']);
			$this->name_en = strval($row['name_en']);
			$this->symbol = strval($row['symbol']);
		}
        return $this;
    }
    
    
    /**
     * Loescht den Waehrungsdatensatz aus der Datenbank. 
     *   Der zu loeschende Datensatz ist durch die Identifikationsnummer 
     *   in der Eigenschaft $id repraesentiert.
     *
     * @return boolean Die Methode liefert den boolschen Wert true, 
     *   falls die Daten geloescht werden konnten, sonst false.
     */
    public function delete()
    {
        $query = "DELETE FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}currency WHERE id=:id";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->rowCount();
        return (0 < $count);
    }
    
    
    /**
     * Ueberprueft ob eine Waehrung in der Datenbank existiert.
     * 
     * @property array $options Assoziatives Feld (Map) mit den Suchoptionen 
     *   als Schluessel/Wert-Paare. Die Suchoptionen sind UND verknuepft.
     *   Die Bezeichnung der Schluessel muss mit dem entsprechenden 
     *   Tabellenspaltennamen in der Datenbank uebereinstimmen.
     * @return int|false Wenn die Waehrung gefunden wird, dann wird dessen 
     *   Identifikationsnummer zurueckgegeben, sonst false.
     */
    public function exists()
    {
        $query = "SELECT id FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}currency";
        $whereClause = ' WHERE';
        $firstLoop = true;
        foreach ($this->options as $key => $value) {
            if ($firstLoop) $firstLoop = false;
            else $whereClause .= ' AND';
            if ('id' == $key) $whereClause .= ' id=' . $value;
            else $whereClause .= ' ' . $key . ' LIKE ' . "'$value'";
        }
        $query .= $whereClause;
        $stmt = $this->dbh->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (false === $row)? $row: intval($row['id']);
    }
    
    
    /**
     * Sucht die Waehrung, deren Wert im Datenbankfeld 'name_en' dem 
     *  Namensfragment aehneln. 
     *   
     * @param string $nameFragment Fragment des Waehrungsnamen.
     * @return array Feld mit den Identifikationsnummern der 
     *   Waehrungen, deren Wert im Datenbankfeld 'name_en' dem 
     *   Namensfragment aehneln.
     */
    public function searchByName($nameFragment)
    {
        $nameFragment = '%' . $nameFragment . '%';
        $query = "SELECT id FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}currency";
        $whereClause = ' WHERE name_en LIKE :name_en';
        $stmt = $this->dbh->prepare($query . $whereClause);
        $stmt->bindParam(':name_en', $nameFragment, \PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $rows;
    }
    
    
    /**
     * Uebertraegt die wichtigsten Eigenschaften eines Waehrungsdatensatzes 
     *  in ein Objekt einer Standardklasse und gibt dieses Objekt zurueck.
     *
     * @return \stdClass
     */
    public function toStdObject(): \stdClass
    {
        $output = new \stdClass();
        $output->id = intval($this->id);
        $output->currency = strval($this->currency);
        $output->name_en = strval($this->name_en);
        $output->symbol = strval($this->symbol);
        return $output;
    }
    
    
    /**
     * Setzt ausgewaehlte, proteced Eigenschaften eines Objektes der Instanz 
     *  der Waehrungsklasse auf vordefinierte Werte zurueck.
     *
     * @return HaHeLa\CurrencyModel
     */
    public function resetProperties()
    {
        $classVars = get_class_vars('\HaHeLa\CurrencyModel');
        foreach ($classVars as $name => $value) { // $value wird nicht genutzt.
            $reflecPropObject = new \ReflectionProperty('\HaHeLa\CurrencyModel', $name);
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
        return $this;
    }
    
    
    /************************************** private und geschuetzte Methoden */
}
