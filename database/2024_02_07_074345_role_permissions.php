<?php

use Lib\Database\ColumnDefinition;
use Lib\Database\Contracts\Schema;
use Lib\Database\SchemaForge;

return new class implements Schema
{
    public function up(ColumnDefinition $column): void
    {
        SchemaForge::createTable('role_permissions', [
            $column->id()
                ->generate(),
            $column->unsignedBigInteger('role_id')
                ->generate(),
            $column->unsignedBigInteger('permission_id')
                ->generate(),
            $column->foreign('role_id')
                ->references('roles')
                ->generate(),
            $column->foreign('permission_id')
                ->references('permissions')
                ->onDelete(ColumnDefinition::CASCADE)
                ->onUpdate(ColumnDefinition::CASCADE)
                ->generate()
        ]);
    }

    public function down(): void
    {
        SchemaForge::dropTable('role_permissions');
    }
};
