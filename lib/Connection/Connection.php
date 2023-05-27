<?php

namespace Lib\Connection;

use Lib\Exception\ConnectionExceptions\DatabaseConnectionException;
use Lib\Exception\ExceptionHandler;
use mysqli;

class Connection
{
    protected $connection;

    public function __construct()
    {
        // error_reporting(E_ERROR);
        // ini_set('display_errors', 0);
        $this->connect();
    }

    protected function connect()
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

    public function close()
    {
        $this->connection->close();
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
