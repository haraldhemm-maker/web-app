<?php


/**
 * oauth-status.php - Prüft, ob der OAuth2-Authorization-Server erreichbar ist.
 *
 * Wird per fetch() aus dem Frontend aufgerufen und liefert JSON:
 *   {"online": true|false, "http_code": 200}
 *
 * Der Browser selbst kann den Server nicht direkt abfragen
 * (CORS), daher übernimmt dieses serverseitige Skript den Check.
 */


declare(strict_types=1);


header('Content-Type: application/json');
header('Cache-Control: no-store');

/* URL des Authorization Servers - bei Port-Änderung anpassen! */
const OAUTH_SERVER = 'http://localhost:8000/index.php';

$ch = curl_init(OAUTH_SERVER);

curl_setopt_array($ch, [
    // HEAD-Request reicht: wir wollen nur wissen, ob jemand antwortet
    CURLOPT_NOBODY          => true,
    CURLOPT_RETURNTRANSFER  => true,
    // Schnell scheitern, damit das Frontend nicht wartet
    CURLOPT_CONNECTTIMEOUT  => 2,
    CURLOPT_TIMEOUT         => 3,
    // Lokaler Dev-Server: ggf. self-signed Zertifikate akzeptieren
    CURLOPT_SSL_VERIFYPEER  => false,
    CURLOPT_SSL_VERIFYHOST  => 0,
]);

$execOk    = curl_exec($ch) !== false;
$httpCode  = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
curl_close($ch);

echo json_encode([
    // online = Verbindung stand UND ein gültiger HTTP-Status kam zurück
    'online'    => $execOk && $httpCode >= 200 && $httpCode < 500,
    'http_code' => $httpCode,
]);
