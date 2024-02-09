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
                ->exec(),
            $column->unsignedBigInteger('user_id')
                ->exec(),
            $column->string('name')
                ->nullable()
                ->exec(),
            $column->string('token')
                ->notNullable()
                ->exec(),
            $column->timestamp('last_used_at')
                ->nullable()
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
        SchemaForge::dropTable('personal_access_tokens');
    }
};
