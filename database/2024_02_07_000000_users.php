<?php

use Lib\Database\ColumnDefinition;
use Lib\Database\Contracts\Schema;
use Lib\Database\SchemaForge;

return new class implements Schema
{
    public function up(ColumnDefinition $column): void
    {
        SchemaForge::createTable('users', [
            $column->id()->exec(),
            $column->string('name')->exec(),
            $column->string('email')->exec(),
            $column->string('password')->exec(),
            $column->timestamps()->exec(),
        ]);
    }

    public function down(): void
    {
        SchemaForge::dropTable('users');
    }
};
