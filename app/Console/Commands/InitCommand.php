<?php

namespace App\Console\Commands;

require_once __DIR__ . '/../../../config/env.php';

use Illuminate\Console\Command;
use Lib\Connection\Connection;

class InitCommand extends Command
{
    protected $signature = 'init';

    protected $description = 'This command is used to create the necessary framework tables in the database.';

    protected $connection;

    public function handle()
    {
        $this->createTable('permissions', [
            'id INT PRIMARY KEY AUTO_INCREMENT',
            'name VARCHAR(255) NOT NULL',
        ], 'Permissions');

        $this->createTable('roles', [
            'id INT PRIMARY KEY AUTO_INCREMENT',
            'name VARCHAR(255) NOT NULL',
        ], 'Roles');

        $this->createTable('users', [
            'id INT NOT NULL AUTO_INCREMENT',
            'name VARCHAR(255)',
            'email VARCHAR(255)',
            'password VARCHAR(255)',
            'PRIMARY KEY (id)',
        ], 'User');

        $this->createTable('sessions', [
            'id INT NOT NULL AUTO_INCREMENT',
            'user_id INT',
            'ip_address VARCHAR(45)',
            'user_agent VARCHAR(255)',
            'last_activity INT',
            'PRIMARY KEY (id)',
            'CONSTRAINT fk_sessions_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE',
        ], 'Session');

        $this->createTable('user_roles', [
            'user_id INT',
            'role_id INT',
            'FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE',
            'FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE',
            'PRIMARY KEY (user_id, role_id)',
        ], 'User roles');

        $this->createTable('role_permissions', [
            'role_id INT',
            'permission_id INT',
            'FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE',
            'FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE',
            'PRIMARY KEY (role_id, permission_id)',
        ], 'Role permissions');

        $this->createTable('personal_access_tokens', [
            'id INT PRIMARY KEY AUTO_INCREMENT',
            'name VARCHAR(255) NULL',
            'token TEXT NOT NULL',
            'last_used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ], 'Personal access tokens');
    }

    public function createTable($tableName, $columns, $tableLabel)
    {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . $tableName . ' (';
        $sql .= implode(',', $columns);
        $sql .= ');';

        $this->connection = (new Connection())->getConnection();

        if ($this->connection->query($sql) === TRUE) {
            $this->info($tableLabel . ' table created successfully');
        } else {
            $this->error('Error creating ' . $tableLabel . ' table: ' . $this->connection->error);
        }
    }
}
