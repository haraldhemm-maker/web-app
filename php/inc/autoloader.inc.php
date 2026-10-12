<?php


/**
 * Autoloader fuer Klassen (…Model) und Interfaces (…Interface) der Web-App.
 * 
 * .../php/inc/autoloader.php
 * 
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 2.0.0
 * @since 1.0.0
 *
 * Pfadschema:
 *   FooBarModel      -> <base>/php/modules/model/foo/bar.php
 *   FooBarInterface  -> <base>/php/modules/interface/foo/bar.php
 */


namespace HaHeLa;


spl_autoload_register(function (string $class): void {
    // Basisverzeichnis der Anwendung anpassen
    $basePath = dirname(__DIR__) . '/modules';
    
    // Namensraum-Praefix (HaHeLa) entfernen 
    if (str_contains($class, '\\')) {
        $class = substr($class, strrpos($class, '\\') + 1);
    }

    if (str_ends_with($class, 'Model')) {
        $name = substr($class, 0, -strlen('Model'));
        $subdir = 'model';
    } elseif (str_ends_with($class, 'Interface')) {
        $name = substr($class, 0, -strlen('Interface'));
        $subdir = 'interface';
    } else {
        return; // gehört nicht zu diesem Schema
    }

    // CamelCase in Segmente zerlegen: SecurityType -> ['Security', 'Type']
    $segments = preg_split('/(?=[A-Z])/', $name, -1, PREG_SPLIT_NO_EMPTY);
    if ($segments === false) {
        return;
    }

    // Letztes Segment = Dateiname, Rest = Verzeichnisse
    $file = array_pop($segments);

    // alles in Kleinbuchstaben, Sonderzeichen bleiben erhalten
    $parts = array_map('strtolower', $segments);
    $parts[] = strtolower($file) . '.php';

    $path = $basePath . '/' . $subdir . '/' . implode('/', $parts);

    if (is_file($path)) {
        require $path;
    }
});

