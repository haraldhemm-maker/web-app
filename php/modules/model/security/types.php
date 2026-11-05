<?php


/**
 * php/modules/model/security/types.php
 *
 * Security Types Model - Modell der Wertpapiertypen (pluaral).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


use HaHeLa\SecurityTypeModel as SecurityType;
use HaHeLa\SecurityTypesModel as SecurityTypes;


/**
 * Security Types Model - Modell der Wertpapiertypen (pluaral).
 */
class SecurityTypesModel implements SecurityTypesInterface
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
     * Konstruktor
     *
     * @param \PDO $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param array $options Optionen.
     */
    public function __construct(\PDO $dbh, array $config, array $options = array())
    {
        $this->dbh = $dbh;
        $this->config = $config;
        $this->options = $options;
    }
    

    /**
     * Holt eine Liste aller Wertpapiertypen als Objekte der Standardklasse.
     * 
     * @return array Ein indexiertes Feld mit Objekten der Standardklasse 
     *  die Wertpapiertypdaten enthaelt.
     */
    public function getStdObjList(): array
    {
        $query = "SELECT id FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}security_type";
        $stmt = $this->dbh->prepare($query);
        $stmt->execute();
        $list = array();
        $type = new SecurityTypeModel($this->dbh, $this->config);
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $id = intval($row['id']);
            $list[] = $type->setId($id)->read()->toStdObject();
        }
        return $list;        
    }
}