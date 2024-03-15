<?php

use Lib\Database\ColumnDefinition;
use Lib\Database\Contracts\Schema;
use Lib\Database\SchemaForge;

return new class implements Schema
{
    public function up(ColumnDefinition $column): void
    {
        SchemaForge::createTable('roles', [
            $column->id()->generate(),
            $column->string('name')->notNullable()->generate(),
            $column->text('description')->nullable()->generate(),
            $column->timestamps()->generate(),
        ]);
    }

    public function down(): void
    {
        SchemaForge::dropTable('roles');
    }
};
