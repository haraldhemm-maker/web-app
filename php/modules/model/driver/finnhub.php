<?php


/**
 * php/modules/model/driver/finnhub.php
 *
 * DriverFinnhubModel - Spezifische Klasse fuer die Nutzung der FinnHub-API
 *  als Datenquelle.
 * 
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


use HaHeLa\DriverOpenfigiModel as DriverOpenfigi;
use HaHeLa\CountryModel as Country;
use HaHeLa\CurrencyModel as Currency;
use HaHeLa\SecurityTypeModel as SecurityType;


if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}


/**
 * Driver_Deutscheboerse_Model - Spezifische Klasse fuer die Website 
 *   "live.deutsche-boerse.com" als Datenquelle.
 */
class DriverFinnhubModel implements DriverGeneralInterface
{
    use AccessorTrait;
    
    
	/**
	 * Konstanten
	 */
	
	// "Versionsnummer"
	const VERSION = '1.0.0';
	// Name, Beschreibung, Domaene und URL.
	const NAME = 'Finnhub Stock API';
	const DESCRIPTION = 'Echtzeit-RESTful-APIs und WebSockets für Aktien, Währungen und Kryptowährungen. Machen Sie Finanzdaten für alle zugänglich – mit der leistungsstärksten Aktien-API auf dem Markt.';
	const DOMAIN = 'finnhub.io/';
	const URL = 'https://finnhub.io';
    
    const API_VERSION_PATH = '/api/v1';
    
    
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
     * Optionen (Optional)
     * @property array
     */
    protected array $options;
    
    //
    // Alle Namen der Eigenschaften eines Wertpapiers beginnen mit $security_
    //  und haben den Sichtbarkeitsmodifikator (Zugriffsmodifikator) 
    //  protected.
    // 
    
    /**
     * Identifikationsnummer eines Wertpapiertyps in der Datenbank.
     *  (Fremdschluessel auf Eintrag in der Tabelle wa_security_type)
     * @property int 
     */
    protected int $security_type_id;
    
    /**
     * Name eines Wertpapiers.
     * @property string
     */
    protected string $security_name;

    /**
     * Internationale Wertpapierkennnummer (International Securities 
     *  Identification Number).
     * @property string
     */
    protected string $security_isin;

    /**
     * Ticker-Symbol eines Wertpapiers.
     * @property string
     */
    protected string $security_ticker;

    /**
     * Alpha-2-Code eines Landes (nur Grossbuchstaben).
     * @property string
     */
    protected string $security_country;
    
    /**
     * Waehrungsabkuerzung
     * @property string
     */
    protected string $security_currency;
    
    /**
     * Benennung der Branche (und des Sektors) zu der das Wertpapier #
     *  zugeordnet wird.
     * @property string
     */
    protected $security_industry; // Brance und Sektor.
    
    /**
     * Finnhub-Typ eines Wertpapiers.
     * @property string
     */
    protected string $finnhub_type;
    
    
	/**
	 * Konstruktor
	 *
	 * @param resource $dbh Datenbank-Handle.
	 * @param array $config Konfigurationsdaten.
     * @param array $options Opional Optionen.
	 * @return \HaHeLa\DriverFinnhubModel
	 */
	public function __construct(\PDO $dbh, array $config, array $options = array())
	{
		$this->dbh = $dbh;
		$this->config = $config;
        $this->options = $options;
	}
    
    
	/**
	 * Holt die Version.
	 * 
	 * @return string Version
	 */
	public function getVersion(): string
	{
		return self::VERSION;
	}
    
    
	/**
	 * Holt den Namen der Datenquelle.
	 * 
	 * @return string Name der Datenquelle.
	 */
	public function getName(): string
	{
		return self::NAME;
	}
    

	/**
	 * Holt die Beschreibung der Datenquelle.
	 * 
	 * @return string Beschreibung der Datenquelle.
	 */
	public function getDescription(): string
	{
		return self::DESCRIPTION;
	}
	

	/**
	 * Holt die Domaene (Domain) der Datenquelle.
	 * 
	 * 	Eine Domain (Internetadresse) ist der weltweit eindeutige, 
	 * 	einprägsame Name einer Website (z. B. live.deutsche-boerse.com), 
	 * 	der anstelle einer komplexen, numerischen IP-Adresse verwendet 
	 * 	wird. Sie ermöglicht den Zugriff auf Webseiten und besteht aus 
	 * 	Top-Level-Domain (.com), Second-Level-Domain (Name, z.B. 
	 * 	deutsche-boerse) und oft einer Subdomain z.B. live). 
	 * 	Aufbau: subdomain.domainname.tld – Beispiel: live.deutsche-boerse.com.
	 * 	Bestandteile:
	 * 		- Top-Level-Domain (TLD): Endung wie .de, .com, .org.
	 * 		- Second-Level-Domain (SLD): Der gewählte Name, 
	 * 			z. B. deutsche-boerse.
	 * 		- Third-Level-Domain / Subdomain: Oft www oder spezifische 
	 * 			z.B. live..
	 * 
	 * @return string Domaene der Datenquelle.
	 */
	public function getDomain(): string
	{
		return self::DOMAIN;
	}
	
	
	/**
	 * Holt den URL der Website.
	 * 
	 * @return string
	 */
	public function getUrl(): string
	{
		return self::URL;
	}
    
    
    /**
     * Finnhub-Typ auf lokalen Systemtyp abbilden. Die Identifikationsnummer 
     *  des Systemtyps wird in der Eigenschaft $security_type_id gespeichert
     *  und der Code des Systemtyps wird zurueckgegeben.
     * 
     * @param string $finnhubType
     * @return string Lokaler Systemtyp-Code.
     * @property protected $security_type_id
     */
    public function mapFinnhubTypeToSystemType(string $finnhubType): string
    {
        $TYPE_MAP = [
            'Mutual Fund'  => 'fund'
        ];
        $systemTypeCode = $finnhubType;
        if (array_key_exists($finnhubType, $TYPE_MAP)) {
            $systemTypeCode = $TYPE_MAP[$finnhubType];
        }
        $systemTypeCode = $this->slugify($systemTypeCode);
        $securityType = new SecurityType($this->dbh, $this->config);
        $securityType->setOptions(array('code' => $systemTypeCode));
        if (false === $typeId = $securityType->exists()) {
            $typeId = $securityType->setOptions(array('code' => 'unknown'))->exists();
        }
        $this->setSecurityTypeId($typeId);
        return $systemTypeCode;
    }
    
    
    /**
     * ISIN aufloesen: 
     *  ISIN -> [0 = ticker, 1 = name, 2 = finnhubType] ueber die Such-API
     * 
     * @param string $isin
     * @return ?array
     */
    public function resolveIsin(string $isin): ?array {
        $res = $this->fhGet('/search', ['q' => $isin]);
        foreach ($res['result'] ?? [] as $hit) {
            if (!empty($hit['symbol'])) {
                $result = [ $hit['symbol'], $hit['description'] ?? '', $hit['type'] ?? ''];
                $this->setSecurityTicker($result[0]);
                $this->setSecurityName($result[1]);
                $this->setFinnhubType($result[2]);
                return $result;
            }
        }
        return null;
    }

    
    /** 
     * Profildaten eines Wertpapiers zum Ticker ermitteln und laden. 
     *  Das Wertpapier wird mit der Angabe des Tickersymbols ausgewaehlt.
     *  Wenn Profildaten zum Tickersymbol gefunden wurden, dann liefert die 
     *  Methode als Ergebnis ein assoziatives Array mit den Profildaten. Von
     *  diesen Profildaten werden 'name', 'country', 'currency' und 
     *  'finnhubIndustriy' in die entsprechenden Objekt-Eigenschaften 
     *  gespeichert. Im Fall, dass keine Profildaten gefunden wurden, dann 
     *  liefert die Methode ein leeres Array.
     * 
     * @param string $ticker
     * @return  array(14) { // assoziatives Array z.B. Microsoft
     *  ["ticker"]=>string(4) "MSFT"
     *  ["name"]=>string(21) "Microsoft Corporation"
     *  ["country"]=>string(2) "US"
     *  ["currency"]=>string(3) "USD"
     *  ["estimateCurrency"]=>string(3) "USD"
     *  ["exchange"]=>string(26) "NASDAQ NMS - GLOBAL MARKET"
     *  ["ipo"]=>string(10) "1986-03-13"
     *  ["marketCapitalization"]=>float(3940412.890048273)
     *  ["logo"]=>string(77) "https://static2.finnhub.io/file/publicdatany/finnhubimage/stock_logo/MSFT.png"
     *  ["shareOutstanding"]=>float(7426.19)
     *  ["finnhubIndustry"]=>string(10) "Technology"
     *  ["phone"]=>string(11) "14258828080"
     *  ["weburl"]=>string(32) "https://www.microsoft.com/en-in/"
     *  ["floatingShare"]=>float(7419.07)
     * }
     */
    function enrichProfile(string $ticker): array 
    {
        $p = $this->fhGet('/stock/profile2', ['symbol' => $ticker]);
        if (!empty($p)) {
            $this->setSecurityTicker($p['ticker']);
            $this->setSecurityName($p['name']);
            $this->setSecurityCountry($p['country']);
            $this->setSecurityCurrency($p['currency']);
            $this->setSecurityIndustry($p['finnhubIndustry']);
        }
        return $p ?: [];
    }
    
    
    /**
     * Holt, mit der Angabe der ISIN, die Wertpapierdaten.
     * 
     * @param string $isin Internationale Wertpapierkennnummer.
     * @return Assoziatives Array (Map) mit den folgenden Wertpapierdaten:
     *  array(
     *      int:    type_id,        // Fremdschluessel (FK) auf wa_security_type
     *      string: name,           // Name des Wertpapiers
     *      string: isin,           // Internationale Wertpapierkennnummer
     *      string: ticker,         // Symbol des Wertpapers
     *      string: country,        // Alpha-2-Code des Landes
     *      string: currency,       // Waehrungsabkuerzung
     *      string: industry,       // Branche und Sektor
     */
    public function getSecurityDataByIsin(string $isin): array
    {
        $scurityData = array();
        //
        // isin
        //
        $securityData['isin'] = $isin;
        //
        // ticker, name, type_id
        //
        $data = $this->resolveIsin($isin);
        $securityData['ticker'] = $this->getSecurityTicker();
        $securityData['name'] = $this->getSecurityName();
        $systemTypeCode = $this->mapFinnhubTypeToSystemType(
            $this->getFinnhubType()
        );
        $securityData['type_id'] = $this->getSecurityTypeId();
        //
        // country, currency, industry(sector)
        //
        $data = $this->enrichProfile($securityData['ticker']);
        $securityData['country'] = $this->getSecurityCountry();
        $securityData['currency'] = $this->getSecurityCurrency();
        $securityData['industry'] = $this->getSecurityIndustry();
        return $securityData;
    }
    
	/* ******************************************* private (Hilfs-)Methoden */
    
      
    /** 
     * GET-Request an die Finnhub-REST-API 
     *
     * @param string $path
     * @param array $params
     * @return array
     * @throws RuntimeException
     */
    public function fhGet(string $path, array $params): array 
    {
        $params['token'] = $this->config['FINNHUB']['API_KEY'];
        $url = $this->getUrl() . self::API_VERSION_PATH . $path . '?' . http_build_query($params);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $body   = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $err    = curl_error($ch);
        curl_close($ch);
        if ($body === false) {
            throw new \RuntimeException("cURL-Fehler: $err");
        }
        if ($status === 429) {
            throw new \RuntimeException('Rate Limit erreicht (max. 60 Calls/Minute im Free Tier)');
        }
        if ($status !== 200) {
            throw new RuntimeException("Finnhub HTTP $status");
        }
        return json_decode($body, true) ?? [];
    }
    
    
    /**
     * Slugify („Slugifizieren“) ist die programmatische Umwandlung eines 
     *  beliebigen Textes in eine saubere, URL-sichere Zeichenkette.
     *  Datei werden all Grossbuchstaben in entsprechende Kleinbuchstaben 
     *  umgewandelt. Beliebig grosse Wortzwischenraeume werden in einen 
     *  Unterstrich '_' umgewandelt.
     *  Z.B. "Eins zwei  Drei   Vier" => "eins_zwei_drei_vier"
     * 
     * @param string $text
     * @return "slugifizierter Text.
     */
    private function slugify(string $text): string {
        // Mehrfache (und einfache) Leerzeichen durch einen Unterstrich 
        //  ersetzen.
        $text = preg_replace('/\s+/', '_', trim($text));
        // In Kleinbuchstaben umwandeln (multibyte-sicher)
        return mb_strtolower($text, 'UTF-8');
    }
}
