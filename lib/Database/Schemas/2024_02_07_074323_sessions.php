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
                ->generate(),
            $column->unsignedBigInteger('user_id')
                ->generate(),
            $column->string('ip_address', 45)
                ->notNullable()
                ->generate(),
            $column->string('user_agent')
                ->notNullable()
                ->generate(),
            $column->integer('last_activity')
                ->notNullable()
                ->generate(),
            $column->timestamps()
                ->generate(),
            $column->foreign('user_id')
                ->references('users')
                ->onDelete(ColumnDefinition::CASCADE)
                ->onUpdate(ColumnDefinition::CASCADE)
                ->generate(),
        ]);
    }

    public function down(): void
    {
        SchemaForge::dropTable('sessions');
    }
};
