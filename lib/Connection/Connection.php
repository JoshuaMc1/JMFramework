<?php

namespace Lib\Connection;

use Lib\Exception\ConnectionExceptions\DatabaseConnectionException;
use PDO;

class Connection
{
    protected $connection;
    protected $config;

    /**
     * Connection constructor. Initiates the database connection.
     *
     * @return void
     */
    public function __construct()
    {
        $driver = config('database.default');
        $this->config = config('database.connections.' . $driver);
        $this->connect();
    }

    /**
     * Establishes the database connection.
     *
     * @throws DatabaseConnectionException If a database connection error occurs.
     *
     * @return void
     */
    protected function connect()
    {
        try {
            $dsn = "{$this->config['driver']}:host={$this->config['host']};dbname={$this->config['database']};charset={$this->config['charset']};port={$this->config['port']}";

            $this->connection = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (\PDOException $exception) {
            throw new DatabaseConnectionException($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * Closes the database connection.
     */
    public function close(): void
    {
        $this->connection = null;
    }

    /**
     * Gets the established database connection.
     *
     * @return PDO|null The PDO database connection.
     */
    public function getConnection(): ?PDO
    {
        return $this->connection;
    }
}
