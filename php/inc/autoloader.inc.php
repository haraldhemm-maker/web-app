<?php


/**
 * .../php/inc/autoloader.php
 * 
 * Autoloader fuer die Web-App
 * 
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 *
 * Einbindung in den Skripten:
 *   include_once '/home/harald/public_html/app/web-app/php/inc/autoloader.inc.php';
 *
 * Namenskonvention:
 *  Postfix "Model"         -> Klassen in .../php/modules/model/
 *  Postfix "Interface"    -> Interfaces in .../php/modules/interface/
 *
 * Beispiel:
 *   namespace HaHeLa;
 *   $config = new ConfigurationModel();
 *   -> lädt .../php/modules/model/configuration.php und 
 *      .../php/modules/interface/configuration.php
 */


namespace HaHeLa;


class Autoloader
{
    /** Basisverzeichnis der Module */
    const MODULES_DIR = '/home/harald/public_html/app/web-app/php/modules';
    
    
    /**
     * Registriert den Autoloader.
     * 
     * @return void
     */
    public static function register(): void
    {
        spl_autoload_register([__CLASS__, 'load']);
    }
    
    
    /**
     * Laedt eine Klasse oder ein Interface anhand des Namenspostfix
     *
     * @param string $className Vollqualifizierter Name, z. B. 
     *  "HaHeLa\ConfigurationModel"
     * @return void
     */
    public static function load(string $className): void
    {
        // Namensraum-Praefix (HaHeLa) entfernen 
        //  (wir arbeiten mit dem kurzen Namen)
        $shortName = $className;
        if (str_contains($className, '\\')) {
            $shortName = substr($className, strrpos($className, '\\') + 1);
        }
        
        // init fully qualified file name 
        $fqFileName = '';
        
        // Postfix wie 'Model' bzw 'Interface' entfernen
        if (str_ends_with($shortName, 'Model')) {
            $shortName = strtolower(substr($shortName, 0, -5));
            $fqFileName = self::MODULES_DIR . DS . 'model'. DS . $shortName . '.php';
        } 
        elseif (str_ends_with($shortName, 'Interface')) {
            $shortName = strtolower(substr($shortName, 0, -9));
            $fqFileName = self::MODULES_DIR . DS . 'interface'. DS . $shortName . '.php';
        }
        
        if (!empty($fqFileName) && is_file($fqFileName)) {
            include $fqFileName;
        }
    }
    
        
    /* ******************************************** private Hilfsfunktionen */
    
    
    /**
     * Prefix Model_ oder Interface_ aus dem Namen entfernen
     * 
     * @param string $name Model- oder Interfacename mit dem entsprechenden 
     *  Prefix Model_ bzw. Interface_.
     * @return string Name ohne Prefix.
     */
    private static function remove_prefix (string $name): string
    {
        $name = strstr($name, '_');
        $name = ltrim($name, '_'); // Entfernt den fuehrenden Unterstrich
        $name = strtolower($name);
        return $name;
    }
}


Autoloader::register();
