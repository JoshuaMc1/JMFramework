<?php

use Lib\Database\ColumnDefinition;
use Lib\Database\Contracts\Schema;
use Lib\Database\SchemaForge;

return new class implements Schema
{
    public function up(ColumnDefinition $column): void
    {
        SchemaForge::createTable('roles', [
            $column->id()->exec(),
            $column->string('name')->notNullable()->exec(),
            $column->text('description')->nullable()->exec(),
            $column->timestamps()->exec(),
        ]);
    }

    public function down(): void
    {
        SchemaForge::dropTable('roles');
    }
};
