<?php

namespace Lib\Database;

use Lib\Connection\Connection;
use Lib\Database\Contracts\SchemaForgeInterface;
use Lib\Support\Log;

/**
 * Class SchemaForge
 * 
 * Provides functionality for creating and dropping tables in the database.
 *
 * Commands:
 * - schema:run
 * - create:schema
 * - init
 */
class SchemaForge implements SchemaForgeInterface
{
    /**
     * Connection to the database.
     * 
     * @var \PDO
     */
    protected static $connection = null;

    /**
     * Instance of the SchemaForge.
     * 
     * @var SchemaForge
     */
    protected static $instance = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        self::$connection = (new Connection())
            ->getConnection();
    }

    /**
     * Create a new table in the database. 
     * 
     * @param string $tableName The name of the table.
     * @param array $columns An array of column names and their data types.
     * 
     * @return bool
     */
    public static function createTable(string $tableName, array $columns): bool
    {
        try {
            self::getInstance();

            $columns = implode(', ', array_values($columns));

            $statement = self::$connection->statement("CREATE TABLE IF NOT EXISTS {$tableName} ({$columns})");

            if ($statement === false) {
                throw new \PDOException("Failed to create table");
            }

            return true;
        } catch (\PDOException $e) {
            Log::debug($e, 'Failed to create table');

            return false;
        }
    }

    /**
     * Drop a table from the database.
     * 
     * @param string $tableName The name of the table.
     * 
     * @return bool
     */
    public static function dropTable(string $tableName): bool
    {
        try {
            self::getInstance();

            $statement = self::$connection->statement("DROP TABLE IF EXISTS {$tableName}");

            if ($statement === false) {
                throw new \PDOException("Failed to drop table");
            }

            return true;
        } catch (\PDOException $e) {
            Log::debug($e, 'Failed to drop table');

            return false;
        }
    }

    /**
     * Get the instance of the SchemaForge.
     * 
     * @return SchemaForge
     */
    private static function getInstance(): SchemaForgeInterface
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
