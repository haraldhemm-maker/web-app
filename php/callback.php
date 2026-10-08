<?php


/**
 * callback.php - Client-seitiger Redirect-Endpoint
 *
 * GET  ?code=...&state=...  : prüft state (via eingebettetem JS gegen den
 *                             sessionStorage) und reicht code_verifier an
 *                             dieses Script (POST) weiter.
 * POST code + code_verifier : tauscht den Code per curl am Token-Endpoint
 *                             gegen Access-/Refresh-Token ein und startet
 *                             die PHP-Session des Clients.
 */


declare(strict_types=1);


/* Konfiguration des Clients ------------------------------------------- */
const AUTH_TOKEN_ENDPOINT = 'http://localhost:8000/index.php?action=token';
const CLIENT_ID           = 'demo-app';
const CLIENT_REDIRECT_URI = 'http://localhost/~harald/app/web-app/php/callback.php';


session_start();


/* Fall A: Redirect vom Authorization Server --------------------------- */
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET') {
    $code  = $_GET['code']  ?? '';
    $state = $_GET['state'] ?? '';

    if ($code === '' || $state === '') {
        http_response_code(400);
        exit('Ungültiger Callback: code oder state fehlt.');
    }
    ?>
<!doctype html>
<html lang="de">
<head><meta charset="utf-8"><title>Anmeldung läuft …</title></head>
<body>
<p>Authorization Code empfangen, Token wird ausgetauscht …</p>
<script type="module">
  // state prüfen: URL-Parameter gegen sessionStorage
  const params = new URLSearchParams(window.location.search);
  const expectedState = sessionStorage.getItem('oauth_state');
  const verifier      = sessionStorage.getItem('oauth_code_verifier');

  if (!expectedState || !verifier || params.get('state') !== expectedState) {
    document.body.textContent = 'Sicherheitsfehler: state stimmt nicht überein (CSRF?).';
  } else {
    sessionStorage.removeItem('oauth_state');           // einmalig verwenden
    sessionStorage.removeItem('oauth_code_verifier'); // einmalig verwenden

    // Verifier + Code an das eigene PHP-Backend übergeben (nicht in die URL!)
    const res = await fetch(window.location.pathname, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        code: params.get('code'),
        code_verifier: verifier
      })
    });
    if (res.redirected) {
      window.location.assign(res.url);   // Erfolg -> Zielseite
    } else {
      document.body.textContent = await res.text();
    }
  }
</script>
</body>
</html>
    <?php
    exit;
}

/* Fall B: Token-Austausch (vom eingebetteten JS aufgerufen) ------------ */
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $code    = $_POST['code']         ?? '';
    $verifier = $_POST['code_verifier'] ?? '';

    if ($code === '' || $verifier === '') {
        http_response_code(400);
        exit('code oder code_verifier fehlt.');
    }

    $ch = curl_init(AUTH_TOKEN_ENDPOINT);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query([
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => CLIENT_REDIRECT_URI,
            'client_id'     => CLIENT_ID,
            'code_verifier' => $verifier,
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER      => ['Accept: application/json'],
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    if ($response === false) {
        http_response_code(502);
        exit('Token-Endpoint nicht erreichbar.');
    }

    $tokens = json_decode($response, true);

    if ($httpCode !== 200 || !isset($tokens['access_token'])) {
        http_response_code($httpCode ?: 400);
        exit('Token-Austausch fehlgeschlagen: ' . htmlspecialchars($response));
    }

    // Tokens in der Client-Session ablegen (Server-seitig, HttpOnly-Cookie)
    $_SESSION['access_token']  = $tokens['access_token'];
    $_SESSION['refresh_token'] = $tokens['refresh_token'] ?? null;
    $_SESSION['token_expires'] = time() + (int)($tokens['expires_in'] ?? 3600);

    // Weiterleitung auf die geschützte Startseite der Web-App
    header('Location: ../frontend.html?login=success');
    exit;
}

http_response_code(405);
exit('Nur GET oder POST erlaubt.');
