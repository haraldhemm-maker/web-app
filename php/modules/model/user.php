<?php
/**
 * php/modules/model/user.php
 *
 * User Model - Benutzerdaten und Benutzerkonto.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */
namespace HaHeLa;


/**
 * Account Model - Benutzerkonto.
 */
class UserModel implements UserInterface
{
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
     * @var array
     */
    private array $options;
    
    /**
     * Identifikationsnummer des Datensatzes in der Datenbank.
     * @var integer
     */
    private int $id;
    
    /**
     * Subject ist die Identifikationsnummer des Benutzerdatensatzes beim 
     *  OAuth2-Server und dient als Bindeglied zwischen den Daten des 
     *  OAuth2-Servers und den Daten des lokalen Servers.
     * @var integer
     */
    private int $sub;
    
    /**
     * Vorname des Benutzers
     * @var string
     */
    private string $firstName;
    
    /**
     * Nachname des Benutzers
     * @var string
     */
    private string $lastName;
    
    /**
     * Erstellungsdatum
     * @var string Format TT.MM.YYYY
     * @default '' Leere Zeichenkette
     */
    private string $created = '';
    
    /**
     * Datum der letzten Aenderung
     * @var string Format TT.MM.YYYY
     * @default '' Leere Zeichenkette
     */
    private string $modified = '';

        
    /**
     * Constructor
     *
     * @param resource $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param array $options optional Optionen
     *
     * @param integer $id (optional) Identifikationsnummer eines
     *   User-Datensatzes in der Datenbank.
     *
     * @return UserModel
     */
    public function __construct ($dbh, $config, $options = array())
    {
        $this->dbh = $dbh;
        $this->config = $config;
        $this->options = $options;
        if (3 < func_num_args()) {
            if (4 == func_num_args()) {
                $this->id = intval(func_get_arg(3));
                $this->read();
            }
        }
    }
    
    
    /**
     * Setzt die Identifikationsnummer des Benutzerkontos.
     * 
     * @param int $id Identifikationsnummer.
     * @return UserModel
     */
    public function setId(int $id): UserModel
    {
        $this->id = $id;
        return $this;
    }
    
    
    /**
     * Setzt die Identifikationsnummer (Subject) vom OAuth2-Server.
     * 
     * @param int $id Subject/Identifikationsnummer.
     * @return UserModel
     */
    public function setSub(int $sub): UserModel
    {
        $this->sub = $sub;
        return $this;
    }
    
    
    /**
     * Setzt den Vornamen des Benutzers.
     * 
     * @param stirng $firstName Vorname des Benutzers.
     * @return UserModel
     */
    public function setFirstName(string $firstName): UserModel
    {
        $this->firstName = $firstName;
        return $this;
    }
    
    
    /**
     * Setzt den Nachnamen des Benutzers.
     * 
     * @param string $lastName Nachname des Benutzers.
     * @return UserModel
     */
    public function setLastName(string $lastName): UserModel
    {
        $this->lastName = $lastName;
        return $this;
    }
    
    
    /**
     * Holt Informationen ueber den aktuellen Benutzer aus der Datenbank des 
     *  OAuth-Servers.
     * 
     * @return array Assoziatives Array mit den folgenden Informationen 
     *  ueber den Benutzer in der Datenbank des OAuth-Servers.
     */
    public function getUserInfoFromOAuthServer(): array
    {
        $accessToken = $_SESSION['access_token'];
        $ch = curl_init('http://localhost:8000/index.php?action=userinfo');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $accessToken]
        ]);
        // $userInfo['username'], $userInfo['role'], $userInfo['sub']
        $userInfo = json_decode(curl_exec($ch), true);
        return $userInfo;
    }
    
    
    /**
     * Ueberprueft, ob ein Benutzer mit den Eigenschaften im $criteria-Array
     *  in der lokalen Datenbank existiert. Die Eigenschaften im $criteria-
     *  Array werden bei der Suche in der lokalen Datenbank UND-verknuepft.
     *  Die ID des gefundenen Benutzers wird als einziger Eintrag in einem 
     *  Array zurueckgegeben. Wenn mehr als ein Benutzer mit den Eigenschaften 
     *  ermittelt wird, dann werden alle IDs dieser Benutzer in einem Array
     *  zuruckgegeben. Wenn KEIN Benutzer mit den Eigenschaften gefunden wird, 
     *  dann wird ein leeres Array zurueckgegeben.
     * 
     * @param array $criteria Ein Array mit den Eigenschaften mit denen der 
     *  Benutzer in der lokalen Datenbank gesucht wird.
     * @return Array Ein Array mit den IDs der gefundenen Benutzer.
     * @throws \InvalidArgumentException Wenn nach einer Eigenschaft in einer 
     *  nicht erlaubten Spalte gesucht wird, dann wird eine Exception 
     *  ausgeloest.
     */
    public function exists(array $criteria): array
    {
        if (empty($criteria)) {
            return [];
        }
        // Nur bekannte Spalten zulassen 
        //  (Schutz vor SQL-Injection ueber Spaltennamen)
        $allowedColumns = ['id', 'sub', 'first_name', 'last_name'];

        $columns = [];
        $values  = [];
        foreach ($criteria as $column => $value) {
            if (!in_array($column, $allowedColumns, true)) {
                throw new \InvalidArgumentException("Ungültige Spalte: $column");
            }
            $columns[] = "`$column` = ?";
            $values[]  = $value;
        }

        $query  = 'SELECT `id` FROM `wa_user` WHERE ' . implode(' AND ', $columns);
        $stmt = $this->dbh->prepare($query);
        $stmt->execute($values);

        // fetchAll mit PDO::FETCH_COLUMN liefert direkt ein Array von IDs
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    
    
    /**
     * Erzeugt einen Benutzekonto-Datensatzes in der lokalen Datenbank.
     * 
     * @return int Identifikationsnummer des neuen 
     *  Benutzerkonto-Datensatzes in der lokalen Datenbank.
     */
    public function create(): int
    {
        $query = "INSERT INTO {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}user (sub,first_name,last_name) VALUES(:sub,:first_name,:last_name)";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':sub', $this->sub, \PDO::PARAM_INT);
        $stmt->bindParam(':first_name', $this->firstName, \PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $this->lastName, \PDO::PARAM_STR);
        $stmt->execute();
        $this->id = intval($this->dbh->lastInsertId());
        return $this->id;
    }
    
    
    /**
     * Liest den User-Datensatz, der durch die Identifikationsnummer in der
     *   Eigenschaft $id repraesentiert wird.
     *
     * @return UserModel Gibt das Objekt selbst zurueck 
     *  (fluent interface).
     */
    public function read(): UserModel
    {
        $query = "SELECT * FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}user WHERE id=:id";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        $this->sub = intval($row['sub']);
        $this->firstName = strval($row['first_name']);
        $this->lastName = strval($row['last_name']);
        $this->created = strval($row['created']);
        $this->modified = strval($row['modified']);
        return $this;
    }
    
    
    /**
     * Aktualisiert den Benutzerkonto-Datensatz in der lokalen Datenbank. 
     * 
     * @return bool Ergebnis der Aktualisierung. true - die Aktualisierung
     *   war erfolgreich. false - der Datensatz wurde nicht aktualisert.
     */
    public function update(): bool
    {
        $query = "UPDATE {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}user SET sub=:sub,first_name=:first_name,last_name=:last_name WHERE id=:id";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':sub', $this->sub, \PDO::PARAM_INT);
        $stmt->bindParam(':first_name', $this->firstName, \PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $this->lastName, \PDO::PARAM_STR);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    
    /**
     * Loescht einen Benutzerkonto-Datensatz. 
     *   Der zu loeschende Datensatz ist durch die Identifikationsnummer 
     *   in der Eigenschaft $id repraesentiert.
     *
     * @return bool Die Methode liefert den boolschen Wert true, 
     *   falls die Daten geloescht werden konnten, sonst false.
     */
    public function delete (): bool
    {
        $query = "DELETE FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}user WHERE id=:id";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->rowCount();
        return (0 < $count);
    }
    
    
    /**
     * Uebertraegt die wichtigsten Eigenschaften eines lokalen Benutzerkontos 
     *  in ein Objekt einer Standardklasse und gibt dieses Objekt zurueck.
     *
     * @return \stdClass
     */
    public function toStdObject(): \stdClass
    {
        $output = new \stdClass();
        $output->id = intval($this->id);
        $output->sub = intval($this->sub);
        $output->firstName = $this->firstName;
        $output->lastName = $this->lastName;
        $output->created = $this->created;
        $output->modified = $this->modified;
        return $output;
    }


    /**
     * Uebertraegt die wichtigsten Eigenschaften eines lokalen Benutzerkontos 
     *  in ein assoziatives Array und gibt dieses Array zurueck.
     *
     * @return array
     */
    public function toArray(): array
    {
        $output = array(
            'id' => intval($this->id),
            'sub' => intval($this->sub),
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'created' => $this->created,
            'modified' => $this->modified
        );
        return $output;
    }

    
  /* ********************************************** private (Hilfs-)Methoden */
}