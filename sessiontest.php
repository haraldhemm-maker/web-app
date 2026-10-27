<?php
declare(strict_types=1);
session_start();
if (isset($_SESSION['hits'])) {
    $_SESSION['hits']++;
} else {
    $_SESSION['hits'] = 1;
}
echo 'Hits: ' . $_SESSION['hits'] . ' | save_path: ' . ini_get('session.save_path')
   . ' | Cookie: ' . session_name() . '=' . session_id();
