<?php

use Lib\Database\ColumnDefinition;
use Lib\Database\Contracts\Schema;
use Lib\Database\SchemaForge;

return new class implements Schema
{
    public function up(ColumnDefinition $column): void
    {
        SchemaForge::createTable('sessions', [
            $column->id()
                ->exec(),
            $column->unsignedBigInteger('user_id')
                ->exec(),
            $column->string('ip_address', 45)
                ->notNullable()
                ->exec(),
            $column->string('user_agent')
                ->notNullable()
                ->exec(),
            $column->integer('last_activity')
                ->notNullable()
                ->exec(),
            $column->timestamps()
                ->exec(),
            $column->foreign('user_id')
                ->references('users')
                ->onDelete(ColumnDefinition::CASCADE)
                ->onUpdate(ColumnDefinition::CASCADE)
                ->exec(),
        ]);
    }

    public function down(): void
    {
        SchemaForge::dropTable('sessions');
    }
};
