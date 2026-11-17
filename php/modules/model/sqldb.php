<?php
/**
 * php/modules/model/sqldb.php
 *
 * SQL (Structured Query Language) DB (DataBase) Model - 
 *   SQL Datenbank Besonderheiten.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 2.0.0
 * @since 1.0.0
 */
namespace HaHeLa;


/**
 * SQL DB Model - SQL Datenbank Besonderheiten.
 *
 */
class SqldbModel implements SqldbInterface
{
    /**
     * Konstanten
     */
    const CASE_LOWER   = 1;
    const CASE_UPPER   = 2;
    const CASE_NATURAL = 3;


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
     * Optionen
     */
    protected array $options;

    /**
     * Specifies the case of strings.
     *   Options:
     *   HaHeLa\SqlDbModel::CASE_NATURAL
     *   HaHeLa\SqlDbModel::CASE_LOWER
     *   HaHeLa\SqlDbModel::CASE_UPPER
     *
     * @var int
     * @default \HaHeLa\SqlDbModel::CASE_NATURAL (default)
     */
    private int $caseFolding = self::CASE_NATURAL;


    /**
     * Constructor
     *
     * @param \PDO $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten
     * @param array $options Unbenutzt.
     */
    public function __construct(\PDO $dbh, array $config, array $options)
    {
        $this->dbh = $dbh;
        $this->config = $config;
        $this->options = $options;
    }


    /**
     * Returns the default value of the enumeration
     *
     * @param string $tableName
     * @param string $columnName
     * @return string Default enumeration
     */
    public function getDefaultEnumeration(string $tableName, string $columnName): string
    {
        $description = $this->describeTable($tableName);
        return (string)$description[$columnName]['DEFAULT'];
    }


    /**
     * Returns the enumerations.
     *
     * @param string $tableName
     * @param string $columnName
     * @return array|FALSE Enumerations
     */
    public function getEnumerations(string $tableName, string $columnName): array|FALSE
    {
        $enumerations = array();
        $description = $this->describeTable($tableName);
        $dataType = (string)$description[$columnName]['DATA_TYPE'];
        $match = array();
        if (preg_match('/enum.(.*)./', $dataType, $match)) {
            $opts = explode('\',', $match[1]);
            foreach ($opts as $item) {
                $item = str_replace('\'', '', $item);
                $enumerations[] = substr($item, 0, strlen($item));
            }
            return $enumerations;
        }
        else return false;
    }


	/**
	 * A wrapper function that calls the original function 
	 * 	getDefaultInteger.
     * 
     * @param string $tableName
     * @param string $columnName
     * @return int
	 */
	public function getDefaultInt(string $tableName, string $columnName): int
	{
		return $this->getDefaultInteger($tableName, $columnName);
	}
	
	
    /**
     * Returns the default value of integer fields
     *
     * @param string $tableName
     * @param string $columnName
     * @return integer Default integer value
     */
    public function getDefaultInteger(string $tableName, string $columnName): int
    {
        $description = $this->describeTable($tableName);
        return intval($description[$columnName]['DEFAULT']);
    }


    /**
     * Returns the default value of float fields
     *
     * @param string $tableName
     * @param string $columnName
     * @return float Default float value
     */
    public function getDefaultFloat(string $tableName, string $columnName): float
    {
        $description = $this->describeTable($tableName);
        return floatval($description[$columnName]['DEFAULT']);
    }


    /**
     * Return the default value of varchar fields.
     *
     * @param string $tableName
     * @param string $columnName
     * @return string Default string value
     */
    public function getDefaultVarchar(string $tableName, string $columnName): string
    {
        $description = $this->describeTable($tableName);
        return strval($description[$columnName]['DEFAULT']);
    }
	
	
	/**
	 * Return the default value of date fields.
	 *
	 * @param string $tableName
	 * @param string $columnName
	 * @return string Default string value
	 */
	public function getDefaultDate(string $tableName, string $columnName): string
	{
	    $description = $this->describeTable($tableName);
	    return strval($description[$columnName]['DEFAULT']);
	}


    /**
     * Returns the schema name of the selected database.
     *
     * @return string Schema name.
     */
    public function getSelectedDatabase(): string
    {
        $query = 'SELECT DATABASE()';
        $stmt = $this->dbh->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $schemaName = strval($row['DATABASE()']);
        return $schemaName;
    }


    /**
     * Returns the column descriptions for a table.
     *
     * The return value is an associative array keyed by the column name,
     * as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * SCHEMA_NAME      => string; name of database or schema
     * TABLE_NAME       => string;
     * COLUMN_NAME      => string; column name
     * COLUMN_POSITION  => number; ordinal position of column in table
     * DATA_TYPE        => string; SQL datatype name of column
     * DEFAULT          => string; default expression of column, null if none
     * NULLABLE         => boolean; true if column can have nulls
     * LENGTH           => number; length of CHAR/VARCHAR
     * SCALE            => number; scale of NUMERIC/DECIMAL
     * PRECISION        => number; precision of NUMERIC/DECIMAL
     * UNSIGNED         => boolean; unsigned property of an integer type
     * PRIMARY          => boolean; true if column is part of the primary key
     * PRIMARY_POSITION => integer; position of column in primary key
     * IDENTITY         => integer; true if column is auto-generated with unique values
     *
     * @param string $tableName
     * @return array
     */
    public function describeTable(string $tableName): array
    {
        $query = "SHOW COLUMNS FROM $tableName";
        $stmt = $this->dbh->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $field   = 0;
        $type    = 1;
        $null    = 2;
        $key     = 3;
        $default = 4;
        $extra   = 5;
        $desc = array();
        $i = 1;
        $p = 1;
        foreach ($rows as $row) {
            list($length, $scale, $precision, $unsigned,
                $primary, $primaryPosition, $identity
            ) = array(null, null, null, null, false, null, false);
            $matches = array();
            if (preg_match('/unsigned/', $row[$type])) {
                $unsigned = true;
            }
            if (preg_match(
                '/^((?:var)?char)\((\d+)\)/', $row[$type], $matches
            )) {
                $row[$type] = $matches[1];
                $length = $matches[2];
            }
            else if (preg_match(
                '/^decimal\((\d+),(\d+)\)/', $row[$type], $matches
            )) {
                $row[$type] = 'decimal';
                $precision = $matches[1];
                $scale = $matches[2];
            } else if (preg_match(
                '/^float\((\d+),(\d+)\)/', $row[$type], $matches
            )) {
                $row[$type] = 'float';
                $precision = $matches[1];
                $scale = $matches[2];
            } else if (preg_match(
                '/^((?:big|medium|small|tiny)?int)\((\d+)\)/', $row[$type], $matches
            )) {
                $row[$type] = $matches[1];
                $scale = $matches[2];
                // The optional argument of a MySQL int type is not precision
                // or length; it is only a hint for display width.
            }
            if (strtoupper($row[$key]) == 'PRI') {
                $primary = true;
                $primaryPosition = $p;
                if ($row[$extra] == 'auto_increment') {
                    $identity = true;
                } else {
                    $identity = false;
                }
                ++$p;
            }
            $desc[$this->foldCase($row[$field])] = array(
                'SCHEMA_NAME'      => $this->foldCase($this->getSelectedDatabase()),
                'TABLE_NAME'       => $this->foldCase($tableName),
                'COLUMN_NAME'      => $this->foldCase($row[$field]),
                'COLUMN_POSITION'  => $i,
                'DATA_TYPE'        => $row[$type],
                'DEFAULT'          => $row[$default],
                'NULLABLE'         => (bool) ($row[$null] == 'YES'),
                'LENGTH'           => $length,
                'SCALE'            => $scale,
                'PRECISION'        => $precision,
                'UNSIGNED'         => $unsigned,
                'PRIMARY'          => $primary,
                'PRIMARY_POSITION' => $primaryPosition,
                'IDENTITY'         => $identity
            );
            ++$i;
        }
        return $desc;
    }
	

	/**
	 * Returns a column descriptions.
	 * 
	 * @param string $tableNmae 
	 * @param string $columnName
	 * @return array
	 */	
	public function describeColumn(string $tableName, string $columnName): array
	{
		$tabDescriptions = $this->describeTable($tableName);
		foreach ($tabDescriptions as $tabDescription) {
			if ($tabDescription['COLUMN_NAME'] === $columnName) {
				return $tabDescription;
			}
		}
		return [];
	}


    /**
     * Helper method to change the case of the strings used
     *   when returning result sets.
     *
     * @param string $key
     * @return string
     */
    public function foldCase(string $key): string
    {
        switch ($this->caseFolding) {
            case self::CASE_LOWER:
                $value = strtolower((string)$key);
                break;
            case self::CASE_UPPER:
                $value = strtoupper((string)$key);
                break;
            case self::CASE_NATURAL:
            default:
                $value = (string)$key;
        }
        return $value;
    }


    /**
     * Set case folding
     *
     * @param int $caseFolding Possible values are CASE_LOWER, CASE_UPPER,
     *   CASE_NATRUAL
     * @return \HaHeLa\SqldbModel Fluent interface
     */
    public function setCaseFolding(int $caseFolding = self::CASE_NATURAL): \HaHeLa\SqldModel
    {
        $this->caseFolding = $caseFolding;
        return $this;
    }
}