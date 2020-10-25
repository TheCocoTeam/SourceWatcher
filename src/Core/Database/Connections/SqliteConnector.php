<?php

namespace Coco\SourceWatcher\Core\Database\Connections;

/**
 * Class SqliteConnector
 *
 * @package Coco\SourceWatcher\Core\Database\Connections
 *
 * Following the definition from:
 *     https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#pdo-sqlite
 */
class SqliteConnector extends EmbeddedDatabaseConnector
{
    protected string $path;

    protected bool $memory;

    public function __construct ()
    {
        $this->driver = "pdo_sqlite";

        $this->memory = false;
    }

    public function getPath () : string
    {
        return $this->path;
    }

    public function setPath ( string $path ) : void
    {
        $this->path = $path;
    }

    public function isMemory () : bool
    {
        return $this->memory;
    }

    public function setMemory ( bool $memory ) : void
    {
        $this->memory = $memory;
    }

    protected function getConnectionParameters () : array
    {
        $this->connectionParameters = [];

        $this->connectionParameters["driver"] = $this->driver;
        $this->connectionParameters["user"] = $this->user;
        $this->connectionParameters["password"] = $this->password;

        if ( isset( $this->path ) && $this->path !== "" ) {
            $this->connectionParameters["path"] = $this->path;
        }

        $this->connectionParameters["memory"] = $this->memory;

        return $this->connectionParameters;
    }
}
