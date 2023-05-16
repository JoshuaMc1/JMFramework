<?php

namespace Lib\Connection;

use Lib\Http\ErrorHandler;
use mysqli;

class Connection
{
    protected $connection;

    public function __construct()
    {
        $this->connect();
    }

    protected function connect()
    {
        try {
            $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

            if ($this->connection->connect_error) {
                ErrorHandler::renderError(500, 'Internal Server Error', $this->connection->connect_error);
                die();
            }
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
            die();
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
