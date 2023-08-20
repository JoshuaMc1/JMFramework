<?php

namespace Lib\Connection;

use Lib\Exception\ConnectionExceptions\DatabaseConnectionException;
use Lib\Exception\ExceptionHandler;
use mysqli;

/**
 * Class Connection
 *
 * Represents a database connection using MySQLi.
 */
class Connection
{
    /** @var mysqli|null The MySQLi database connection. */
    protected $connection;

    /**
     * Connection constructor. Initiates the database connection.
     */
    public function __construct()
    {
        $this->connect();
    }

    /**
     * Establishes the database connection.
     *
     * @throws DatabaseConnectionException If a database connection error occurs.
     */
    protected function connect(): void
    {
        try {
            $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

            if ($this->connection->connect_error) {
                throw new DatabaseConnectionException($this->connection->connect_errno, 'Database Connection Error', $this->connection->connect_error);
            }
        } catch (DatabaseConnectionException $exception) {
            ExceptionHandler::handleException($exception);
        } catch (\Throwable $th) {
            throw new DatabaseConnectionException($th->getCode(), 'Internal Server Error', $th->getMessage());
        }
    }

    /**
     * Closes the database connection.
     */
    public function close(): void
    {
        $this->connection->close();
    }

    /**
     * Gets the established database connection.
     *
     * @return mysqli|null The MySQLi database connection.
     */
    public function getConnection(): ?mysqli
    {
        return $this->connection;
    }
}
