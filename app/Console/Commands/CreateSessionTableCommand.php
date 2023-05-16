<?php

namespace App\Console\Commands;

require_once __DIR__ . '/../../../config/env.php';

use Illuminate\Console\Command;
use Lib\Connection\Connection;

class CreateSessionTableCommand extends Command
{
    protected $signature = 'session:table';

    protected $description = 'Create user table and session table in database';

    protected $connection;

    public function handle()
    {
        $this->createUserTable();
        $this->createSessionTable();
    }

    public function createUserTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT NOT NULL AUTO_INCREMENT,
            name VARCHAR(255),
            email VARCHAR(255),
            password VARCHAR(255),
            PRIMARY KEY (id)
        );";

        $this->connection = (new Connection())->getConnection();

        if ($this->connection->query($sql) === TRUE) {
            $this->info('User table created successfully');
        } else {
            $this->error('Error creating user table: ' . $this->connection->error);
        }
    }

    public function createSessionTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS sessions (
        id INT NOT NULL AUTO_INCREMENT,
        user_id INT,
        ip_address VARCHAR(45),
        user_agent VARCHAR(255),
        last_activity INT,
        PRIMARY KEY (id),
        CONSTRAINT fk_sessions_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );";

        $this->connection = (new Connection())->getConnection();

        if ($this->connection->query($sql) === TRUE) {
            $this->info('Session table created successfully');
        } else {
            $this->error('Error creating session table: ' . $this->connection->error);
        }
    }
}
