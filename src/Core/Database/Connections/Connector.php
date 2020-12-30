<?php

namespace Coco\SourceWatcher\Core\Database\Connections;

use Coco\SourceWatcher\Core\Row;
use Coco\SourceWatcher\Core\SourceWatcherException;
use Coco\SourceWatcher\Utils\FileUtils;
use Coco\SourceWatcher\Utils\Internationalization;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class Connector
 *
 * @package Coco\SourceWatcher\Core\Database\Connections
 */
abstract class Connector
{
    private Logger $logger;

    public function __construct ()
    {
        $this->logger = new Logger( "Connector" );

        $streamPath = FileUtils::file_build_path( __DIR__, "..", "..", "..", "..", "logs",
            "Connector" . "-" . gmdate( "Y-m-d-H-i-s", time() ) . "-" . getmypid() . ".txt" );

        $this->logger->pushHandler( new StreamHandler( $streamPath ), Logger::DEBUG );
    }

    protected string $driver = "";

    protected array $connectionParameters = [];

    protected string $user = "";

    protected string $password = "";

    protected string $tableName = "";

    public function getDriver () : string
    {
        return $this->driver;
    }

    protected abstract function getConnectionParameters () : array;

    public function getUser () : string
    {
        return $this->user;
    }

    public function setUser ( string $user ) : void
    {
        $this->user = $user;
    }

    public function getPassword () : string
    {
        return $this->password;
    }

    public function setPassword ( string $password ) : void
    {
        $this->password = $password;
    }

    public function getTableName () : string
    {
        return $this->tableName;
    }

    public function setTableName ( string $tableName ) : void
    {
        $this->tableName = $tableName;
    }

    /**
     * @return Connection
     * @throws Exception
     */
    public function getConnection () : Connection
    {
        return DriverManager::getConnection( $this->getConnectionParameters() );
    }

    /**
     * @param Row $row
     * @return int
     * @throws SourceWatcherException
     */
    public function insert ( Row $row ) : int
    {
        if ( $this->tableName == null || $this->tableName == "" ) {
            throw new SourceWatcherException( Internationalization::getInstance()->getText( Connector::class,
                "No_Table_Name_Found" ) );
        }

        try {
            $connection = $this->getConnection();

            if ( !$connection->isConnected() ) {
                $connection->connect();
            }
        } catch ( Exception $exception ) {
            throw new SourceWatcherException( Internationalization::getInstance()->getText( Connector::class,
                "Connection_Object_Not_Connected_Cannot_Insert" ), 0, $exception );
        }

        try {
            $numberOfAffectedRows = $connection->insert( $this->tableName, $row->getAttributes() );

            $connection->close();
        } catch ( Exception $exception ) {
            $this->logger->debug( $exception->getMessage() );

            throw new SourceWatcherException( Internationalization::getInstance()->getText( Connector::class,
                "Unexpected_Error" ), 0, $exception );
        }

        return $numberOfAffectedRows;
    }

    /**
     * @param string $query
     * @return array
     * @throws SourceWatcherException
     */
    public function executePlainQuery ( string $query ) : array
    {
        try {
            $connection = $this->getConnection();

            if ( !$connection->isConnected() ) {
                $connection->connect();
            }

            $statement = $connection->executeQuery( $query );

            return $statement->fetchAllAssociative();
        } catch ( \Doctrine\DBAL\Driver\Exception $e ) {
            throw new SourceWatcherException( "Something went wrong: " . $e->getMessage(), 0, $e );
        } catch ( Exception $e ) {
            throw new SourceWatcherException( "Something unexpected went wrong: " . $e->getMessage(), 0, $e );
        }
    }
}
