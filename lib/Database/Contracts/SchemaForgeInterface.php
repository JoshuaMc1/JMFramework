<?php

namespace Lib\Database\Contracts;

interface SchemaForgeInterface
{
    public static function createTable(string $tableName, array $columns): bool;
    public static function dropTable(string $tableName): bool;
}
