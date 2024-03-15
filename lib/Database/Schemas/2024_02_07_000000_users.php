<?php

use Lib\Database\ColumnDefinition;
use Lib\Database\Contracts\Schema;
use Lib\Database\SchemaForge;

return new class implements Schema
{
    public function up(ColumnDefinition $column): void
    {
        SchemaForge::createTable('users', [
            $column->id()->generate(),
            $column->string('name')->generate(),
            $column->string('email')->generate(),
            $column->string('password')->generate(),
            $column->timestamps()->generate(),
        ]);
    }

    public function down(): void
    {
        SchemaForge::dropTable('users');
    }
};
