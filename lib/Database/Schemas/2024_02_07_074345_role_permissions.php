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
                ->exec(),
            $column->unsignedBigInteger('role_id')
                ->exec(),
            $column->unsignedBigInteger('permission_id')
                ->exec(),
            $column->foreign('role_id')
                ->references('roles')
                ->exec(),
            $column->foreign('permission_id')
                ->references('permissions')
                ->onDelete(ColumnDefinition::CASCADE)
                ->onUpdate(ColumnDefinition::CASCADE)
                ->exec()
        ]);
    }

    public function down(): void
    {
        SchemaForge::dropTable('role_permissions');
    }
};
