<?php


/**
 * php/modules/model/security.php
 *
 * Security Model - Modell eines Wertpapiers (singular).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


use HaHeLa\CountryModel as Country;
use HaHeLa\SecurityTypeModel as SecurityType;


/**
 * Security Model - Modell eines Wertpapiers (singular).
 */
class SecurityModel extends SqldbModel implements SecurityInterface
{
    /**
     * Konstanten
     */
    const SECURITY_TABLE_NAME  = 'security';
    const SECURITY_COLUMN_NAME_TYPE = 'type';
    
    /**
     * Datenbank-Handle
     * @property resource
     */
    private $dbh;
    
    /**
     * Konfigurationsdaten des Systems.
     * @property array
     */
    private $config;
    
    /**
     * Identifikationsnummer des Wertpapierdatensatzes in der Datenbank.
     * @property integer
     * @default 0 
     */
    private $id = 0;
    
    /**
     * Name des Wertpapiers.
     * @property string
     * @default '' Leere Zeichenkette
     */
    private $name = '';
    
    
    /**
     * Wertpapiertyp ('indefinite', 'stock' , 'bond', 'equity', 'etf', 
     *  'fund', 'certificate', 'warrant', 'commodity', 'benchmark').
     * @property string
     * @default 'stock' Aktie
     */
    private $type = 'stock';
    
    /**
     * International Security Identification Number (ISIN).
     * @property string (max. 12 Zeichen)
     * @default '' Leere Zeichenkette
     */
    private $isin = '';
    
    /**
     * Wertpapier-Kennnummer-
     * @property string (max. 6 Zeichen)
     * @default '' Leere Zeichenkette
     */
    private $wkn = '';
    
    /**
     * Wertpapier-Symbol
     * @property string (max. 15 Zeichen).
     * @default '' Leere Zeichenkette,
     */
    private $symbol = '';
    
    /**
     * Reuters Instrument Code (RIC)
     * @property string (max 7 Zeichen).
     * @default '' Leere Zeichenkette.
     */
    private $ric = '';
    
    /**
     * Land (bevorzugt wird die 2-alpha Abkuerzung z.B. DE fuer Deutschland).
     *  Moeglich sind neben der 2-alpha Abkuerzung jedoch auch die 3-alpha 
     *  Abkuerzung und der ausgeschriebene Landesname in unterschiedlichen 
     *  Sprachen.
     * @property string 
     * @default '' Leere Zeichenkette
     */
    private $country = '';
    
    /**
     * Branche
     * @property string
     * @default '' Leere Zeichenkette
     */
    private $industry = '';
    
    /**
     * Sektor
     * @property string
     * @default '' Leere Zeichenkette
     */
    private $sector = '';
    
    
    /**
     * Konfiguration- oder Suchoptionen.
     * @property array
     * @default array() Leeres Feld.
     */
    private $options = array();
    
    
    /**
     * Konstruktor
     *
     * @param resource $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param array $options Optionale Konfigurationsparameter.
     * 
     * @param integer $id (optional) Identifikationsnummer eines
     *   Wertpapierdatensatzes in der Datenbank. Wenn dieser Parameter 
     *   vorhanden ist, dann werden die Daten des entsprechenden Wertpapiers
     *   aus der Datenbank gelesen und die Objekteigenschaften entsprechend 
     *   initialisiert. 
     */
    public function __construct ($dbh, $config, $options = array())
    {
        $this->dbh = $dbh;
        $this->config = $config;
        $this->options = $options;
        parent::__construct($this->dbh, $this->config, null);
        $this->type = parent::getDefaultEnumeration($this->config['database']['db_tableprefix'] . self::SECURITY_TABLE_NAME, self::SECURITY_COLUMN_NAME_TYPE);
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
     * Holt die Identifikationsnummer des Wertpapierdatensatzes 
     *   in der Datenbank.
     *   
     * @return integer Identifikationsnummer des Wertpapierdatensatzes. 
     */
    public function getId ()
    {
        return $this->id;
    }
    
    
    /**
     * Setzt die Identifikationsnummer des Wertpapierdatensatzes.
     * 
     * @param integer $id Identifikationsnummer des Wertpapierdatensatzes.
     * @return \HaHeLa\Modules_Model_Security
     */
    public function setId ($id)
    {
        $this->id = intval($id);
        return $this;
    }
    
    
    /**
     * Holt den Namen des Wertpapiers.
     * 
     * @return string Name des Wertpapiers.
     */
    public function getName ()
    {
        return $this->name;
    }
    
    
    /**
     * Setzt den Namen des Wertpapiers.
     * 
     * @param string Name des Wertpapiers.
     * @return \HaHeLa\Modules_Model_Security
     */
    public function setName ($name)
    {
        $this->name = strval($name);
        return $this;
    }
    
        
    /**
     * Holt den Typ des Wertpapiers. Folgende Wertpapiertypen sind moeglich:
     *   unbestimmt - indefinite
     *   Aktie - stock 
     *   Anleihe - bond
     *   Eigenkapital - equity 
     *   ETF - etf
     *   Fond - fund
     *   Zertifikat - certificate
     *   Optionsschein - warrant
     *   Rohstoff - commodity
     *   Vergleichsmaßstab (Aktienindex) - benchmark 	
     * @return string Typ des Wertpapiers. 
     */
    public function getType ()
    {
        return $this->type;
    }
    
    
    /**
     * Setzt den Typ des Wertpapiers.
     *
     * @param string $type Type des Wertpapiers.
     * @return \HaHeLa\Modules_Model_Security
     */
    public function setType ($type)
    {
        $this->type = strval($type);
        return $this;
    }
    
        
    /**
     * Holt die ISIN
     *   
     * @return string ISIN
     */
    public function getIsin ()
    {
        return $this->isin;
    }
    
    
    /**
     * Setzt die ISIN
     * 
     * @param string $isin ISIN
     * @return \HaHeLa\Modules_Model_Security
     */
    public function setIsin ($isin)
    {
        $this->isin = strval($isin);
        return $this;
    }
    
    
    /**
     * Holt die WKN
     *
     * @return string WKN
     */
    public function getWkn ()
    {
        return $this->wkn;
    }
    
    
    /**
     * Setzt die WKN
     *
     * @param string $wkn WKN
     * @return \HaHeLa\Modules_Model_Security
     */
    public function setWkn ($wkn)
    {
        $this->wkn = strval($wkn);
        return $this;
    }


    /**
     * Holt das Wertpapiersymbol
     *
     * @return string Symbol
     */
    public function getSymbol ()
    {
        return $this->symbol;
    }
    
    
    /**
     * Setzt das Wertpapiersymbol
     *
     * @param string $symbol Symbol
     * @return \HaHeLa\Modules_Model_Security
     */
    public function setSymbol ($symbol)
    {
        $this->symbol = strval($symbol);
        return $this;
    }
    
    
    /**
     * Holt den Reuters Instrument Code (RIC)
     *
     * @return string ric
     */
    public function getRic ()
    {
        return $this->ric;
    }
    
    
    /**
     * Setzt den Reuters Instrument Code (RIC)
     *
     * @param string $ric RIC
     * @return \HaHeLa\Modules_Model_Security
     */
    public function setRic ($ric)
    {
        $this->ric = strval($ric);
        return $this;
    }
    
    
    /**
     * Holt das Land der Wertpapierausgabe.
     *
     * @return string Land
     */
    public function getCountry ()
    {
        return $this->country;
    }
    
    
    /**
     * Setzt das Land der Wertpapierausgabe.
     *
     * @param string $country Land der Wertpapierausgabe.
     *  (bevorzugt wird die 2-alpha Abkuerzung z.B. DE fuer Deutschland).
     * @return \HaHeLa\Modules_Model_Security
     */
    public function setCountry ($country)
    {
        $this->country = strval($country);
        return $this;
    }
    
    
    /**
     * Holt die Branche 
     *
     * @return string Brance
     */
    public function getIndustry ()
    {
        return $this->industry;
    }
    
    
    /**
     * Setzt die Branche.
     *
     * @param string $industry Branche
     * @return \HaHeLa\Modules_Model_Security
     */
    public function setIndustry ($industry)
    {
        $this->industry = strval($industry);
        return $this;
    }
    
    
    /**
     * Holt den Sektor.
     *
     * @return string Sektor
     */
    public function getSector ()
    {
        return $this->sector; 
    }
    
    
    /**
     * Setzt den Sektor.
     *
     * @param string $sector Sektor
     * @return \HaHeLa\Modules_Model_Security
     */
    public function setSector ($sector)
    {
        $this->sector = strval($sector);
        return $this;
    }
    

    /**
     * Holt die Optionen
     *
     * @return array Optionen
     */
    public function getOptions ()
    {
        return $this->options;
    }
    
    
    /**
     * Setzt die Optionen
     *
     * @param array $options Optionen
     * @return \HaHeLa\Modules_Model_Security
     */
    public function setOptions ($options)
    {
        $this->options = $options;
        return $this;
    }
    
    
    /**
     * Ermittelt aus der ISIN eines Wertpapiers dessen Identifikationsnummer 
     *  in der Datenbank und speichert diese in die Objekteigenschaft '$id'. 
     *  Ist das Wertpapier mit der gegebenen ISIN NICHT in der Datenbank, 
     *  dann ist der Wert der Identifikationsnummer 0.
     *  
     * @return \HaHeLa\Modules_Model_Security
     */
    public function  determinesIdFromIsin () {
        $query = "SELECT id FROM {$this->config['database']['db_databasename']}.{$this->config['database']['db_tableprefix']}security WHERE isin=:isin";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':isin', $this->isin, \PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (FALSE === $row) $this->setId(0);
        else $this->setId($row['id']);
        return $this;
    }
    
    
    /**
     * Erzeugt einen Wertpapierdatensatz in der Datenbank.
     * 
     * @return integer Identifikationsnummer des Wertpapierdatensatzes
     *   in der Datenbank.
     */
    public function create ()
    {
        $query = "INSERT INTO {$this->config['database']['db_databasename']}.{$this->config['database']['db_tableprefix']}security (name,type,isin,wkn,symbol,ric,country,industry,sector) VALUES(:name,:type,:isin,:wkn,:symbol,:ric,:country,:industry,:sector)";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':name', $this->name, \PDO::PARAM_STR);
        $stmt->bindParam(':type', $this->type, \PDO::PARAM_STR);
        $stmt->bindParam(':isin', $this->isin, \PDO::PARAM_STR);
        $stmt->bindParam(':wkn', $this->wkn, \PDO::PARAM_STR);
        $stmt->bindParam(':symbol', $this->symbol, \PDO::PARAM_STR);
        $stmt->bindParam(':ric', $this->ric, \PDO::PARAM_STR);
        $stmt->bindParam(':country', $this->country, \PDO::PARAM_STR);
        $stmt->bindParam(':industry', $this->industry, \PDO::PARAM_STR);
        $stmt->bindParam(':sector', $this->sector, \PDO::PARAM_STR);
        $stmt->execute();
        $this->id = intval($this->dbh->lastInsertId());
        return $this->id;
    }
    
    
    /**
     * Aktualisiert den Wertpapierdatensatz. 
     * 
     * @return boolean Ergebnis der Aktualisierung. true - die Aktualisierung
     *   war erfolgreich. false - der Datensatz wurde nicht aktualisert.
     */
    public function update ()
    {
        $query = "UPDATE {$this->config['database']['db_databasename']}.{$this->config['database']['db_tableprefix']}security SET name=:name,type=:type,isin=:isin,wkn=:wkn,symbol=:symbol,ric=:ric,country=:country,industry=:industry,sector=:sector WHERE id=:id";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':name', $this->name, \PDO::PARAM_STR);
        $stmt->bindParam(':type', $this->type, \PDO::PARAM_STR);
        $stmt->bindParam(':isin', $this->isin, \PDO::PARAM_STR);
        $stmt->bindParam(':wkn', $this->wkn, \PDO::PARAM_STR);
        $stmt->bindParam(':symbol', $this->symbol, \PDO::PARAM_STR);
        $stmt->bindParam(':ric', $this->ric, \PDO::PARAM_STR);
        $stmt->bindParam(':country', $this->country, \PDO::PARAM_STR);
        $stmt->bindParam(':industry', $this->industry, \PDO::PARAM_STR);
        $stmt->bindParam(':sector', $this->sector, \PDO::PARAM_STR);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        return $stmt->execute();        
    }

    
    /**
     * Liest den Wertpapierdatensatz, der durch die Identifikationsnummer 
     *   in der Eigenschaft $id repraesentiert wird. Ist ein Datensatz 
	 *   mit der gegebenen Identifikationsnummer nicht in der Datenbank 
	 *   vorhanden, dann werden die Eigenschaften des Wertpapierobjekts
     *   mit vorgegebenen Werten gefuellt.	 
     *
     * @return Modules_Model_Security Gibt das Objekt selbst zurueck 
     *   (fluent interface).
     */
    public function read ()
    {
        $query = "SELECT * FROM {$this->config['database']['db_databasename']}.{$this->config['database']['db_tableprefix']}security WHERE id=:id";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
		if (false === $row) $this->resetProperties();
		else {
			$this->name = strval($row['name']);
			$this->type = strval($row['type']);
			$this->isin = strval($row['isin']);
			$this->wkn = strval($row['wkn']);
			$this->symbol = strval($row['symbol']);
			$this->ric = strval($row['ric']);
			$this->country = strval($row['country']);
			$this->industry = strval($row['industry']);
			$this->sector = strval($row['sector']);
		}
        return $this;
    }
    
    
    /**
     * Loescht den Wertpapierdatensatz aus der Datenbank. 
     *   Der zu loeschende Datensatz ist durch die Identifikationsnummer 
     *   in der Eigenschaft $id repraesentiert.
     *
     * @return boolean Die Methode liefert den boolschen Wert true, 
     *   falls die Daten geloescht werden konnten, sonst false.
     */
    public function delete ()
    {
        $query = "DELETE FROM {$this->config['database']['db_databasename']}.{$this->config['database']['db_tableprefix']}security WHERE id=:id";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->rowCount();
        return (0 < $count);
    }
    
    
    /**
     * Ueberprueft ob ein Wertpapier bereits in der Datenbank existiert.
     * 
     * @property array $options Assoziatives Feld (Map) mit den Suchoptionen 
     *   als Schluessel/Wert-Paare. Die Suchoptionen sind UND verknuepft.
     *   Die Bezeichnung der Schluessel muss mit dem entsprechenden 
     *   Tabellenspaltennamen in der Datenbank uebereinstimmen.
     * @return int|false Wenn das Wertpapier gefunden wird, dann wird dessen 
     *   Identifikationsnummer zurueckgegeben, sonst false.
     */
    public function exists ()
    {
        $query = "SELECT id FROM {$this->config['database']['db_databasename']}.{$this->config['database']['db_tableprefix']}security";
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
     * Sucht die Wertpapierdatensaetze deren Wert im Datenbankfeld
     *   'name' dem Namensfragment aehneln. 
     *   
     * @param string $nameFragment Fragment des Wertpapiernamens.
     * @return array Feld mit den Identifikationsnummern der 
     *   Wertpapierdatensaetze deren Wert im Datenbankfeld 'name' dem 
     *   Namensfragment aehneln.
     */
    public function searchByName ($nameFragment)
    {
        $nameFragment = '%' . $nameFragment . '%';
        $query = "SELECT id FROM {$this->config['database']['db_databasename']}.{$this->config['database']['db_tableprefix']}security";
        $whereClause = ' WHERE  name LIKE :name';  
        $stmt = $this->dbh->prepare($query . $whereClause);
        $stmt->bindParam(':name', $nameFragment, \PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $rows;
    }
    
    
    /**
     * Uebertraegt die wichtigsten Eigenschaften eines Wertpapierdatensatzes 
     *   in ein Objekt einer Standardklasse und gibt dieses Objekt zurueck.
     *
     * @param bool $withAdditions Optional Mit Ergaenzungen. 
     *  Wenn dieser Parameter TRUE ist, dann werden einige Wertpapierdaten 
     *  durch zusaetzliche Informationen ergaenzt, z.B. werden die aus zwei 
     *  oder drei Buchstaben bestehende Landeskennzeichnung um den deutschen 
     *  Landes-(Staats-)namen ergaenzt.
     * @return \stdClass
     */
    public function toStdObject ($withAdditions = FALSE): \stdClass
    {
        $output = new \stdClass();
        $output->id = intval($this->id);
        $output->name = strval($this->name);
        $output->type = strval($this->type);
        if ($withAdditions) {
            $options = array('code' => $output->type);
            $securityType = new Security_Type($this->dbh, $this->config, $options);
            if (0 < $id = $securityType->exists()) {
                $securityType->setId($id)->read();
                $output->type .= ' - ' . $securityType->getName();
            }
        }
        $output->isin = strval($this->isin);
        $output->wkn = strval($this->wkn);
        $output->symbol = strval($this->symbol);
        $output->ric = strval($this->ric);
        $output->country = strval($this->country);
        if ($withAdditions) {
            $option = array();
            $country = new Country($this->dbh, $this->config);
            if (0 < preg_match('/^[A-Za-z]{2}$/', $output->country)) {
                // 2-Buchstabenlandeskennung
                $option['alpha_2'] = $output->country;
            }
            elseif (0 < preg_match('/^[A-Za-z]{3}$/', $output->country)) {
                // 3-Buchstabenlandeskennung
                $option['alpha__3'] = $output->country;
            }
            $id = $country->setOptions($option)->exists();
            if (FALSE !== $id) {
              $output->country .= ' - ' . $country->setId($id)->read()->getName();
            }
        }
        $output->industry = strval($this->industry);
        $output->sector = strval($this->sector);
        return $output;
    }
    
    
    /**
     * Berechne die Pruefziffer einer ISIN.
     * @see http://www.pruefziffernberechnung.de/I/ISIN.shtml,
     *   https://www.bjoerns-choice.de/archives/302
     * 
     * @return integer Pruefziffer
     */
    public function calculateIsinCheckDigit (): int
    {
        $isin = strtoupper($this->isin);
        $s = ''; 
        for ($i = 0; $i <= 10; $i++) {
            $b = substr($isin, $i, 1);
            if (ord($b) >= 65 && ord($b) <= 90) 
                $b = ord($b) - 55; 
            $s .= $b;
        }
        $checkDigit = 0;
        $s = strrev($s);
        $len = strlen($s);
        for ( $i = 0; $i <= $len; $i++) {
            $b = (int) substr($s, $i, 1);
            if (!\bcmod((string) $i, '2')) 
                $b = $this->checksum($b * 2);
            $checkDigit += $b;
        }
        $checkDigit = 10 - \bcmod((string) $checkDigit, '10');
        $checkDigit = \bcmod((string) $checkDigit, '10');
        return (int) $checkDigit;
    }
    
    
    /**
     * Validiert die ISIN (International Securities Identification Number, 
     *  internationale Kennnummer fuer Wertpapiere).
     * 
     * @return bool Wenn die Pruefziffer einer der ISIN stimmt, dann gibt 
     *  die Methode den Wert TRUE zurueck, sonst FALSE. 
     */
    public function validateIsin (): bool
    {
        $checkDigit = $this->calculateIsinCheckDigit();
        return (intval(substr($this->getIsin(), -1, 1)) === $checkDigit);
    }
	
	
	/**
	 * Validiert die WKN (deutsche Wertpapierkennnummer).
	 * 
	 * @return bool Wenn die WKN den Anforderungen der einfachen Form 
	 * 	entspricht, dann liefert die Methode den Wert TRUE, sonst FALSE.
	 */
	public function validateWkn (): bool
	{
		if (1 === preg_match('/^[A-Z0-9]{6}$/', $this->getWkn())) return TRUE;
		else return FALSE;
	}

	    
    /**
     * Holt eine Liste der Wertpapiertypen.
     * 
     * @param string $language (optional) 2-Buchstabenkennung der Sprache. 
     *   Voreinstellung ist 'de' fuer Deutsch.
     * @param string $numerus (optional) Mengenangabe der Grammatik. 
     *   Moegliche Werte sind 'singular' oder 'plural'. Voreinstellung ist
     *   'singular'.
     * @return array Liste mit Wertpapiertypen: 
     *   array[
     *      \stdClass {
     *          id: <int>,
     *          code: <string>,
     *          name: <string>
     *      },
     *      \stdClass{...},
     *      ...
     *   ] 
     */
    public function getTypeList (string $language = 'de', string $numerus = 'singular'): array
    {
        $securityTypeList = array();
        $query = "SELECT id,code,name_{$language}_{$numerus} AS name FROM {$this->config['database']['db_databasename']}.{$this->config['database']['db_tableprefix']}security_type";
        $stmt = $this->dbh->prepare($query);
        $stmt->execute();
        while ($object = $stmt->fetch(\PDO::FETCH_OBJ)) {
            $securityTypeList[] = $object;
        }
        return $securityTypeList;
    }
    
    
    /**
     * Prueft, ob ein Eintrag in der Merkliste bereits existiert.
     * 
     * @param int $accountId Identifikationsnummer eines Benutzerkontos in 
     *  der Datenbank.
     * @return bool Wenn der Eintrag in der Merkliste bereits exisitert, 
     *  dann wird TRUE zurueck gegeben, sonst FALSE.
     */
    public function existsInWatchList ($accountId)
    {
        $query = "SELECT COUNT(*) AS count FROM {$this->config['database']['db_databasename']}.{$this->config['database']['db_tableprefix']}watchlist";
        $whereClause = ' WHERE account_id=:account_id AND security_id=:security_id';
        $query .= $whereClause;
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':account_id', $accountId, \PDO::PARAM_INT);
        $stmt->bindParam(':security_id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $count = intval($row['count']);
        return (0 < $count);
    }
    
    
    /**
     * Fuege eine Wertpapier-Identifikationsnummer zusammen mit einer 
     *  Benutzerkonto-Identifikationsnummer in die Datenbankmerkliste ein.
     *  Die Wertpapier-Identifikationsnummer muss sich in der Eigenschaft 
     *  'id' des Wertpapierobjektes befinden. Das Einfuegen erfolgt nur 
     *  dann, wenn die Kombination Benutzerkonto- und Werpapier-Id noch 
     *  NICHT in der Datenbank enthalten ist.
     *  
     * @param int $accountId Identifikationsnummer eines Benutzerkontos in 
     *  der Datenbank.
     * @return bool Wenn die Mehtode erfolg hatte, dann wird TRUE zurueck 
     *  gegeben, sonst FALSE.
     */
    public function addToWatchList ($accountId)
    {
        if (!$this->existsInWatchList($accountId)) {
            $query = "INSERT INTO {$this->config['database']['db_databasename']}.{$this->config['database']['db_tableprefix']}watchlist (account_id,security_id) VALUES(:account_id,:security_id)";
            $stmt = $this->dbh->prepare($query);
            $stmt->bindParam(':account_id', $accountId, \PDO::PARAM_INT);
            $stmt->bindParam(':security_id', $this->id, \PDO::PARAM_INT);
            return $stmt->execute();
        }
        return FALSE;
    }
    
    
    /**
     * Entfernt eine Wertpapier-Identifikationsnummer zusammen mit einer
     *  Benutzerkonto-Identifikationsnummer aus der Datenbankmerkliste.
     *  Die Wertpapier-Identifikationsnummer muss sich in der Eigenschaft
     *  'id' des Wertpapierobjektes befinden.
     *
     * @param int $accountId Identifikationsnummer eines Benutzerkontos in
     *  der Datenbank.
     * @return bool Wenn die Mehtode erfolg hatte, dann wird TRUE zurueck
     *  gegeben, sonst FALSE.
     */
    public function removeFromWatchList ($accountId)
    {
        $query = "DELETE FROM {$this->config['database']['db_databasename']}.{$this->config['database']['db_tableprefix']}watchlist";
        $whereClause = ' WHERE account_id=:account_id AND security_id=:security_id';
        $query .= $whereClause;        
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':account_id', $accountId, \PDO::PARAM_INT);
        $stmt->bindParam(':security_id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->rowCount();
        return (0 < $count);        
    }
    
    
    /**
     * Setzt ausgewaehlte Wertpapiereigenschaften auf vordefinierte Werte
     *     zurueck.
     *
     * @return HaHeLa\Modules_Model_Security
     */
    public function resetProperties ()
    {
        $this->id = 0;
        $this->name = '';
        $this->type = 'stock';
        $this->isin = '';
        $this->wkn = '';
        $this->symbol = '';
        $this->ric = '';
        $this->country = '';
        $this->industry = '';
        $this->sector = '';
        $this->options = array();
        return $this;
    }
    
    
    /************************************** private und geschuetzte Methoden */
    
    /**
     * Quersumme
     * 
     * @param int $number
     * @return int
     */
    private function checksum (int $number): int
    {
        $strNum = (string) $number;
        $len = strlen($strNum);
        for ($intCheckSum = $i = 0; $i < $len; $i++) {
            $intCheckSum += (int) $strNum[$i];
        }
        return (int) $intCheckSum;
    }
}
