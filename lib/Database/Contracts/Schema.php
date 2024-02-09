<?php

namespace Lib\Database\Contracts;

use Lib\Database\ColumnDefinition;

interface Schema
{
    public function up(ColumnDefinition $column): void;

    public function down(): void;
}
