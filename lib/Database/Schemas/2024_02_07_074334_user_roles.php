<?php

use Lib\Database\ColumnDefinition;
use Lib\Database\Contracts\Schema;
use Lib\Database\SchemaForge;

return new class implements Schema
{
    public function up(ColumnDefinition $column): void
    {
        SchemaForge::createTable('user_roles', [
            $column->id()
                ->exec(),
            $column->unsignedBigInteger('user_id')
                ->exec(),
            $column->unsignedBigInteger('role_id')
                ->exec(),
            $column->foreign('user_id')
                ->references('users')
                ->onDelete(ColumnDefinition::CASCADE)
                ->onUpdate(ColumnDefinition::CASCADE)
                ->exec(),
            $column->foreign('role_id')
                ->references('roles')
                ->onDelete(ColumnDefinition::CASCADE)
                ->onUpdate(ColumnDefinition::CASCADE)
                ->exec()
        ]);
    }

    public function down(): void
    {
        SchemaForge::dropTable('user_roles');
    }
};
