<?php


/**
 * php/modules/model/security/type.php
 *
 * Security Type Model - Modell eines Wertpapiertyps (singular).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


use HaHeLa\SecurityTypeModel as SecurityType;


/**
 * Security Type Model - Modell eines Wertpapiertyps (singular).
 */
class SecurityTypeModel implements SecurityTypeInterface
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
     * Konfiguration- oder Suchoptionen.
     * @var array
     */
    protected array $options = array();
    
    /**
     * Identifikationsnummer des Wertpapiertypdatensatzes in der Datenbank.
     * @var int
     * @default 1 Dies ist keine gueltige Identifikationsnummer eines 
     *  Wertpapiertypdatensatzes.
     */
    protected int $id = 0;
    
    /**
     * Wertpapierschluessel (auch als Wertpapiertyp bezeichnet).
     * Z.B. Wertpapiertyp ist 'stock' => Wertpapierbezeichner ist 'Aktie'.
     * @var string
     * @default Leere Zeichenkette.
     */
    protected string $code = '';
    
    /**
     * Klassifizierung von Finanzinstrumenten - ISO 10962
     *  (Classification of Financial Instruments).
     * @var string char(2)
     * @default Leere Zeichenkette.
     */
    protected string $cfi_code = '';

    /**
     * Englische Bezeichnung des Wertpapiertyps.
     * @var string
     * @default Leere Zeichenkette
     */
    protected string $name_en = '';

    /**
     * Wertpapierbezeichnung in Deutsch (Einzahl)
     *  Z.B. Wertpapiertyp ist 'stock' => Deutscher Wertpapierbezeichner 
     *  Singular ist 'Aktie'.
     * @var string
     * @default Leere Zeichenkette
     */
    protected string $name_de_singular = '';
    
    /** 
     * Wertpapierbezeichnung in Deutsch (Mehrzahl)
     *  Z.B. Wertpapiertyp ist 'stock' => Deutscher Wertpapierbezeichner 
     *  Plural ist 'Aktien'.
     * @var string
     * @default Leere Zeichenkette
     */
    protected $name_de_plural = '';
    
    /**
     * Sortierreihenfolge.
     * @var int
     * @default 0 (bedeutet undefiniert, denn die Zaehlung beginnt bei 1)
     */
    protected int $sort = 0; 
    
        
    /**
     * Konstruktor
     *
     * @param \PDO $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param array $options Optionale Konfigurationsparameter.
     *
     * @param int $id (optional) Identifikationsnummer eines
     *   Wertpapiertypdatensatzes in der Datenbank. Wenn dieser Parameter
     *   vorhanden ist, dann werden die Daten des entsprechenden
     *   Wertpapiertyps aus der Datenbank gelesen und die Objekteigenschaften
     *   entsprechend initialisiert.
     */
    public function __construct (\PDO $dbh, array $config, array $options = array())
    {
        $this->dbh = $dbh;
        $this->config = $config;
        $this->options = $options;
        if (3 < \func_num_args()) { // Mehr als drei Parameter wurden angegeben.
            if (4 == \func_num_args()) { // Parameteranzahl ist 4
                $this->id = intval(\func_get_arg(3));
                $this->read();
            }
            else {
                // Option auf mehr als 4 Parameter.
            }
        }
        else { // Drei oder weniger Parameter wurden angegeben.
            // $this->id ist voreingestellt 1. Dies ist die 
            //  Identifikationsnummer des 'indefinite' bzw.
            //  'unbestimmten' Wertpapiertypdatensatzes, der 
            //  hier gelesen wird.
            $this->read();
        }
    }
    
    
    /**
     * Liest den Wertpapiertypdatensatz, der durch die Identifikationsnummer
     *   in der Eigenschaft $id repraesentiert wird. Ist ein Datensatz
     *   mit der gegebenen Identifikationsnummer nicht in der Datenbank
     *   vorhanden, dann werden die Eigenschaften des Wertpapiertypobjekts
     *   mit vorgegebenen Werten gefuellt.
     *
     * @return SecurityTypeModel Gibt das Objekt selbst zurueck
     *   (fluent interface).
     */
    public function read (): SecurityTypeModel
    {
        $query = "SELECT * FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}security_type WHERE id=:id";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (false === $row) $this->resetProperties();
        else {
            $this->code = strval($row['code']);
            $this->cfi_code = strval($row['cfi_code']);
            $this->name_en = strval($row['name_en']);
            $this->name_de_singular = strval($row['name_de_singular']);
            $this->name_de_plural = strval($row['name_de_plural']);
            $this->sort = intval($row['sort']);
        }
        return $this;
    }
    
    
    /**
     * Setzt ausgewaehlte Wertpapiertypeigenschaften auf vordefinierte 
     *  Werte zurueck.
     *
     * @return HaHeLa\SecurityTypeModel
     */
    public function resetProperties (): SecurityTypeModel
    {
        $this->options = array();
        $this->id = 0; // 0 ist keine gueltige Identifikationsnummer.
        $this->code = '';
        $this->cfi_code = '';
        $this->name_en = '';
        $this->name_de_singular = '';
        $this->name_de_plural = '';
        $this->sort = 0; // Kein gueltiger Wert der Sortierung.
        return $this;
    }
    
    
    /**
     * Uebertraegt die wichtigsten Eigenschaften eines 
     *  Wertpapiertypdatensatzes in ein Objekt einer Standardklasse 
     *  und gibt dieses Objekt zurueck.
     *
     * @return \stdClass
     */
    public function toStdObject (): \stdClass
    {
        $output = new \stdClass();
        $output->id = intval($this->id);
        $output->options = $this->options;
        $output->code = strval($this->code);
        $output->cfi_code = strval($this->cfi_code);
        $output->name_en = strval($this->name_en);
        $output->name_de_singular = strval($this->name_de_singular);
        $output->name_de_plural = strval($this->name_de_plural);
        $output->sort = intval($this->sort);
        return $output;
    }
    
    
    /**
     * Ueberprueft, ob ein Wertpapiertyp bereits in der Datenbank existiert.
     *
     * @property array $options Assoziatives Feld (Map) mit den Suchoptionen
     *   als Schluessel/Wert-Paare. Die Suchoptionen sind UND verknuepft.
     *   Die Bezeichnung der Schluessel muss mit dem entsprechenden
     *   Tabellenspaltennamen in der Datenbank uebereinstimmen.
     * @return int|false Wenn der Wertpapiertyp gefunden wird, dann wird 
     *  dessen Identifikationsnummer zurueckgegeben, sonst FALSE.
     */
    public function exists (): int|false
    {
        $query = "SELECT id FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}security_type";
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
}