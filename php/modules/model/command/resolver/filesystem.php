<?php

/**
 * php/modules/model/command/resolver/filesystem.php
 *
 * Command Resolver Filesystem Model - Modell der Kommando-Aufloesung 
 *   fuer ein Dateisystem.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


namespace HaHeLa;
//use HaHeLa\Modules_Model_Http_Request as HttpRequest;


if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}


/**
 * Command Resolver Filesystem Model - Model der Kommando-Aufloesung 
 *   fuer ein Dateisystem.
 */
class CommandResolverFilesystemModel 
    implements CommandResolverFilesystemInterface
{
    /**
     * HTTP-Request-Parametername des Kommandos.
     * @const string COMMAND
     * @default 'cmd'
     */
    private const string COMMAND = 'cmd';

    
    /**
     * Datenbank-Handle
     * @var \PDO $dbh
     */
    private \PDO $dbh;

    /**
     * Konfigurationsdaten des Systems.
     * @var array
     */
    private array $config;

    /**
     * Pfad zu den Kommando-Dateien (ohne abschliessenden Slash oder Backslash).
     * @var string
     */
    private string $path;

    /**
     * Voreingestelltes Kommando.
     * @var string
     */
    private string $defaultCommand;


    /**
     * Constructor
     *
     * @param \PDO $dbh Datenbank-Handle.
     * @param array $config Konfigurationsdaten.
     * @param string $path Pfad zu den Kommando-Dateien.
     * @param string $defaultCommand Der Name eines voreingestellten Kommandos.
     */
    public function __construct ($dbh, $config, $path, $defaultCommand)
    {
        $this->dbh = $dbh;
        $this->config = $config;
        $this->path = $path;
        $this->defaultCommand = $defaultCommand;
    }

    
    /**
     * Holt das Kommando aus dem HTTP Request und laed das Kommando-Objekt.
     *
     * @param HttpRequest $request
     * @return string Kommando
     */
    public function getCommand (HttpRequest $request)
    {
		$cmdName = $request->getParameter(self::COMMAND);
        if ($request->issetParameter(self::COMMAND)) {
            $cmdName = $request->getParameter(self::COMMAND);
            $command = $this->loadCommand($cmdName);
            if ($command instanceof CommandInterface) {
                return $command;
            }
        }
        $command = $this->loadCommand($this->defaultCommand);
        return $command;
    }


    /**
     * Laed und instanziiert die Kommand-Klasse.
     *
     * @param string $cmdName Name der Kommando-Klasse.
     * @return mixed Kommando-Klasse oder FALSE.
     */
    public function loadCommand ($cmdName)
    {
        $filename = $this->path.DS.strtolower($cmdName).'.php';
        if (!file_exists($filename)) {
            return FALSE;
        }
        include_once $filename;
        $class = "HaHeLa\Modules_Model_Rest_{$cmdName}";
        if (!class_exists($class)) {
            return FALSE;
        }
        $command = new $class($this->dbh, $this->config);
        return $command;
    }
}