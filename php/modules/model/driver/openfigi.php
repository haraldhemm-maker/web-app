<?php


/**
 * php/modules/model/driver/openfigi.php
 *
 * Open FIGI Model - Modell zur Bestimmung der Globalen Identifikatoren fuer 
 *  Finanzinstrumente (Open Financial Instrument Global Identifier OpenFIFI) 
 *  (singular).
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


/**
 * Open FIGI Model - Modell zur Bestimmung der Globalen Identifikatoren fuer 
 *  Finanzinstrumente (Open Financial Instrument Global Identifier OpenFIFI) 
 *  (singular).
 */
class DriverOpenfigiModel implements DriverOpenfigiInterface
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
     * @default array() Leeres Array
     */
    protected array $options = array();
    
    
    /**
     * Financial Instrument Global Identifier.
     *  Ein FIGI ist ein offener, zwoelfstelliger alphanumerischer Standard 
     *  zur weltweiten und eindeutigen Identifizierung von Finanzinstrumenten 
     *  ueber alle Anlageklassen hinweg an einem konkreten Handelsplatz. Der 
     *  Code ist zufällig generiert und enthaelt keine „inherente Intelligenz“ 
     *  (er verraet also nicht auf den ersten Blick das Land oder die Bank, 
     *  sondern dient als reiner Verweis).
     * @var string
     */
    protected string $figi = '';
    
    /**
     * Die Composite-FIGI ist eine eindeutige Kennung fuer eine Aktie 
     *  innerhalb eines bestimmten Landes oder einer bestimmten 
     *  Wirtschaftsregion. Sie verbindet alle identischen Wertpapiere, die 
     *  in diesem Land an verschiedenen Handelsplaetzen gehandelt werden.
     *  Die compositeFIGI repraesentiert das Wertpapier auf Laenderebene,
     *  waehrend die normale figi den konkreten Handelsplatz (die Boerse) 
     *  abbildet.
     */
    protected string $composite_figi = '';
    
    /**
     * Tickersymbol (kurz Ticker) ist eine eindeutige Kurzbezeichnung aus 
     *  Buchstaben (selten auch Zahlen), die ein boersennotiertes Wertpapier, 
     *  eine Aktie oder einen Rohstoff an einer bestimmten Boerse 
     *  identifiziert.
     * @var string
     */
    protected string $ticker = '';
    
    /**
     * Der exchCode in OpenFIGI steht fuer den Exchange Code (Boersencode). 
     *  Es handelt sich um den Bloomberg-Composite-Code. Dies ist ein 
     *  Bloomberg-eigener Boersen-Kuerzel (2-,3-Zeichen) und gibt die 
     *  spezifische Boerse oder den Handelsplatz an, an dem das jeweilige 
     *  Finanzinstrument gelistet ist oder gehandelt wird.
     * @var string
     */
    protected string $exch_code = '';
    
    /**
     * In OpenFIGI entspricht der securityType der genauen Klassifizierung 
     *  oder dem Produkttyp des Finanzinstruments. Dieses Feld dient als 
     *  zentrales Merkmal fuer Stammdaten-Systeme (Instrument Master), um 
     *  Wertpapiere praezise nach ihrer Rechtsform, Struktur und ihren 
     *  Handelseigenschaften zu kategorisieren.
     * 
     *  Um die Datenarchitektur von Bloomberg und OpenFIGI zu verstehen, muss 
     *  man den securityType im Zusammenspiel mit zwei weiteren hierarchischen 
     *  Feldern betrachten:
     *  OpenFIGI grenzt Finanzinstrumente von der groebsten bis zur feinsten 
     *  Kategorie ueber drei Felder ab:
     *      1. marketSector (Die Anlageklasse / Das Asset-Asset): Die oberste 
     *          Ebene (z. B. Equity fuer Aktien/ETFs, Govt für Staatsanleihen, 
     *          Corp fuer Unternehmensanleihen oder Commodity).
     *      2. securityType2 (Die grobe Instrumentenart): Eine Zwischenebene, 
     *          die oft weniger spezifisch ist (z. B. Common Stock, Option 
     *          oder Mutual Fund).
     *      3. securityType (Der spezifische Wertpapiertyp): Die granularste 
     *          Angabe, die exakt definiert, um was es sich handelt.
     * @var string
     */
    protected string $security_type = '';
    protected string $security_type_2 = '';
    
    /**
     * Der marketSector legt fest, in welchem uebergeordneten Marktsegment 
     *  sich das Instrument bewegt (z.B. auf dem Aktienmarkt).
     *  Die 11 offiziellen Bloomberg Market Sectors
     *  Jedes Wertpapier weltweit wird bei Bloomberg zwingend in genau einen 
     *  dieser 11 vordefinierten Sektoren eingeteilt. In API-Antworten oder 
     *  im Terminal tauchen diese Bezeichnungen immer als fester String auf:
     *  1. Equity (Aktienmarkt): Stammaktien, Vorzugsaktien, ETFs, 
     *      Investmentfonds, Optionsscheine (Warrants) und Zertifikate.
     *  2. Corp (Unternehmensanleihen / Corporate Bonds): Festverzinsliche 
     *      Wertpapiere, die von privaten oder oeffentlich-rechtlichen 
     *      Unternehmen herausgegeben werden.
     *  3. Govt (Staatsanleihen / Government Bonds): Anleihen von Staaten, 
     *      Provinzen, Kommunen oder supranationalen Organisationen (z.B. 
     *      EU, Weltbank).
     *  4. Mortgage (Hypothekenbesicherte Wertpapiere / MBS): Verbriefte 
     *      Kredit- und Hypothekenpools (sehr verbreitet im US-Finanzmarkt).
     *  5. Money Market (Geldmarkt): Sehr kurzfristige, hochliquide 
     *      Zinsinstrumente wie Schatzanweisungen (Treasury Bills) oder 
     *      Commercial Papers mit Laufzeiten von meist unter einem Jahr.
     *  6. Preferred (Vorzugstitel): Hybride Finanzinstrumente, die 
     *      Eigenschaften von Aktien und Anleihen mischen (oft 
     *      laenderspezifische Vorzugsaktien-Konstrukte).
     *  7. Municipals (Kommunalanleihen): US-amerikanische Anleihen von 
     *      Staedten, Gemeinden und Bundesstaaten (oft steuerbegünstigt).
     *  8. Commodity (Rohstoffe): Terminkontrakte (Futures) und Optionen auf 
     *      Rohstoffe wie Gold, Oel, Weizen oder Kaffee.
     *  9. Currency (Devisen / Forex): Wechselkurse, Devisen-Kassageschaefte 
     *      (Spot) und FX-Forwards.
     *  10. Index (Indizes): Statistische Kennzahlen, die Maerkte abbilden 
     *      (z. B. S&P 500, DAX, MSCI World). Sie selbst sind nicht direkt 
     *      handelbar.
     *  11. Client (Spezialkategorie): Proprietaere oder kundenspezifische 
     *      Datenstrukturen, die innerhalb von geschlossenen Systemen genutzt 
     *      werden.
     * @var string
     */
    protected string $market_sector = '';

    /**
     * Das Feld name enthaelt den offiziellen, rechtlichen Firmennamen 
     *  (Legal Name) des Emittenten oder die formelle Bezeichnung des 
     *  Finanzinstruments.
     *  Es drei wichtige Besonderheiten, die man fuer die Praxis kennen muss:
     *  1. Verwendung von Standard-Abkuerzungen (Truncation):
     *      Corporation             => CORP
     *      Public Limited Company  => PLC
     *      Aktiengesellschaft      => AG
     *      Incorporated            => INC
     *      Société Anonyme         => SA
     *  2. Großschreibung (All Caps)
     *      In den Datenstroemen von Bloomberg und OpenFIGI wird der Name 
     *      (name) historisch bedingt fast ausnahmslos in reinen 
     *      Großbuchstaben ausgegeben.
     *  3. Eindeutigkeit bei verschiedenen Assetklassen
     *      Der name hilft, Instrumente zu unterscheiden, die denselben Ticker 
     *      teilen. Wenn beispielsweise nach dem Kuerzel BMW suchst wird, 
     *      erkennt man anhand des Namens sofort, um was es sich handelt:
     *      - Stammaktie: BAYERISCHE MOTOREN WERKE AG
     *      - Eine Anleihe von BMW: BMW FINANCE NV (Tochtergesellschaft, die 
     *          als Emittent auftritt).
     * @var string
     */
    protected string $name = '';

    /**
     * OpenFIGI kann auch den standardisierten Market Identifier Code (MIC) 
     *  nach ISO 10383 (z. B. XFRA fuer die Boerse Frankfurt oder XNYS fuer 
     *  die New York Stock Exchange) verwenden.
     * @var string
     */
    protected string $mic = '';
    

    /**
     * SQL-Query-Fragmente
     */
    private string $sqlQuerySelectFragment;
    
    
    /**
     * Konstruktor
     *
     * @param \PDO $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param array $options Optionale Konfigurationsparameter.
     */
    public function __construct(\PDO $dbh, array $config, array $options = array())
    {
        $this->dbh = $dbh;
        $this->config = $config;
        $this->options = $options;
        $this->resetProperties();
        
        // Initiiere SQL-Query-Fragmente
        $this->sqlQuerySelectFragment = "SELECT * FROM {$this->config['DATABASE']['DATABASENAME']}.{$this->config['DATABASE']['TABLE_PREFIX']}";
    }
    

    /**
     * Ermittelt zu einer ISIN die globalen Identifikatoren fuer 
     *  Finanzinstrumente (Open Financial Instrument Global Identifier - 
     *  OpenFIFI).
     * 
     * @param string $isin ISIN, International Securities Identification Number
     * @param string $code Optional: Schluessel/Wert-Paar; Schluessel ist 
     *  'exchCode' oder 'micCode'. Wert ist ein Bloomberg-eigenes 
     *  Boersen-Kuerzel, der MIC oder NULL.
     * @return array[] Liste von Treffern, jeder Treffer wiederum als Array:
     *  array(8) { // Beispiel fuer die ISIN US5949181045
     *      ["figi"]=>string(12) "BBG000BPH459"
     *      ["ticker"]=>string(4) "MSFT"
     *      ["exchCode"]=>string(2) "US" - "Bloomberg exchange mnemonic" 
     *      ["securityType"]=>string(12) "Common Stock"
     *      ["securityType2"]=>string(12) "Common Stock"
     *      ["marketSector"]=>string(6) "Equity"
     *      ["name"]=>string(14) "MICROSOFT CORP"
     *      ["compositeFIGI"]=>string(12) "BBG000BPH459"
     * }
     * @note *** Hinweise: ***
     *  - Eine ISIN → mehrere Treffer: OpenFIGI liefert pro Handelsplatz einen 
     *      Eintrag (Zum Beispiel notiert Siemens an vielen Handelsplaetzen). 
     */
    public function figiByIsin(string $isin, ?string $code = null): array
    {
        $result = $this->openfigiByIsin($isin, $code);
        if (count($result) === 1) {
            $this->setProperties($result[0]);
            if (null !== $code) {
                $codeKey = substr($code, 0, strpos($code, '='));
                if ('micCode' === $codeKey) {
                    $codeValue = substr($code, strpos($code, '=')+1);
                    $this->setMic($codeValue);
                }
                elseif ('exchCode' === $codeKey) {
                    $codeValue = substr($code, strpos($code, '=')+1);
                    $operatingMic = $this->mapExchCodeToMic($codeValue);
                    $this->setMic($operatingMic);                                        
                }
            }
        }
        return $result; 
    }
    
    
    /*
     * Setzt spezifische Objekteigenschaften.
     * 
     * @param array $hit Feld mit folgenden Eintraegen:
     *  array(8) { // z.B. Microsoft 
     *      ["figi"]=>string(12) "BBG000BPH459"
     *      ["ticker"]=>string(4) "MSFT"
     *      ["exchCode"]=>string(2) "US" - ist der Bloomberg-Composite-Code
     *      ["securityType"]=>string(12) "Common Stock"
     *      ["securityType2"]=>string(12) "Common Stock"
     *      ["marketSector"]=>string(6) "Equity"
     *      ["name"]=>string(14) "MICROSOFT CORP"
     *      ["compositeFIGI"]=>string(12) "BBG000BPH459"
     *  }
     * @return DriverOpenfigiModel
     */
    public function setProperties(array $hit): DriverOpenfigiModel
    {
        $keys = array_keys($hit);
        foreach ($keys as $key) {
            $methodName = 'set' . ucfirst($key);
            $this->$methodName($hit[$key]);
        }
        return $this;
    }
    
    
    /**
     * 'protected' Objekteigenschaften werden auf ihre voreingestellten Werte 
     * zurueckgesetzt.
     * 
     * @param string $visibilityModifier Optional: Sichtbarkeitsmodifikator 
     *  kann folgende Werte annehmen: 'public', 'protected', 'private'. 
     *  Voreingestellter Wert ist 'protected'.
     * @return DriverOpenfigiModel
     */
    public function resetProperties($visibilityModifier = 'protected'): DriverOpenfigiModel
    {
        $refClass = new \ReflectionClass(\HaHeLa\DriverOpenfigiModel::class);
        $defProps = $refClass->getDefaultProperties();
        foreach ($defProps as $propName => $propValue) {
            $refProp = new \ReflectionProperty(\HaHeLa\DriverOpenfigiModel::class, $propName);
            $refPropMethod = 'is' . ucfirst($visibilityModifier);
            if ($refProp->$refPropMethod()) {
                $setterMethodName = 'set' . $this->snakeToCamelCase($propName);
                $this->$setterMethodName($propValue);
            }
        }
        return $this;
    }
    
    
    /**
     * Zeichenkette in der snake-Schreibweise in die camel-case-Schreibweise 
     *  umwandeln.
     * 
     * @param string $snake Zeichenkette in der snake-Schreibweise 
     *  z.B. 'eins_zwei_drei'
     * @return string Zeichenkette in der camel-case-Schreibweise
     *  z.B. 'EinsZweiDrei'
     */
    public function snakeToCamelCase(string $snake): string 
    {
        return str_replace('_', '', ucwords($snake, '_'));
    }
    

    /** 
     * Zeichenkette in der camel-case-Schreibweise in die snake-Schreibweise 
     *  umwandeln.
     * 
     * @param string $camelCase Zeichenkette in der camel-case-Schreibweise
     *  z.B. 'EinsZweiDrei', 'EinsZwei2'
     * @return string Zeichenkette in der snake-Schreibweise 
     *  z.B. 'eins_zwei_drei', 'eins_zwei_2'
     */    
    public function camelCaseToSnake(string $camelCase): string 
    {
        return strtolower(preg_replace(
            ['/([a-z0-9])([A-Z])/', '/([a-zA-Z])(\d)/'],
            ['$1_$2', '$1_$2'],
            $camelCase
        ));
    }
    
    
    /************************************** private und geschuetzte Methoden */
    
    
    /**
     * Loest eine ISIN ueber die OpenFIGI-API (Bloomberg Open Symbology) auf.
     *
     * @param string $isin ISIN, z. B. 'DE0007236101'
     * @param ?string $code Optional: Zeichenkette fuer die Festlegung des 
     *  Handelsplatzes. Der Wert des Parameters ist eine Schluessel/Wertpaar-
     *  Zeichenkette, in der Form "key=value". Der Schluessel (key) benennt 
     *  die Kodierung des Handelsplatzes. Zwei Benennungen sind moeglich: 
     *  'exchCode' oder 'micCode'. Erste Benennung kennzeichnet den
     *  Bloomberg-Composite-Code. Dies ist ein Bloomberg-eigener 
     *  Boersen-Kuerzel (2 Zeichen). Zweite Benennung kennzeichnet den MIC. 
     *  Dies ist ein standardisierter Market Identifier Code (MIC) nach ISO 
     *  10383. Der Wert (value) benennt den Handelsplatz, entweder nach 
     *  Bloomberg-Composite- oder nach Market Identifier-Code. Beispiele:
     *  $code = 'exchCode='UN'; Bloomberg-composite-code fuer die NYSE
     *  $code = 'micCode='XNYS'; MIC nach ISO 10383 fuer die NYSE.
     *  Wenn der Parameter nicht angegeben wird, dann ist sein Wert null und 
     *  alle Handelsplaetze zur ISIN werden ermittelt.
     * @param string $apiKey Optionaler OpenFIGI-API-Key (hoeheres Rate-Limit)
     * @return array[] Liste von Treffern, jeder mit: figi, ticker, exchCode, 
     *  securityType, securityType2, marketSector, name
     * @throws RuntimeException bei Netzwerk-/API-Fehlern
     * @note
     * - Rate-Limit: ohne Key 25 Jobs pro Tag, mit (ebenfalls kostenlosem) 
     *  Key deutlich mehr (Doku). Fuer einen einmaligen Abgleich Ihrer 
     *  ca 300-400 Wertpapiere reicht der Key-freie Zugang knapp nicht;
     *  holen Sie sich dann den kostenlosen Key.
     * - Datennutzung: FIGI-Codes und die Mapping-Ergebnisse sind 
     *  MIT-lizenziert und duerfen frei gespeichert werden – anders als bei 
     *  lizenzpflichtigen Kursdaten unproblematisch.
     */
    private function openfigiByIsin(string $isin, ?string $code = null, string $apiKey = ''): array
    {
        $isin = strtoupper(trim($isin));
        if (!preg_match('/^[A-Z]{2}[A-Z0-9]{9}\d$/', $isin)) {
            throw new \InvalidArgumentException("Ungültige ISIN: $isin");
        }
        
        $job = ['idType' => 'ID_ISIN', 'idValue' => $isin];

        if ($code !== null) {
            $codeKey = substr($code, 0, strpos($code, '='));
            $codeValue = substr($code, strpos($code, '=')+1);
            $job[$codeKey] = strtoupper($codeValue);
        }

        $ch = curl_init('https://api.openfigi.com/v3/mapping');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode([$job]),
            CURLOPT_HTTPHEADER     => array_merge(
                ['Content-Type: application/json'],
                $apiKey !== '' ? ['X-OPENFIGI-APIKEY: ' . $apiKey] : []
            ),
            CURLOPT_TIMEOUT        => 15,
        ]);
        $body   = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $err    = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            throw new \RuntimeException("cURL-Fehler: $err");
        }
        if ($status === 429) {
            throw new \RuntimeException('OpenFIGI Rate Limit erreicht '
                . '(ohne Key ~25 Jobs/Tag, mit Key mehr - siehe openfigi.com)');
        }
        if ($status !== 200) {
            throw new \RuntimeException("OpenFIGI HTTP $status: " . substr($body, 0, 200));
        }

        $response = json_decode($body, true);
        if (!isset($response[0]['data'])) {
            return [];   // ISIN unbekannt oder nicht gemappt
        }

        $results = [];
        foreach ($response[0]['data'] as $hit) {
            $results[] = [
                'figi'          => $hit['figi']                ?? null,
                'ticker'        => $hit['ticker']              ?? null,
                'exchCode'      => $hit['exchCode']            ?? null,
                'securityType'  => $hit['securityType']        ?? null, // z. B. 'Common Stock'
                'securityType2' => $hit['securityType2']       ?? null,
                'marketSector'  => $hit['marketSector']        ?? null, // z. B. 'Equity'
                'name'          => $hit['name']                ?? null,
                'compositeFIGI' => $hit['compositeFIGI']       ?? null,
            ];
        }
        return $results;
    }
    
        
    /**
     * Bloomberg Exchange Code (2-, 3-Buchstabencode des Handelsplatzes der 
     *  Bloomberg Company) auf den Market Identifier Code (MIC) 
     *  nach ISO 10383 abbilden. Das gelingt zur Zeit nur mit der 
     *  "LookUp"-Tabelle "bloomberg_exchcode_to_mic" 
     *  (Tabellenname ohne Prefix).
     * 
     * @param string $exchCode Bloomberg Exchange Code.
     * @return Operating MIC.
     */
    private function mapExchCodeToMic(string $exchCode): string
    {
        $dbTableName = 'bloomberg_exchcode_to_mic '; //(ohne Prefix)
        
        $query = $this->sqlQuerySelectFragment . $dbTableName;
        $whereClause = ' WHERE equity_exch_code LIKE :exchCode';
        $stmt = $this->dbh->prepare($query . $whereClause);
        $stmt->bindParam(':exchCode', $exchCode, \PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (false === $row) return 'NULL';
        return strval($row['mic']);
    }
}
