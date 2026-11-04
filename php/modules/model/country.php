<?php


/**
 * php/modules/model/country.php
 *
 * Country Model - Modell eines Landes (Staates) (singular).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * Country Model - Modell eines Landes (Staates) (singular).
 */
class CountryModel implements CountryInterface
{
    use AccessorTrait;    

    
    /**
     * Datenbank-Handle
     * @var \PDO
     */
    private \PDO $dbh;
    
    /**
     * Konfigurationsdaten des Systems.
     * @var array
     */
    private array $config;
    
    /**
     * Identifikationsnummer des Wertpapierdatensatzes in der Datenbank.
     * @var int
     * @default 0 
     */
    protected int $id = 0;
    
    /**
     * Alpha-2 Landeskennung bestehend aus zwei Großbuchstaben.
     * @var string
     * @default '' Leere Zeichenkette
     */
    protected string $alpha_2 = '';
    
    
    /**
     * Alpha-3 Landeskennung bestehend aus drei Großbuchstaben.
     * @var string
     * @default '' Leere Zeichenkette
     */
    protected string $alpha_3 = '';
    
    /**
     * Landesname (Staatsname) in Deutsch und Englisch.
     * @var string
     * @default '' Leere Zeichenkette(n)
     */
	protected string $name_de = '';    
	protected string $name_en = '';    
    
    /**
     * Konfiguration- oder Suchoptionen.
     * @var array
     * @default array() Leeres Array
     */
    protected array $options = array();
    
    
    /**
     * Konstruktor
     *
     * @param \PDO $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param array $options Optionale Konfigurationsparameter.
     * 
     * @param integer $id (optional) Identifikationsnummer eines
     *   Landes- (Staats)-Datensatzes in der Datenbank. Wenn dieser Parameter 
     *   vorhanden ist, dann werden die Daten des entsprechenden Landes 
     *   (Staats) aus der Datenbank gelesen und die Objekteigenschaften 
     *   entsprechend initialisiert. 
     */
    public function __construct(\PDO $dbh, array $config, array $options = array())
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
     * Holt den Namen des Landes (Staats) in deutscher oder englischer Sprache.
     * 
	 * @param string $language Optionaler Parameter der Sprache. 
	 * 	Voreingestellt ist die deutsche Sprache. 
     * @return string Name des Landes (Staats) in deutscher oder englischer 
	 * 	Sprache.
     */
    public function getName(string $language = 'de'): string
    {
        $methodName = 'getName' . ucfirst($language);
        return $this->$methodName();
    }
    
    
    /**
     * Setzt den Namen eines Landes (Staats). Die voreingestellte Sprache 
	 * 	ist Deutsch. Moeglich ist auch die englische Sprache.
     * 
     * @param string Name des Landes (Staats) in deutscher oder englischer 
	 * 	Sprache.
	 * @param string $language Optionaler Parameter der Sprache. 
	 * 	Voreingestellt ist die deutsche Sprache. 
     * @return CountryModel
     */
    public function setName(string $name, string $language = 'de'): CountryModel
    {
        $methodName = 'setName' . ucfirst($language);
        $this->$methodName($name);
        return $this;
    }
    
    
    /**
     * Erzeugt einen Landes- (Staats-)Datensatzes in der Datenbank. 
     *  Der Landes- (Staats-)name in deutscher Sprache ist zwingend 
     *  erforderlich. Der englische Landes- (Staats-)name ist optional. 
     * 
     * @return integer Identifikationsnummer des Landes- (Staats-)Datensatzes
     *  in der Datenbank. 
     */
    public function create(): int
    {
        $query = "INSERT INTO {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}country (alpha_2,alpha_3,name_de,name_en) VALUES(:alpha_2,:alpha_3,:name_de,:name_en)";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':alpha_2', $this->alpha_2, \PDO::PARAM_STR);
        $stmt->bindParam(':alpha_3', $this->alpha_3, \PDO::PARAM_STR);
        $stmt->bindParam(':name_de', $this->name_de, \PDO::PARAM_STR);
        if (empty($this->name_en))
            $stmt->bindValue(':name_en', null, PDO::PARAM_NULL);
        else
            $stmt->bindValue(':name_en', $this->name_en, PDO::PARAM_STR);
        $stmt->execute();
        $this->id = intval($this->dbh->lastInsertId());
        return $this->id;
    }
    
    
    /**
     * Aktualisiert den Landes- (Staats-)Datensatz. 
     *  Der Landes- (Staats-)name in deutscher Sprache ist zwingend 
     *  erforderlich. Der englische Landes- (Staats-)name ist optional. 
     * 
     * @return boolean Ergebnis der Aktualisierung. true - die Aktualisierung
     *   war erfolgreich. false - der Datensatz wurde nicht aktualisert.
     */
    public function update(): bool
    {
        $query = "UPDATE {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}country SET alpha_2=:alpha_2,alpha_3=:alpha_3,name_de=:name_de,name_en=:name_en WHERE id=:id";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':alpha:2', $this->alpha_2, \PDO::PARAM_STR);
        $stmt->bindParam(':alpha_3', $this->alpha_3, \PDO::PARAM_STR);
        $stmt->bindParam(':name_de', $this->name_de, \PDO::PARAM_STR);
        if (empty($this->name_en))
            $stmt->bindValue(':name_en', null, PDO::PARAM_NULL);
        else
            $stmt->bindValue(':name_en', $this->name_en, PDO::PARAM_STR);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    
    /**
     * Liest den Landes- (Staats-)Datensatz, der durch die 
     *  Identifikationsnummer in der Eigenschaft $id repraesentiert wird. 
     *  Ist ein Datensatz mit der gegebenen Identifikationsnummer nicht in 
     *  der Datenbank vorhanden, dann werden die Eigenschaften des 
     *  Landesobjekts mit vorgegebenen Werten gefuellt.
     *
     * @return CountryModel Gibt das Objekt selbst zurueck 
     *   (fluent interface).
     */
    public function read(): CountryModel
    {
        $query = "SELECT * FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}country WHERE id=:id";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
		if (false === $row) $this->resetProperties();
		else {
			$this->alpha_2 = strval($row['alpha_2']);
			$this->alpha_3 = strval($row['alpha_3']);
			$this->name_de = strval($row['name_de']);
			$this->name_en = strval($row['name_en']);
		}
        return $this;
    }
    
    
    /**
     * Loescht den Landes- (Staats-)Datensatz aus der Datenbank. 
     *  Der zu loeschende Datensatz ist durch die Identifikationsnummer 
     *  in der Eigenschaft $id repraesentiert.
     *
     * @return boolean Die Methode liefert den boolschen Wert true, 
     *   falls die Daten geloescht werden konnten, sonst false.
     */
    public function delete(): bool
    {
        $query = "DELETE FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}country WHERE id=:id";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->rowCount();
        return (0 < $count);
    }
    
    
    /**
     * Ueberprueft, ob ein Land (Staat) in der Datenbank existiert.
     * 
     * @property array $options Assoziatives Feld (Map) mit den Suchoptionen 
     *  als Schluessel/Wert-Paare. Die Suchoptionen sind UND verknuepft.
     *  Die Bezeichnung der Schluessel muss mit dem entsprechenden 
     *  Tabellenspaltennamen in der Datenbank uebereinstimmen.
     * @return int|false Wenn das Land (Staat) gefunden wird, dann wird 
     *  dessen Identifikationsnummer zurueckgegeben, sonst false.
     */
    public function exists(): int|false
    {
        $query = "SELECT id FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}country";
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
     * Sucht die Landes- (Staats-)Datensaetze deren Wert im Datenbankfeld
     *   'name_de' ODER 'name_en' dem Namensfragment aehneln. 
     * 
     * @param string $nameFragment Fragment des Landes- (Staats-)namen in 
     *  deutscher oder englischer Sprache.
	 * @param string $language Optionale Angabe der Sprache, Voreingestellt 
	 * 	ist die deutsche Sprache. Englisch ist eine weitere 
	 * 	Sprachalternative.
     * @return array Feld mit den Identifikationsnummern der 
     *  Landes- (Staats-)Datensaetze deren Wert im Datenbankfeld 'name_de'
     *  ODER 'name_en' dem Namensfragment aehneln.
     */
    public function searchByName(string $nameFragment, string $language = 'de'): array
    {
		$colName = 'name_' . $language;
        $nameFragment = '%' . $nameFragment . '%';
        $query = "SELECT id FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}country";
        $whereClause = " WHERE $colName LIKE :$colName"; 
        $stmt = $this->dbh->prepare($query . $whereClause);
        $stmt->bindParam(":$colName", $nameFragment, \PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $rows;
    }
    
    
    /**
     * Uebertraegt die wichtigsten Eigenschaften eines Landes- 
     *  (Staats)Datensatzes in ein Objekt einer Standardklasse und gibt 
     *  dieses Objekt zurueck.
     *
     * @return \stdClass
     */
    public function toStdObject(): \stdClass
    {
        $output = new \stdClass();
        $output->id = intval($this->id);
        $output->alpha_2 = strval($this->alpha_2);
        $output->alpha_3 = strval($this->alpha_3);
		$output->name_de = strval($this->name_de);
		$output->name_en = strval($this->name_en);
        return $output;
    }
    
    
    /**
     * Bestimmt den 2-Buchstabencode eines Landes (Staats).
     * 
     * @param string $identifier Die Kennung nach der in der Datenbak gesucht 
     *  wird, kann eine 2- oder 3-Buchstabenkennung in Grossbuchstaben oder 
     *  ein Namensfragment sein.
     * @return string 2-Buchstabenkennung in Grossbuchstaben.
     */
    public function determine2LetterCode(string $identifier): string
    {
        // 2-Buchstabenkennung (nur in Grossbuchstaben)
        $pattern = "/^[A-Z]{2}$/";
        if (1 === preg_match($pattern, $identifier)) {
            return $identifier;
        }
        // 3-Buchstabenkennung (nur in Grossbuchstaben)
        $pattern = "/^[A-Z]{3}$/";
        if (1 === preg_match($pattern, $identifier)) {
            $id = $this->setOptions(array('alpha_3' => $identifier))->exists();
            return $this->setId($id)->read()->getAlpha2();
        }
        // Namensfragment
        $result = $this->searchByName($identifier);
        return $this->setId($result[0]['id'])->read()->getAlpha2();
    }
    
    
    /**
     * Setzt ausgewaehlte Landes- (Staats-)Eigenschaften auf vordefinierte 
     *  Werte zurueck.
     *
     * @return CountryModel
     */
    public function resetProperties(): CountryModel
    {
        $this->id = 0;
        $this->alpha_2 = '';
        $this->alpha_3 = '';
		$this->name_de = '';
		$this->name_en = '';
        $this->options = array();
        return $this;
    }
    
    
    /************************************** private und geschuetzte Methoden */
}
