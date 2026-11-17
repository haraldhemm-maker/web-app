<?php


/** 
 * GET-Request an die Finnhub-REST-API 
 *
 * @param string $path
 * @param array $params
 * @return array
 * @throws RuntimeException
 */
function fhGet(string $path, array $params): array 
{
    $params['token'] = 'dbms9b9r01qn1o673430dbms9b9r01qn1o67343g';
    $url = 'https://finnhub.io/api/v1' . $path . '?' . http_build_query($params);
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


// list_finnhub_types.php - zeigt alle vorkommenden Security Types einer Börse
$symbols = fhGet('/search', ['q' => 'XETRA']); // Frankfurt 'XETR'


$types = [];
foreach ($symbols as $s) {
    $t = $s['type'] ?? '(leer)';
    $types[$t] = ($types[$t] ?? 0) + 1;
}


arsort($types);
print_r($types);   // z. B. ['Stock' => 1234, 'COB' => 56, 'DR' => 12, ...]