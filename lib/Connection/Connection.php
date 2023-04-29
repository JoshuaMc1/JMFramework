<?php

namespace Lib\Connection;

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
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

        if ($this->connection->connect_error) {
            die('Connection error: ' . $this->connection->connect_error);
        }
    }

    public function close()
    {
        $this->connection->close();
    }

    public function getConnection()
    {
        return self::$connection;
    }
}
