<?php

use Lib\Database\ColumnDefinition;
use Lib\Database\Contracts\Schema;
use Lib\Database\SchemaForge;

return new class implements Schema
{
    public function up(ColumnDefinition $column): void
    {
        SchemaForge::createTable('personal_access_tokens', [
            $column->id()
                ->generate(),
            $column->unsignedBigInteger('user_id')
                ->generate(),
            $column->string('name')
                ->nullable()
                ->generate(),
            $column->string('token')
                ->notNullable()
                ->generate(),
            $column->timestamp('last_used_at')
                ->nullable()
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
        SchemaForge::dropTable('personal_access_tokens');
    }
};
