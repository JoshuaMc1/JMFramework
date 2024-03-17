<?php

namespace Lib\Connection;

use Lib\Exception\ConnectionExceptions\DatabaseConnectionException;
use Illuminate\Database\Capsule\Manager as Capsule;

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
            $capsule = new Capsule;

            $capsule->addConnection($this->config);
            $capsule->setAsGlobal();
            $capsule->bootEloquent();

            $this->connection = $capsule->getConnection();
        } catch (\PDOException $exception) {
            throw new DatabaseConnectionException($exception->getCode() !== 0 ? $exception->getCode() : 0101, $exception->getMessage());
        }
    }

    /**
     * Get the database connection.
     *
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
