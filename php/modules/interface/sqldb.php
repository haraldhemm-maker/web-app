<?php


/**
 * php/modules/interface/sqldb.php
 *
 * SQL (Structured Query Language) DB (DataBase) Interface - 
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
 * SQL DB Interface - SQL Datenbank Besonderheiten.
 */
interface SqldbInterface
{
    /**
     * Constructor
     *
     * @param \PDO $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten
     * @param array $options Unbenutzt.
     */
    public function __construct(\PDO $dbh, array $config, array $options);


    /**
     * Returns the default value of the enumeration
     *
     * @param string $tableName
     * @param string $columnName
     * @return string Default enumeration
     */
    public function getDefaultEnumeration(string $tableName, string $columnName): string;


    /**
     * Returns the enumerations.
     *
     * @param string $tableName
     * @param string $columnName
     * @return array|FALSE Enumerations
     */
    public function getEnumerations(string $tableName, string $columnName): array|FALSE;


    /**
     * A wrapper function that calls the original function 
     *  getDefaultInteger.
     * 
     * @param string $tableName
     * @param string $columnName
     * @return int
     */
    public function getDefaultInt(string $tableName, string $columnName): int;


    /**
     * Returns the default value of integer fields
     *
     * @param string $tableName
     * @param string $columnName
     * @return integer Default integer value
     */
    public function getDefaultInteger(string $tableName, string $columnName): int;


    /**
     * Returns the default value of float fields
     *
     * @param string $tableName
     * @param string $columnName
     * @return float Default float value
     */
    public function getDefaultFloat(string $tableName, string $columnName): float;


    /**
     * Return the default value of varchar fields.
     *
     * @param string $tableName
     * @param string $columnName
     * @return string Default string value
     */
    public function getDefaultVarchar(string $tableName, string $columnName): string;


    /**
     * Return the default value of date fields.
     *
     * @param string $tableName
     * @param string $columnName
     * @return string Default string value
     */
    public function getDefaultDate(string $tableName, string $columnName): string;


    /**
     * Returns the schema name of the selected database.
     *
     * @return string Schema name.
     */
    public function getSelectedDatabase(): string;


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
    public function describeTable(string $tableName): array;


    /**
     * Returns a column descriptions.
     * 
     * @param string $tableNmae 
     * @param string $columnName
     * @return array
     */ 
    public function describeColumn(string $tableName, string $columnName): array;


    /**
     * Helper method to change the case of the strings used
     *   when returning result sets.
     *
     * @param string $key
     * @return string
     */
    public function foldCase(string $key): string;


    /**
     * Set case folding
     *
     * @param int $caseFolding Possible values are CASE_LOWER, CASE_UPPER,
     *   CASE_NATRUAL
     * @return \HaHeLa\SqldbModel Fluent interface
     */
    public function setCaseFolding(int $caseFolding = self::CASE_NATURAL): \HaHeLa\SqldModel;
}