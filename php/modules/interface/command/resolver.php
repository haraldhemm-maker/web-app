<?php


/**
 * php/modules/interface/command/resolver.php
 *
 * Command Resolver Interface - Interface der Kommando-Aufloesung.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;


use HaHeLa\HttpRequestModel as HttpRequest;


/**
 * Command Resolver Interface - Interface der Kommando-Aufloesung.
 */
interface CommandResolverInterface
{
    /**
     * Constructor
     *
     * @param resource $dbh Datenbank-Handle.
     * @param array $configuration Konfigurationsdaten.
     * @param string $path Pfad zu den Kommando-Dateien.
     * @param string $defaultCommand Der Name eines voreingestellten Kommandos.
     * @return void
     */
    public function __construct ($dbh, $config, $path, $defaultCommand);
    
    
    /**
     * Holt das Kommando aus dem HTTP Request und laed das Kommando-Objekt.
     *
     * @param HttpRequest $request
     * @return string Kommando
     */
    public function getCommand (HttpRequest $request);
    
    
    /**
     * Laed und instanziiert die Kommand-Klasse.
     *
     * @param string $cmdName Name der Kommando-Klasse.
     * @return mixed Kommando-Klasse oder false.
     */
    public function loadCommand ($cmdName);
}